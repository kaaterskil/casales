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
 * @version     SVN $Id: MarketingListController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Controller\AbstractApplicationController;
use Application\Form\AccountFieldset;
use Application\Form\AccountForm;
use Application\Form\ContactFieldset;
use Application\Form\ContactForm;
use Application\Form\LeadFieldset;
use Application\Form\LeadForm;
use Application\Form\MarketingListFieldset;
use Application\Form\MarketingListForm;
use Application\Model\Account;
use Application\Model\Contact;
use Application\Model\Lead;
use Application\Model\ListStatus;
use Application\Model\MarketingList;
use Application\Model\MemberType;
use Application\Service\AddListMembersListRequest;
use Application\Service\AddListMembersListResponse;
use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\FindByCriteria;
use Application\Service\RemoveMemberListRequest;
use Application\Service\RemoveMemberListResponse;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\TargetCreateMarketingList;
use Application\Service\TargetDeleteMarketingList;
use Application\Service\TargetRetrieveMarketingList;
use Application\Service\TargetRetrieveMultipleAccountByPost;
use Application\Service\TargetRetrieveMultipleContactByPost;
use Application\Service\TargetRetrieveMultipleLeadByPost;
use Application\Service\TargetRetrieveMultipleMarketingList;
use Application\Service\TargetUpdateMarketingList;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

/**
 * Marketing List action controller
 *
 * @package Application\Controller
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN: $Rev: 13 $
 */
class MarketingListController extends AbstractApplicationController {
	const MSG_LIST_LOCKED = 'The list is locked and cannot be modified.';

	/**
	 * Retrieves a collection of lists
	 *
	 * @return \Zend\View\Model\ViewModel
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		/* @var $response RetrieveMultipleResponse */
		$owner = $this->zfcUserAuthentication()->getIdentity();
		$service = $this->getService();
		
		// Create receiver and command objects
		$criteria = new FindByCriteria();
		$criteria->setCriteria( array(
			'owner' => $owner
		) );
		$criteria->setOrderBy( array(
			'name' => 'asc'
		) );
		$target = new TargetRetrieveMultipleMarketingList();
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
		
		$view = new ViewModel( array(
			'pageTitle' => 'Marketing List',
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/marketingList/index' );
		return $view;
	}

	/**
	 * Creates a list
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createAction() {
		/* @var $request Request */
		/* @var $success CreateResponse */
		$service = $this->getService();
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Create the form and bind an empty entity to it
		$form = new MarketingListForm( $service->getEntityManager() );
		$entity = new MarketingList();
		$form->bind( $entity );
		
		$this->initializeFormValues( $form );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the campaign and return
				$target = new TargetCreateMarketingList();
				$target->setEntity( $entity );
				$create = new CreateRequest();
				$create->setTarget( $target );
				
				$success = $service->execute( $create );
				if ($success->getResult()) {
					$isRedirect = $request->getPost( MarketingListForm::SUBMITCLOSE, false );
					if ($isRedirect) {
						return $this->redirect()->toRoute( 'marketingList', array(
							'action' => 'index'
						) );
					} else {
						return $this->redirect()->toRoute( 'marketingList', array(
							'action' => 'edit',
							'id' => $success->getId()
						) );
					}
				} else {
					$statusMessage = sprintf( self::MSG_ERROR_CREATE, $success->getMessage() );
				}
			} else {
				$statusMessage = sprintf( self::MSG_INVALID_CREATE_FORM, $form->getMessages() );
			}
		}
		
		$view = new ViewModel( array(
			'form' => $form,
			'pageTitle' => 'New Marketing List',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/marketingList/create' );
		return $view;
	}

	/**
	 * Updates a list
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		/* @var $entity MarketingList */
		/* @var $request Request */
		/* @var $response RetrieveResponse */
		
		$service = $this->getService();
		
		// Fetch and test parameters
		$front = $this->params( 'front', 'tab-1' );
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'marketingList', array(
				'action' => 'index'
			) );
		}
		
		// Create the form and bind the specified entity to it
		$form = new MarketingListForm( $service->getEntityManager() );
		$entity = $this->fetchMarketingList( $id );
		$form->bind( $entity );
		
		// Set form values
		$this->setFormValues( $form, $entity );
		$statusMessage = sprintf( self::MSG_STATUS, $entity->getStatus() );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the entity and return
				$target = new TargetUpdateMarketingList();
				$target->setEntity( $entity );
				$update = new UpdateRequest();
				$update->setTarget( $target );
				
				$success = $service->execute( $update );
				if ($success->getResult()) {
					$isRedirect = $request->getPost( MarketingListForm::SUBMITCLOSE, false );
					if ($isRedirect) {
						return $this->redirect()->toRoute( 'marketingList', array(
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
		
		$listMembers = $entity->getListMembers();
		$queryForm = null;
		switch ($entity->getMemberType()) {
			case MemberType::ACCOUNT :
				$queryForm = new AccountForm( $service->getEntityManager() );
				$queryForm->bind( new Account() );
				break;
			case MemberType::CONTACT :
				$queryForm = new ContactForm( $service->getEntityManager() );
				$queryForm->bind( new Contact() );
				break;
			case MemberType::LEAD :
				$queryForm = new LeadForm( $service->getEntityManager() );
				$queryForm->bind( new Lead() );
				break;
		}
		
		$view = new ViewModel( array(
			'form' => $form,
			'front' => $front,
			'id' => $id,
			'listMembers' => $listMembers,
			'memberType' => $entity->getMemberType(),
			'pageTitle' => $entity->getName(),
			'queryForm' => $queryForm,
			'recordSet' => array(),
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/marketingList/edit' );
		return $view;
	}

	/**
	 * Deletes a list
	 *
	 * @return \Zend\Stdlib\ResponseInterface>
	 */
	public function deleteAction() {
		/* @var $success DeleteResponse */
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'marketingList', array(
				'action' => 'index'
			) );
		}
		
		// Create the receiver and concrete command
		$target = new TargetDeleteMarketingList();
		$target->setId( $id );
		$delete = new DeleteRequest();
		$delete->setTarget( $target );
		
		// Execute the deletion and return
		$service = $this->getService();
		$success = $service->execute( $delete );
		if ($success->getResult()) {
			$statusMessage = self::MSG_DELETE_SUCCESS;
		} else {
			$statusMessage = sprintf( self::MSG_ERROR_DELETE, $success->getMessage() );
		}
		return $this->redirect()->toRoute( 'marketingList', array(
			'action' => 'index'
		) );
	}

	/**
	 * Retrieves a collection of marketing list members
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function memberIndexAction() {
		/* @var $response RetrieveMultipleResponse */
		/* @var $success1 AddListMembersListResponse */
		/* @var $success2 RemoveMemberListResponse */
		
		$service = $this->getService();
		$statusMessage = 'No records found.';
		$recordSet = array();
		$front = 'tab-1';
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$type = $this->params( 'type', null );
		if (empty( $id ) || empty( $type )) {
			return $this->redirect()->toRoute( 'marketingList', array(
				'action' => 'index'
			) );
		}
		
		// Create the marketing list form and bind the specified list to it
		$mlf = new MarketingListForm( $service->getEntityManager() );
		$ml = $this->fetchMarketingList( $id );
		$mlf->bind( $ml );
		$this->setFormValues( $mlf, $ml );
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			if ($type == 'Query') {
				// Create receiver and command for querying accounts
				// with the specified parameters
				switch ($ml->getMemberType()) {
					case MemberType::ACCOUNT :
						$target = new TargetRetrieveMultipleAccountByPost();
						$target->setParams( $request->getPost( AccountFieldset::FIELDSETNAME ) );
						break;
					case MemberType::CONTACT :
						$target = new TargetRetrieveMultipleContactByPost();
						$target->setParams( $request->getPost( ContactFieldset::FIELDSETNAME ) );
						break;
					case MemberType::LEAD :
						$target = new TargetRetrieveMultipleLeadByPost();
						$target->setParams( $request->getPost( LeadFieldset::FIELDSETNAME ) );
						break;
				}
				$retrieve = new RetrieveMultipleRequest();
				$retrieve->setTarget( $target );
				
				$start = microtime( true );
				$response = $service->execute( $retrieve );
				$recordSet = $ml->removeMatchedCandidates( $response->getRecordSet() );
				$end = microtime( true );
				
				$elapsedTime = $end - $start;
				$numRecords = count( $recordSet );
				$statusMessage = $numRecords . ' records retrieved in ' . $elapsedTime . ' seconds';
				$front = 'tab-3';
			} elseif ($type == 'Add') {
				$front = 'tab-3';
				// Add the specified members to the marketing list
				if ($ml->getLockStatus() == false) {
					$selectedItems = $request->getPost( 'selected_fld' );
					
					$request = new AddListMembersListRequest();
					$request->setEntity( $ml );
					$request->setMemberIds( $selectedItems );
					
					$success1 = $service->execute( $request );
					if ($success1->getResult()) {
						$this->redirect()->toRoute( 'marketingList', array(
							'action' => 'edit',
							'id' => $id,
							'front' => $front
						) );
					}
					$statusMessage = $success1->getMessage();
				} else {
					$statusMessage = self::MSG_LIST_LOCKED;
				}
			} elseif ($type == 'Remove') {
				$front = 'tab-2';
				// Remove the specified members from the marketing list
				if ($ml->getLockStatus() == false) {
					$selectedItems = $request->getPost( 'selected_fld' );
					
					$request = new RemoveMemberListRequest();
					$request->setEntity( $ml );
					$request->setMemberIds( $selectedItems );
					
					$success2 = $service->execute( $request );
					if ($success2->getResult()) {
						$this->redirect()->toRoute( 'marketingList', array(
							'action' => 'edit',
							'id' => $id,
							'front' => $front
						) );
					}
					$statusMessage = $success2->getMessage();
				} else {
					$statusMessage = self::MSG_LIST_LOCKED;
				}
			}
		}
		
		$listMembers = $ml->getListMembers();
		$queryForm = null;
		switch ($ml->getMemberType()) {
			case MemberType::ACCOUNT :
				$queryForm = new AccountForm( $service->getEntityManager() );
				$queryForm->bind( new Account() );
				break;
			case MemberType::CONTACT :
				$queryForm = new ContactForm( $service->getEntityManager() );
				$queryForm->bind( new Contact() );
				break;
			case MemberType::LEAD :
				$queryForm = new LeadForm( $service->getEntityManager() );
				$queryForm->bind( new Lead() );
				break;
		}
		
		$view = new ViewModel( array(
			'form' => $mlf,
			'front' => $front,
			'id' => $id,
			'listMembers' => $listMembers,
			'memberType' => $ml->getMemberType(),
			'pageTitle' => $ml->getName(),
			'queryForm' => $queryForm,
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/marketingList/edit' );
		return $view;
	}

	/**
	 * Copies the members from the source list to the target list wihtout creating duplicates
	 *
	 * @return void
	 */
	public function copyMembersAction() {
		throw new \InvalidArgumentException( 'copyMembersAction() method not yet implemented.' );
	}

	/**
	 * Retrieves the specified marketing list
	 *
	 * @param int $id
	 * @return MarketingList
	 */
	private function fetchMarketingList($id) {
		$target = new TargetRetrieveMarketingList();
		$target->setId( (int) $id );
		
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget( $target );
		
		$service = $this->getService();
		$response = $service->execute( $retrieve );
		return $response->getEntity();
	}

	/**
	 * Sets default form values for a new marketing list
	 *
	 * @param MarketingListForm $form
	 */
	private function initializeformValues(MarketingListForm $form) {
		/* @var $lfs MarketingListFieldset */
		/* @var $owner User */
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		$lfs = $form->get( MarketingListFieldset::FIELDSETNAME );
		$lfs->get( MarketingListFieldset::OWNER )->setValue( $owner->getId() );
		$lfs->get( MarketingListFieldset::STATUS )->setValue( ListStatus::ACTIVE );
	}

	/**
	 * Sets nonstandard values for an existing marketing list
	 *
	 * @param MarketingListForm $form
	 * @param MarketingList $entity
	 */
	private function setFormValues(MarketingListForm $form, MarketingList $entity) {
		$form->get( MarketingListForm::SUBMIT )->setValue( 'Save' );
	}
}
?>