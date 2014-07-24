<?php

/**
 * Casales Library
 * PHP version 5.4
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
 * KAATERSKIL MANAGEMENT, LLC BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Casales
 * @package Application
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN $Id: Module.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Mail\Storage\Imap;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

/**
 * Provides initialization functions for the module.
 *
 * @package		Application
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Module.php 13 2013-08-05 22:53:55Z  $
 */
class Module {

	public function onBootstrap(MvcEvent $e) {
		/* @var $eventManager EventManagerInterface */
		$eventManager = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach( $eventManager );
		
		// Added test for authentication
		$eventManager->attach( MvcEvent::EVENT_DISPATCH, array(
			$this,
			'checkAuth'
		) );
	}

	/**
     * Tests if the user is authenticated. This method will redirect any unauthenticated
     * user to the login page.
     *
     * @param MvcEvent $e
     * @return unknown|boolean
     */
	public function checkAuth(MvcEvent $e) {
		$sm = $e->getApplication()->getServiceManager();
		$auth = $sm->get( 'zfcuser_auth_service' );
		$c = $e->getTarget();
		
		$isExempt = $c instanceof \ZfcUser\Controller\UserController;
		if (!$isExempt && !$auth->hasIdentity()) {
			$url = $e->getRouter()->assemble( array(), array(
				'name' => 'zfcuser/login'
			) );
			$response = $e->getResponse();
			$response->getHeaders()->addHeaderLine( 'Location', $url );
			$response->setStatusCode( 302 );
			return $response;
		}
		return true;
	}

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php'
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
				)
			)
		);
	}

	public function getServiceConfig() {
		return array(
			'invokables' => array(),
			
			'factories' => array(
				'mail.transport' => function(ServiceManager $sm) {
					$config = $sm->get('Config');
					$transport = new Smtp();
					$options = new SmtpOptions($config['mail']['transport']['options']);
					$transport->setOptions($options);
					return $transport;
				},
				'mail.inbox' => function(ServiceManager $sm) {
					$config = $sm->get('Config');
					$params = $config['mail']['inbox']['options'];
					return new Imap($params);
				}
			),
		);
	}
}
