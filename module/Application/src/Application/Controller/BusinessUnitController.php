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
 * @version     SVN $Id: BusinessUnitController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Controller\AbstractApplicationController;
use Application\Model\BusinessUnit;
use Application\Form\BusinessUnitFieldset;
use Application\Form\BusinessUnitForm;
use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\FindByCriteria;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\TargetCreateBusinessUnit;
use Application\Service\TargetDeleteBusinessUnit;
use Application\Service\TargetRetrieveBusinessUnit;
use Application\Service\TargetRetrieveMultipleBusinessUnit;
use Application\Service\TargetUpdateBusinessUnit;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

/**
 * Business Unit action controller
 *
 * @package Application\Controller
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN: $Rev: 13 $
 */
class BusinessUnitController extends AbstractApplicationController {

	/**
	 * The main listing page
	 *
	 * @return \Zend\View\Model\ViewModel
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		/* @var $response RetrieveMultipleResponse */
		$service = $this->getService();
		
		// Create receiver and command objects
		$criteria = new FindByCriteria();
		$criteria->setOrderBy( array(
			'name' => 'asc'
		) );
		$target = new TargetRetrieveMultipleBusinessUnit();
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
			'pageTitle' => 'Business Units',
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/businessUnit/index' );
		return $view;
	}

	/**
	 * Presents a form for the creation of a new entity
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createAction() {
		/* @var $request \Zend\Http\Request */
		/* @var $success CreateResponse */
		
		$service = $this->getService();
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Create the form, and bind an empty entity to it
		$form = new BusinessUnitForm( $service->getEntityManager() );
		$businessUnit = new BusinessUnit();
		$form->bind( $businessUnit );
		
		// Process a request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the entity and return
				$target = new TargetCreateBusinessUnit();
				$target->setEntity( $businessUnit );
				$create = new CreateRequest();
				$create->setTarget( $target );
				
				$success = $service->execute( $create );
				if ($success->getResult()) {
					return $this->redirect()->toRoute( 'businessUnit', array(
						'action' => 'index'
					) );
				}
				$statusMessage = sprintf( self::MSG_ERROR_CREATE, $success->getMessage() );
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		
		$view = new ViewModel( array(
			'form' => $form,
			'pageTitle' => 'New Business Unit',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/businessUnit/create' );
		return $view;
	}

	public function editAction() {
		/* @var $request Request */
		/* @var $response RetrieveResponse */
		/* @var $success UpdateResponse */
		/* @var $bu BusinessUnit */
		
		$service = $this->getService();
		
		// Fetch and test the parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'businessUnit', array(
				'action' => 'create'
			) );
		}
		
		$form = new BusinessUnitForm( $service->getEntityManager() );
		
		// Fetch the given entity and bind it to the form
		$target = new TargetRetrieveBusinessUnit();
		$target->setId( $id );
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget( $target );
		$response = $service->execute( $retrieve );
		$bu = $response->getEntity();
		$form->bind( $bu );
		
		$this->setFormValues( $form, $bu );
		$statusMessage = '';
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the entity and return
				$target = new TargetUpdateBusinessUnit();
				$target->setEntity( $bu );
				$update = new UpdateRequest();
				$update->setTarget( $target );
				
				$success = $service->execute( $update );
				if ($success->getResult()) {
					return $this->redirect()->toRoute( 'businessUnit', array(
						'action' => 'index'
					) );
				}
				$statusMessage = sprintf( self::MSG_ERROR_UPDATE, $success->getMessage() );
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM;
			}
		}
		
		$auditItems = $bu->getAuditItems()->getValues();
		$users = $bu->getUsers()->getValues();
		
		$view = new ViewModel( array(
			'auditItems' => $auditItems,
			'form' => $form,
			'id' => $id,
			'pageTitle' => $bu->getName(),
			'statusMessage' => $statusMessage,
			'users' => $users
		) );
		$view->setTemplate( 'application/businessUnit/edit' );
		return $view;
	}

	public function deleteAction() {
		/* @var $success DeleteResponse */
		
		// Fetch and test the parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'businessUnit', array(
				'action' => 'index'
			) );
		}
		
		// Create the receiver and command objects
		$target = new TargetDeleteBusinessUnit();
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
		return $this->redirect()->toRoute( 'businessUnit', array(
			'action' => 'index'
		) );
	}

	private function setFormValues(BusinessUnitForm $form, BusinessUnit $bu) {
		// noop
	}
}
?>