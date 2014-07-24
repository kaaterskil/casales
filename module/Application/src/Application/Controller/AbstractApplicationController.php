<?php

/**
 * Casales Library
 *
 * PHP version 5.4
 *
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
 * @category    Casales
 * @package     Application\Controller
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: AbstractApplicationController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Service\FlushListener;
use Application\Service\Service;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\Events;
use Zend\EventManager\EventManagerInterface;

/**
 * Abstract action controller that provides additional root level functions
 *
 * @package		Application\Controller
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Revision: 13 $
 * @abstract
 */
abstract class AbstractApplicationController extends AbstractActionController {
	const MSG_ERROR_CREATE = 'The record could not be created: %s';
	const MSG_ERROR_UPDATE = 'The record could not be updated: %s';
	const MSG_ERROR_DELETE = 'The record could not be deleted: %s';
	const MSG_INVALID_CREATE_FORM = 'The record has invalid form values and could not be created.';
	const MSG_INVALID_UPDATE_FORM = 'The record has invalid form values and could not be updated.';
	const MSG_NEW_RECORD = 'Status: New record.';
	const MSG_STATISTICS = '%s records retrieved in %s seconds';
	const MSG_STATUS = 'Status: %s';
	const MSG_CREATE_SUCCESS = 'The record was successfully created. Status: %s';
	const MSG_UPDATE_SUCCESS = 'The record was successfully updated. Status: %s';
	const MSG_DELETE_SUCCESS = 'The record was successfully deleted. Status: %s';

	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var Service
	 */
	private $service;

	/**
	 * @var Zend\View\Model\ViewModel
	 */
	private $view;
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	public function getEntityManager() {
		if ($this->em == null) {
			$em = $this->getServiceLocator()->get( '\Doctrine\ORM\EntityManager' );
			$this->setEntityManager( $em );
		}
		return $this->em;
	}

	/**
	 * @param EntityManager $em
	 */
	public function setEntityManager(EntityManager $em) {
		// Register audit listener
		$em->getEventManager()->addEventListener( array(
			Events::onFlush
		), new \Application\Service\FlushListener( $this->zfcUserAuthentication()
			->getIdentity() ) );
		$this->em = $em;
	}

	/**
	 * @return \Application\Service\Service
	 */
	public function getService() {
		if ($this->service == null) {
			$service = new Service( $this->getEntityManager() );
			$service->setUser( $this->zfcUserAuthentication()
				->getIdentity() );
			$service->setServiceManager( $this->getServiceLocator() );
			$this->setService( $service );
		}
		return $this->service;
	}

	/**
	 * @param Service $service
	 */
	public function setService(Service $service) {
		$service->setUser( $this->zfcUserAuthentication()
			->getIdentity() );
		$service->setServiceManager( $this->getServiceLocator() );
		$this->service = $service;
	}

	/**
	 * @return Zend\View\Model\ViewModel
	 */
	public function getView() {
		if ($this->view == null) {
			$this->view = new ViewModel();
		}
		return $this->view;
	}

	/**
	 * @param ViewModel $view
	 */
	public function setView(ViewModel $view) {
		$this->view = $view;
	}
}
?>