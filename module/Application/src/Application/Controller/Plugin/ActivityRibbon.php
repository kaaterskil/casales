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
 * @package     Application\Controller\Plugin
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: ActivityRibbon.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\Navigation\Navigation;

/**
 * Plugin that generates a navigation bar to create activities
 *
 * @package Application\Controller\Plugin
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN: $Rev: 13 $
 */
class ActivityRibbon extends AbstractPlugin {

	/**
	 * Returns a menu for adding Activities
	 *
	 * @param string $route
	 * 		The page route
	 * @param int $id
	 * 		The specified entity id
	 * @return Navigation
	 */
	public function __invoke($route, $id) {
		$id = (int) $id;
		
		$config = array(
			array(
				'label' => 'Add Appointment',
				'route' => 'activity',
				'pages' => array(
					array(
						'label' => 'Meeting',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'MeetingAppointment',
							'entityId' => $id,
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'Breakfast',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => $id,
							'type' => 'BreakfastAppointment',
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'Lunch',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'LunchAppointment',
							'entityId' => $id,
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'Dinner',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'DinnerAppointment',
							'entityId' => $id,
							'entityRoute' => $route
						)
					)
				)
			),
			array(
				'label' => 'Add Interaction',
				'route' => 'activity',
				'pages' => array(
					array(
						'label' => 'Email',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'EmailInteraction',
							'entityId' => $id,
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'Fax',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'FaxInteraction',
							'entityId' => $id,
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'Letter',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'LetterInteraction',
							'entityId' => $id,
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'Telephone Call',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'TelephoneInteraction',
							'entityId' => $id,
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'Visit',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => $id,
							'type' => 'VisitInteraction',
							'entityRoute' => $route
						)
					)
				)
			),
			array(
				'label' => 'Add Note',
				'route' => 'activity',
				'pages' => array(
					array(
						'label' => 'Lead Note',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'AccountNote',
							'entityId' => $id,
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'User Note',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'UserNote',
							'entityId' => $id,
							'entityRoute' => $route
						)
					)
				)
			),
			array(
				'label' => 'Add Task',
				'route' => 'activity',
				'pages' => array(
					array(
						'label' => 'Callback',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'CallbackTask',
							'entityId' => $id,
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'Followup',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'FollowUpTask',
							'entityId' => $id,
							'entityRoute' => $route
						)
					),
					array(
						'label' => 'Other',
						'route' => 'activity',
						'params' => array(
							'action' => 'create',
							'id' => 0,
							'type' => 'OtherTask',
							'entityId' => $id,
							'entityRoute' => $route
						)
					)
				)
			)
		);
		
		$f = new ConstructedNavigationFactory( $config );
		$navigation = $f->createService( $this->getController()
			->getServiceLocator() );
		return $navigation;
	}
}
?>