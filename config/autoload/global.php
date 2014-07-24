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
 * @version SVN $Id: global.php 13 2013-08-05 22:53:55Z  $
 */

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
	'db' => array(
		'driver'         => 'Pdo',
		'dsn'            => 'mysql:dbname=casales2;host=localhost',
		'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''),
	),
	
	'navigation' => array(
		'default' => array(
			array(
				'label' => 'Home',
				'route' => 'home',
			),
			array(
				'label' => 'Workplace',
				'route' => 'home',
				'pages' => array(
					array(
						'label' => 'Mail',
						'route' => 'mail',
					),
					array(
						'label' => 'Calendar',
						'route' => 'calendar',
					),
					array(
						'label' => 'Activities',
						'route' => 'activity',
					),
					array(
						'label' => 'Closed Activities',
						'route' => 'closedActivity',
					),
				),
			),
			array(
				'label' => 'Marketing',
				'route' => 'home',
				'pages' => array(
					array(
						'label' => 'Accounts',
						'route' => 'account',
					),
					array(
						'label' => 'Contacts',
						'route' => 'contact',
					),
					array(
						'label' => 'Campaigns',
						'route' => 'campaign',
					),
					array(
						'label' => 'Marketing Lists',
						'route' => 'marketingList',
					),
					array(
						'label' => 'Sales Literature',
						'route' => 'salesLiterature',
					),
				),
			),
			array(
				'label' => 'Sales',
				'route' => 'home',
				'pages' => array(
					array(
						'label' => 'Leads',
						'route' => 'lead',
					),
					array(
						'label' => 'Open Opportunities',
						'route' => 'opportunity',
					),
					array(
						'label' => 'Closed Opportunities',
						'route' => 'closedOpportunity',
					),
				),
			),
			array(
				'label' => 'Configuration',
				'route' => 'config',
				'pages' => array(
					array(
						'label' => 'Business Units',
						'route' => 'businessUnit',
					),
					array(
						'label' => 'Users',
						'route' => 'user',
					),
					array(
						'label' => 'Account Groups',
						'route' => 'group',
					),
					array(
						'label' => 'Opportunity Stages',
						'route' => 'config',
						'params' => array(
							'slug' => 'stage',
						),
					),
					array(
						'label' => 'Regions',
						'route' => 'config',
						'params' => array(
							'slug' => 'region',
						),
					),
				),
			),
			array(
				'label' => 'Logout',
				'route' => 'zfcuser/logout',
			),
		),
	),
	'service_manager' => array(
		'factories' => array(
			'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
			'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
		),
	),
);
