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
 * @version SVN $Id: module.config.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application;

return array(
	'router' => array(
		'routes' => array(
			'public' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route' => '/',
					'defaults' => array(
						'controller' => 'Application\Controller\Public',
						'action' => 'index'
					)
				)
			),
			'home' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/home[/][:action]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Index',
						'action' => 'index'
					)
				)
			),
			'account' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/account[/][:action][/:id][/:type]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'type' => '[A-Z][a-zA-Z0-9]*'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Account',
						'action' => 'index'
					)
				)
			),
			'activity' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/activity[/][:action][/:id][/:type][/:entityId][/:entityRoute]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'type' => '[A-Z][a-zA-Z0-9]*',
						'entityId' => '[0-9]*',
						'entityRoute' => '[a-zA-Z0-9]*'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Activity',
						'action' => 'index'
					)
				)
			),
			'ajax' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/ajax[/][:action][/:param1][/:param2][/:param3]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'param1' => '[a-zA-Z0-9]*',
						'param2' => '[a-zA-Z0-9]*',
						'param3' => '[a-zA-Z0-9]*'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Ajax',
						'action' => 'index'
					)
				)
			),
			'businessUnit' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/businessUnit[/][:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\BusinessUnit',
						'action' => 'index'
					)
				)
			),
			'calendar' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/activity/calendar',
					'defaults' => array(
						'controller' => 'Application\Controller\Activity',
						'action' => 'calendar'
					)
				)
			),
			'campaign' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/campaign[/][:action][/:id][/:type][/:front]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'type' => '[A-Z][a-zA-Z]+',
						'front' => 'tab-[0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Campaign',
						'action' => 'index'
					)
				)
			),
			'closedActivity' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/closedActivity[/][:action][/:id][/:type]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'type' => '[A-Z][a-zA-Z0-9]*'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\ClosedActivity',
						'action' => 'index'
					)
				)
			),
			'closedOpportunity' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/opportunity/closedIndex',
					'defaults' => array(
						'controller' => 'Application\Controller\ClosedActivity',
						'action' => 'closedIndex'
					)
				)
			),
			'config' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/config/[:slug][/][:action][/:id]',
					'constraints' => array(
						'slug' => '[a-zA-Z][a-zA-z0-9]*',
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Config',
						'action' => 'index'
					)
				)
			),
			'contact' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/contact[/][:action][/:id][/:type][/:param4]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'type' => '[A-Z][a-zA-Z]+',
						'param4' => '[0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Contact',
						'action' => 'index'
					)
				)
			),
			'group' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/group[/][:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Group',
						'action' => 'index'
					)
				)
			),
			'lead' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/lead[/][:action][/:id][/:type][/:param4]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'type' => '[A-Z][a-zA-Z]+',
						'param4' => '[a-zA-Z0-9]*'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Lead',
						'action' => 'index'
					)
				)
			),
			'mail' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/mail[/][:action][/:id][/:mailbox]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'mailbox' => '[a-zA-Z]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Mail',
						'action' => 'index'
					)
				)
			),
			'marketingList' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/marketingList[/][:action][/:id][/:type][/:front]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'type' => '[A-Z][a-zA-Z]+',
						'front' => 'tab-[0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\MarketingList',
						'action' => 'index'
					)
				)
			),
			'opportunity' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/opportunity[/][:action][/:id][/:type]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'type' => '[A-Z][a-zA-Z0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\Opportunity',
						'action' => 'index'
					)
				)
			),
			'salesLiterature' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/salesLiterature[/][:action][/:id][/:itemId][/:front]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+',
						'itemId' => '[0-9]+',
						'front' => 'tab-[0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\SalesLiterature',
						'action' => 'index'
					)
				)
			),
			'user' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/user[/][:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9]*',
						'id' => '[0-9]+'
					),
					'defaults' => array(
						'controller' => 'Application\Controller\User',
						'action' => 'index'
					)
				)
			)
		)
	),
	'service_manager' => array(
		'abstract_factories' => array(
			'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
			'Zend\Log\LoggerAbstractServiceFactory'
		),
		'aliases' => array(
			'translator' => 'MvcTranslator'
		)
	),
	'translator' => array(
		'locale' => 'en_US',
		'translation_file_patterns' => array(
			array(
				'type' => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern' => '%s.mo'
			)
		)
	),
	'controllers' => array(
		'invokables' => array(
			'Application\Controller\Index' 				=> 'Application\Controller\IndexController',
			'Application\Controller\Account' 			=> 'Application\Controller\AccountController',
			'Application\Controller\Activity' 			=> 'Application\Controller\ActivityController',
			'Application\Controller\Ajax' 				=> 'Application\Controller\AjaxController',
			'Application\Controller\BusinessUnit' 		=> 'Application\Controller\BusinessUnitController',
			'Application\Controller\Campaign' 			=> 'Application\Controller\CampaignController',
			'Application\Controller\ClosedActivity' 	=> 'Application\Controller\ClosedActivityController',
			'Application\Controller\Config' 			=> 'Application\Controller\ConfigController',
			'Application\Controller\Contact' 			=> 'Application\Controller\ContactController',
			'Application\Controller\Group'				=> 'Application\Controller\GroupController',
			'Application\Controller\Lead'				=> 'Application\Controller\LeadController',
			'Application\Controller\Mail'				=> 'Application\Controller\MailController',
			'Application\Controller\MarketingList'		=> 'Application\Controller\MarketingListController',
			'Application\Controller\Opportunity'		=> 'Application\Controller\OpportunityController',
			'Application\Controller\Public'				=> 'Application\Controller\PublicController',
			'Application\Controller\SalesLiterature'	=> 'Application\Controller\SalesLiteratureController',
			'Application\Controller\User' 				=> 'Application\Controller\UserController'
		)
	),
	'controller_plugins' => array(
		'invokables' => array(
			'activityRibbon' => 'Application\Controller\Plugin\ActivityRibbon'
		)
	),
	'view_manager' => array(
		'display_not_found_reason' => true,
		'display_exceptions' => true,
		'doctype' => 'HTML5',
		'not_found_template' => 'error/404',
		'exception_template' => 'error/index',
		'template_map' => array(
			'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
			'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
			'error/404' => __DIR__ . '/../view/error/404.phtml',
			'error/index' => __DIR__ . '/../view/error/index.phtml'
		),
		'template_path_stack' => array(
			__DIR__ . '/../view'
		)
	),
	'doctrine' => array(
		'driver' => array(
			'casale_annotation_driver' => array(
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => array(
					__DIR__ . '/../src/' . __NAMESPACE__ . '/Model'
				)
			),
			'orm_default' => array(
				'drivers' => array(
					__NAMESPACE__ => 'casale_annotation_driver'
				)
			)
		)
	)
);
