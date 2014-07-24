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
 * @version     SVN $Id: ClosedActivityController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Form\ActivityForm;
use Application\Form\ActivityTypeForm;
use Application\Form\BaseActivityFieldset;
use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\ScheduledActivity;
use Application\Model\TrackedActivity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

/**
 * Close Activity action controller
 *
 * @package		Application\Controller
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: ClosedActivityController.php 13 2013-08-05 22:53:55Z  $
 */
class ClosedActivityController extends AbstractApplicationController {

	public function indexAction() {
		/* @var $em EntityManager */
		/* @var $q Query */
		
		$em = $this->getEntityManager();
		
		$dql = "select a
				from Application\Model\TrackedActivity a
				where a.state != 'Open'
				order by a.actualEnd desc, a.scheduledEnd desc";
		$q = $em->createQuery( $dql );
		$activities = $q->getResult();
		
		$view = new ViewModel( array (
			'activities' => $activities,
			'pageTitle' => 'Closed Activities'
		) );
		$view->setTemplate( 'application/activity/closedIndex' );
		return $view;
	}

	/**
	 * Edits/Views an existing Activity
	 *
	 * @throws \InvalidArgumentException
	 * 		Throws an exception if the id is not valid.
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		/* @var $em EntityManager */
		/* @var $request Request */
		/* @var $object AbstractActivity */

		$em = $this->getEntityManager();
		
		// Fetch the Activity class, create a Form and populate it with the appropriate
		// elements
		$type = $this->params( 'type', '' );
		if ($type == '') {
			throw new \InvalidArgumentException( 'No Activity Type specified.' );
		}
		$clazz = 'Application\Model\\' . $type;
		$form = new ActivityForm( $em, $clazz );
		
		// Fetch the specified Activity and bind it to the Form
		$id = $this->params( 'id' );
		if (!$id) {
			$this->redirect()->toRoute( 'closedActivity', array (
				'action' => 'index'
			) );
		}
		$object = $em->getRepository( $clazz )->find( $id );
		$form->bind( $object );
		
		// Process a POST request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Update the modification datetime
				$now = new \DateTime();
				$object->setLastUpdateDate( $now );
				
				// Persist the Activity
				$em->persist( $object );
				$em->flush();
				
				// Return to the index page
				$this->redirect()->toRoute( 'closedActivity', array (
					'action' => 'index'
				) );
			}
		}
		
		// Build the page title from the Activity class
		$words = preg_split( '/(?<=[a-z])(?=[A-Z])/x', $type );
		$title = implode( ' ', $words );
		
		$view = new ViewModel( array (
			'object' => $object,
			'id' => $id,
			'form' => $form,
			'type' => $type,
			'pageTitle' => 'Edit ' . $title
		) );
		$view->setTemplate( 'application/activity/edit' );
		return $view;
	}

	public function deleteAction() {
		/* @var $em EntityManager */
		/* @var $obj Activity */

		$em = $this->getEntityManager();
		
		$id = $this->params( 'id', 0 );
		if ($id) {
			$obj = $em->getRepository( 'Application\Model\AbstractActivity' )->find( $id );
			$em->remove( $obj );
			$em->flush();
		}
		return $this->redirect()->toRoute( 'closedActivity', array (
			'action' => 'index'
		) );
	}
}
?>