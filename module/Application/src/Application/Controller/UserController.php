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
 * @version     SVN $Id: UserController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Controller\AbstractApplicationController;
use Application\Model\User;
use Application\Form\UserFieldset;
use Application\Form\UserForm;
use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\FindByCriteria;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\TargetCreateUser;
use Application\Service\TargetDeleteUser;
use Application\Service\TargetRetrieveMultipleUser;
use Application\Service\TargetRetrieveUser;
use Application\Service\TargetUpdateUser;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

/**
 * User action controller
 *
 * @package Application\Controller
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN: $Rev: 13 $
 */
class UserController extends AbstractApplicationController {

	/**
	 * The main listing page
	 * @return \Zend\View\Model\ViewModel
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		/* @var $response RetrieveMultipleResponse */
		$service = $this->getService();
		
		// Create receiver and command objects
		$criteria = new FindByCriteria();
		$criteria->setOrderBy( array(
			'lastName' => 'asc'
		) );
		$target = new TargetRetrieveMultipleUser();
		$target->setCriteria( $criteria );
		$retrieve = new RetrieveMultipleRequest();
		$retrieve->setTarget( $target );
		
		// Fetch records
		$start = microtime( true );
		$response = $service->execute( $retrieve );
		$recordSet = $response->getRecordSet();
		$end = microtime( true );
		
		// Compute statistics
		$elapsedTime = $end - $start;
		$numRecords = count( $recordSet );
		$statusMessage = sprintf( self::MSG_STATISTICS, $numRecords, $elapsedTime );
		
		// Create and return the view
		$view = new ViewModel( array(
			'pageTitle' => 'Users',
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/user/index' );
		return $view;
	}

	public function createAction() {
		/* @var $request \Zend\Http\Request */
		/* @var $success CreateResponse */
		
		$service = $this->getService();
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Create the form, and bind an empty entity to it
		$form = new UserForm( $service->getEntityManager() );
		$user = new User();
		$form->bind( $user );
		
		// Process a request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the entity and return
				$target = new TargetCreateUser();
				$target->setEntity( $user );
				$create = new CreateRequest();
				$create->setTarget( $target );
				
				$success = $service->execute( $create );
				if ($success->getResult()) {
					$isRedirect = $request->getPost( USerForm::SUBMITCLOSE, false );
					if ($isRedirect) {
						return $this->redirect()->toRoute( 'user', array(
							'action' => 'index'
						) );
					}
					$statusMessage = $success->getMessage();
				} else {
					$statusMessage = sprintf( self::MSG_ERROR_CREATE, $success->getMessage() );
				}
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		
		$view = new ViewModel( array(
			'form' => $form,
			'pageTitle' => 'New User',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/user/create' );
		return $view;
	}

	public function editAction() {
		/* @var $request Request */
		/* @var $response RetrieveResponse */
		/* @var $success UpdateResponse */
		/* @var $user User */
		
		$service = $this->getService();
		
		// Fetch and test the parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'user', array(
				'action' => 'create'
			) );
		}
		
		$form = new UserForm( $service->getEntityManager() );
		
		// Fetch the given entity and bind it to the form
		$target = new TargetRetrieveUser();
		$target->setId( $id );
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget( $target );
		$response = $service->execute( $retrieve );
		$user = $response->getEntity();
		$form->bind( $user );
		
		$this->setFormValues( $form, $user );
		
		$status = ($user->getIsDisabled() ? 'Disabled' : 'Active');
		$statusMessage = sprintf( self::MSG_STATUS, $status );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the entity and return
				$target = new TargetUpdateUser();
				$target->setEntity( $user );
				$update = new UpdateRequest();
				$update->setTarget( $target );
				
				$success = $service->execute( $update );
				if ($success->getResult()) {
					$isRedirect = $request->getPost( UserForm::SUBMITCLOSE, false );
					if ($isRedirect) {
						return $this->redirect()->toRoute( 'user', array(
							'action' => 'index'
						) );
					}
					$statusMessage = $success->getMessage();
				} else {
					$statusMessage = sprintf( self::MSG_ERROR_UPDATE, $success->getMessage() );
				}
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM;
			}
		}
		
		$auditItems = $user->getAuditItems()->getValues();
		
		$view = new ViewModel( array(
			'auditItems' => $auditItems,
			'form' => $form,
			'id' => $id,
			'pageTitle' => $user->getFullName(),
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/user/edit' );
		return $view;
	}

	public function deleteAction() {
		/* @var $success DeleteResponse */
		
		// Fetch and test the parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'user', array(
				'action' => 'index'
			) );
		}
		
		// Create the receiver and command objects
		$target = new TargetDeleteUser();
		$target->setId( $id );
		$delete = new DeleteRequest();
		$delete->setTarget( $target );
		
		$service = $this->getService();
		$success = $service->execute( $delete );
		
		if ($success->getResult()) {
			$statusMessage = self::MSG_DELETE_SUCCESS;
		} else {
			$statusMessage = sprintf( self::MSG_ERROR_DELETE, $success->getMessage() );
		}
		return $this->redirect()->toRoute( 'user', array(
			'action' => 'index'
		) );
	}

	/**
	 * @param UserForm $form
	 * @param User $user
	 */
	private function setFormValues(UserForm $form, User $user) {
		/* @var $ufs UserFieldset */
		$ufs = $form->get( UserFieldset::FIELDSETNAME );
		
		if ($user->getBusinessUnit()) {
			$ufs->get( UserFieldset::BUSINESSUNIT )->setValue( $user->getBusinessUnit()
				->getId() );
		}
	}
}
?>