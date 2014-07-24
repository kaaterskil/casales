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
 * @version     SVN $Id: CampaignController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Controller\AbstractApplicationController;
use Application\Form\ActivityForm;
use Application\Form\AppointmentFieldset;
use Application\Form\BaseActivityFieldset;
use Application\Form\CampaignActivityFieldset;
use Application\Form\CampaignFieldset;
use Application\Form\CampaignForm;
use Application\Form\FaxFieldset;
use Application\Form\InteractionFieldset;
use Application\Form\LetterFieldset;
use Application\Form\MarketingListFieldset;
use Application\Form\MarketingListForm;
use Application\Form\NoteFieldset;
use Application\Form\TaskFieldset;
use Application\Form\TelephoneInteractionFieldset;
use Application\Form\VisitFieldset;
use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;
use Application\Model\ActivityPriority;
use Application\Model\AppointmentStatus;
use Application\Model\Campaign;
use Application\Model\CampaignActivity;
use Application\Model\CampaignActivityStatus;
use Application\Model\CampaignActivityType;
use Application\Model\CampaignStatus;
use Application\Model\CampaignType;
use Application\Model\ChannelType;
use Application\Model\EmailInteraction;
use Application\Model\FaxInteraction;
use Application\Model\FaxStatus;
use Application\Model\LetterInteraction;
use Application\Model\LetterStatus;
use Application\Model\MarketingList;
use Application\Model\TaskStatus;
use Application\Model\TelephoneInteraction;
use Application\Model\TelephoneStatus;
use Application\Model\User;
use Application\Model\VisitInteraction;
use Application\Service\AddListsCampaignRequest;
use Application\Service\AddListsCampaignResponse;
use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\FindByCriteria;
use Application\Service\RemoveListsCampaignRequest;
use Application\Service\RemoveListsCampaignResponse;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\TargetCreateCampaign;
use Application\Service\TargetCreateActivity;
use Application\Service\TargetDeleteCampaign;
use Application\Service\TargetRetrieveCampaign;
use Application\Service\TargetRetrieveMultipleCampaign;
use Application\Service\TargetRetrieveMultipleMarketingListByPost;
use Application\Service\TargetUpdateCampaign;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;
use Doctrine\ORM\EntityManager;
use Zend\Form\Fieldset;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;
use Application\Model\CampaignResponse;
use Application\Form\CampaignResponseFieldset;
use Application\Model\ActivityStatus;
use Application\Model\OpportunityCloseStatus;

/**
 * Campaign action controller
 *
 * @package		Application\Controller
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: CampaignController.php 13 2013-08-05 22:53:55Z  $
 */
class CampaignController extends AbstractApplicationController {

	/**
	 * Retrieves a collection of campaigns.
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
			'proposedStart' => 'desc',
			'actualStart' => 'desc'
		) );
		$target = new TargetRetrieveMultipleCampaign();
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
			'pageTitle' => 'Campaigns',
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/campaign/index' );
		return $view;
	}

	/**
	 * Creates a campaign.
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createAction() {
		/* @var $request Request */
		/* @var $success CreateResponse */
		$service = $this->getService();
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Create the form and bind an empty entity to it
		$form = new CampaignForm( $service->getEntityManager() );
		$campaign = new Campaign();
		$form->bind( $campaign );
		
		$this->initializeFormValues( $form );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the campaign and return
				$target = new TargetCreateCampaign();
				$target->setEntity( $campaign );
				$create = new CreateRequest();
				$create->setTarget( $target );
				
				$success = $service->execute( $create );
				if ($success->getResult()) {
					$isRedirect = $request->getPost(CampaignForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute( 'campaign', array(
							'action' => 'index'
						) );
					} else {
						return $this->redirect()->toRoute( 'campaign', array(
							'action' => 'edit',
							'id' => $success->getId()
						) );
					}
				}
				$statusMessage = sprintf( self::MSG_ERROR_CREATE, $success->getMessage() );
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		
		$view = new ViewModel( array(
			'form' => $form,
			'pageTitle' => 'New Campaign',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/campaign/create' );
		return $view;
	}

	/**
	 * Updates a campaign.
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		/* @var $request Request */
		/* @var $campaign Campaign */
		
		$service = $this->getService();
		
		// Fetch and test parameters
		$front = $this->params( 'front', 'tab-1' );
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'campaign', array(
				'action' => 'index'
			) );
		}
		
		// Create the form and bind the specified entity to it
		$form = new CampaignForm( $service->getEntityManager() );
		$campaign = $this->fetchCampaign( $id );
		$form->bind( $campaign );
		
		// Set form values
		$this->setFormValues( $form, $campaign );
		$statusMessage = sprintf( self::MSG_STATUS, $campaign->getStatus() );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the entity and return
				$target = new TargetUpdateCampaign();
				$target->setEntity( $campaign );
				$update = new UpdateRequest();
				$update->setTarget( $target );
				
				$success = $service->execute( $update );
				if ($success->getResult()) {
					$isRedirect = $request->getPost(CampaignForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute( 'campaign', array(
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
		
		$queryForm = new MarketingListForm( $service->getEntityManager() );
		$queryForm->bind( new MarketingList() );
		
		// Set view parameters
		$auditItems = $campaign->getAuditItems()->getValues();
		$campaignResponses = $campaign->getCampaignResponses()->toArray();
		$closedActivities = $campaign->getClosedActivities();
		$closedCampaignActivities = $campaign->getClosedCampaignActivities();
		$marketingLists = $campaign->getListsAsArray();
		$navigation = $this->activityRibbon( 'campaign', $id );
		$openActivities = $campaign->getOpenActivities();
		$openCampaignActivities = $campaign->getOpenCampaignActivities();
		
		$view = new ViewModel( array(
			'auditItems' => $auditItems,
			'campaignResponses' => $campaignResponses,
			'closedActivities' => $closedActivities,
			'closedCampaignActivities' => $closedCampaignActivities,
			'id' => $id,
			'form' => $form,
			'front' => $front,
			'marketingLists' => $marketingLists,
			'navigation' => $navigation,
			'openActivities' => $openActivities,
			'openCampaignActivities' => $openCampaignActivities,
			'pageTitle' => $campaign->getName(),
			'queryForm' => $queryForm,
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/campaign/edit' );
		return $view;
	}

	/**
	 * Deletes a campaign.
	 *
	 * @return void
	 */
	public function deleteAction() {
		/* @var $success DeleteResponse */
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'campaign', array(
				'action' => 'index'
			) );
		}
		
		// Create the receiver and concrete command
		$target = new TargetDeleteCampaign();
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
		return $this->redirect()->toRoute( 'campaign', array(
			'action' => 'index'
		) );
	}

	/**
	 * Copies the information from one campaign to another
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function copyAction() {
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'campaign', array(
				'action' => 'index'
			) );
		}
		
		$service = $this->getService();
		
		// Create the form and bind the specified entity to it
		$form = new CampaignForm( $service->getEntityManager() );
		$campaign = $this->fetchCampaign( $id );
		$form->bind( $campaign );
		
		// Set form values
		$this->setFormValues( $form, $campaign );
		$statusMessage = sprintf( self::MSG_STATUS, $campaign->getStatus() );
		
		// Clone the campaign
		$copy = clone $campaign;
		$copy->setId( 0 );
		$copy->setName( 'Copy of ' . $campaign->getName() );
		$copy->setStatus( CampaignStatus::PROPOSED );
		$copy->setActualStart( null );
		$copy->setActualEnd( null );
		
		// Persist the clone and return
		$target = new TargetCreateCampaign();
		$target->setEntity( $copy );
		$create = new CreateRequest();
		$create->setTarget( $target );
		
		$success = $service->execute( $create );
		if ($success->getResult()) {
			return $this->redirect()->toRoute( 'campaign', array(
				'action' => 'index'
			) );
		} else {
			$statusMessage = sprintf( self::MSG_ERROR_UPDATE, $success->getMessage() );
		}
		
		// Fetch view parameters
		$auditItems = $campaign->getAuditItems()->getValues();
		$closedCampaignActivities = $campaign->getClosedCampaignActivities();
		$openCampaignActivities = $campaign->getOpenCampaignActivities();
		
		$view = new ViewModel( array(
			'auditItems' => $auditItems,
			'closedCampaignActivities' => $closedCampaignActivities,
			'id' => $id,
			'form' => $form,
			'openCampaignActivities' => $openCampaignActivities,
			'pageTitle' => $campaign->getName(),
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/campaign/edit' );
		return $view;
	}

	public function addActivityAction() {
		/* @var $request Request */
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$type = $this->params( 'type', null );
		if (empty( $id ) || empty( $type )) {
			return $this->redirect()->toRoute( 'campaign', array(
				'action' => 'index'
			) );
		}
		
		$service = $this->getService();
		$campaign = $this->fetchCampaign( $id );
		
		// Create the form and bind an empty activity to it
		$clazz = 'Application\Model\\' . $type;
		$rc = new \ReflectionClass( $clazz );
		$activity = $rc->newInstance();
		$form = new ActivityForm( $service->getEntityManager(), $clazz );
		$form->bind( $activity );
		
		$this->initializeActivityFormValues( $form, $activity, $campaign );
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the entity and return
				$target = new TargetCreateActivity();
				$target->setEntity( $activity );
				$create = new CreateRequest();
				$create->setTarget( $target );
				$success = $service->execute( $create );
				
				if ($success->getResult()) {
					return $this->redirect()->toRoute( 'campaign', array(
						'action' => 'edit',
						'id' => $id
					) );
				}
				$statusMessage = sprintf( self::MSG_ERROR_CREATE, $success->getMessage() );
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		$words = preg_split( '/(?<=[a-z])(?=[A-Z])/x', $type );
		$preposition = ($activity instanceof AbstractAppointment ? ' with ' : ' for ');
		$title = 'New ' . implode( ' ', $words ) . $preposition . $campaign->getName();
		
		$view = new ViewModel( array(
			'id' => $id,
			'form' => $form,
			'pageTitle' => $title,
			'statusMessage' => $statusMessage,
			'type' => $type
		) );
		if($activity instanceof CampaignResponse) {
			$view->setTemplate( 'application/activity/createResponse' );
		} else {
			$view->setTemplate( 'application/activity/create' );
		}
		return $view;
	}

	public function marketingListIndexAction() {
		/* @var $response RetrieveMultipleResponse */
		$service = $this->getService();
		$statusMessage = 'No records found.';
		$recordSet = array();
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$type = $this->params( 'type', null );
		if (empty( $id ) || empty( $type )) {
			return $this->redirect()->toRoute( 'campaign', array(
				'action' => 'index'
			) );
		}
		
		// Create the form and bind the specified entity to it
		$cf = new CampaignForm( $service->getEntityManager() );
		$campaign = $this->fetchCampaign( $id );
		$cf->bind( $campaign );
		$this->setFormValues( $cf, $campaign );
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			if ($type == 'Query') {
				$target = new TargetRetrieveMultipleMarketingListByPost();
				$target->setParams( $request->getPost( MarketingListFieldset::FIELDSETNAME ) );
				$retrieve = new RetrieveMultipleRequest();
				$retrieve->setTarget( $target );
				
				$start = microtime( true );
				$response = $service->execute( $retrieve );
				$recordSet = $campaign->removeMatchedCandidates( $response->getRecordSet() );
				$end = microtime( true );
				
				$elapsedTime = $end - $start;
				$numRecords = count( $recordSet );
				$statusMessage = sprintf( self::MSG_STATISTICS, $numRecords, $elapsedTime );
				$front = 'tab-5';
			} elseif ($type == 'Add') {
				$front = 'tab-4';
				$selectedItems = $request->getPost( 'selected_fld' );
				
				$add = new AddListsCampaignRequest();
				$add->setEntity( $campaign );
				$add->setListIds( $selectedItems );
				
				$success1 = $service->execute( $add );
				if ($success1->getResult()) {
					return $this->redirect()->toRoute( 'campaign', array(
						'action' => 'edit',
						'id' => $id,
						'front' => $front
					) );
				}
				$statusMessage = $response->getMessage();
			} elseif ($type == 'Remove') {
				$front = 'tab-4';
				$selectedItems = $request->getPost( 'selected_fld' );
				
				$remove = new RemoveListsCampaignRequest();
				$remove->setEntity( $campaign );
				$remove->setListIds( $selectedItems );
				
				$success2 = $service->execute( $remove );
				if ($success2->getResult()) {
					return $this->redirect()->toRoute( 'campaign', array(
						'action' => 'edit',
						'id' => $id,
						'front' => $front
					) );
				}
				$statusMessage = $response->getMessage();
			}
		}
		
		$queryForm = new MarketingListForm( $service->getEntityManager() );
		$queryForm->bind( new MarketingList() );
		
		// Set view parameters
		$auditItems = $campaign->getAuditItems()->getValues();
		$closedActivities = $campaign->getClosedActivities();
		$closedCampaignActivities = $campaign->getClosedCampaignActivities();
		$marketingLists = $campaign->getListsAsArray();
		$navigation = $this->activityRibbon( 'campaign', $id );
		$openActivities = $campaign->getOpenActivities();
		$openCampaignActivities = $campaign->getOpenCampaignActivities();
		
		$view = new ViewModel( array(
			'auditItems' => $auditItems,
			'closedActivities' => $closedActivities,
			'closedCampaignActivities' => $closedCampaignActivities,
			'id' => $id,
			'form' => $cf,
			'front' => $front,
			'marketingLists' => $marketingLists,
			'navigation' => $navigation,
			'openActivities' => $openActivities,
			'openCampaignActivities' => $openCampaignActivities,
			'pageTitle' => $campaign->getName(),
			'queryForm' => $queryForm,
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/campaign/edit' );
		return $view;
	}

	/**
	 * Fetches the specified campaign
	 *
	 * @param int $id
	 * @return Campaign
	 */
	private function fetchCampaign($id) {
		/* @var $response RetrieveResponse */
		
		// Create the receiver and command objects
		$target = new TargetRetrieveCampaign();
		$target->setId( (int) $id );
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget( $target );
		
		// Fetch the specified entity
		$service = $this->getService();
		$response = $service->execute( $retrieve );
		return $response->getEntity();
	}

	/**
	 * Sets default form values for a new campaign activity
	 *
	 * @param ActivityForm $form
	 * @param Campaign $campaign
	 */
	private function initializeActivityFormValues(ActivityForm $form, AbstractActivity $activity, Campaign $campaign) {
		/* @var $afs Fieldset */
		/* @var $owner User */
		$now = new \DateTime();
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		$afs = $form->get( BaseActivityFieldset::FIELDSETNAME );
		$afs->get( BaseActivityFieldset::CAMPAIGN )->setValue( $campaign->getId() );
		
		if ($activity instanceof CampaignActivity) {
			$afs->get( CampaignActivityFieldset::CHANNEL )->setValue( ChannelType::PHONE );
			$afs->get( CampaignActivityFieldset::OWNER )->setValue( $owner->getId() );
			$afs->get( CampaignActivityFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
			$afs->get( CampaignActivityFieldset::SCHEDULEDSTART )->setValue( $now->format( 'm/d/Y' ) );
			$afs->get( CampaignActivityFieldset::STATUS )->setValue( CampaignActivityStatus::PROPOSED );
			$afs->get( CampaignActivityFieldset::TYPECODE )->setValue(CampaignActivityType::RESEARCH);
			
		} elseif ($activity instanceof CampaignResponse) {
			$afs->get( CampaignResponseFieldset::CHANNEL )->setValue( ChannelType::PHONE );
			$afs->get( CampaignResponseFieldset::OWNER )->setValue( $owner->getId() );
			$afs->get( CampaignResponseFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
			$afs->get( CampaignResponseFieldset::RECEIVEDON )->setValue( $now->format( 'm/d/Y' ) );
			$afs->get( CampaignResponseFieldset::STATUS )->setValue( OpportunityCloseStatus::OPEN );
			
		} elseif ($activity instanceof AbstractAppointment) {
			$afs->get( AppointmentFieldset::SCHEDULEDSTART )->setValue( $now->format( 'm/d/Y g:ia' ) );
			$afs->get( AppointmentFieldset::STATUS )->setValue( AppointmentStatus::FREE );
			$afs->get( AppointmentFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
			
		} elseif ($activity instanceof AbstractInteraction) {
			$afs->get( InteractionFieldset::ACTUALSTART )->setValue( $now->format( 'm/d/Y' ) );
			if ($activity instanceof EmailInteraction) {
				$afs->get( InteractionFieldset::STATUS )->setValue( LetterStatus::OPEN );
			} elseif ($activity instanceof LetterInteraction) {
				$afs->get( LetterFieldset::STATUS )->setValue( LetterStatus::OPEN );
			} elseif ($activity instanceof FaxInteraction) {
				$afs->get( FaxFieldset::STATUS )->setValue( FaxStatus::OPEN );
			} elseif ($activity instanceof TelephoneInteraction) {
				$afs->get( TelephoneInteractionFieldset::STATUS )->setValue( TelephoneStatus::OPEN );
			} elseif ($activity instanceof VisitInteraction) {
				$afs->get( VisitFieldset::STATUS )->setValue( AppointmentStatus::FREE );
			}
			$afs->get( InteractionFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
			
		} elseif ($activity instanceof AbstractNote) {
			$afs->get( NoteFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
			
		} elseif ($activity instanceof AbstractTask) {
			$afs->get( TaskFieldset::SCHEDULEDSTART )->setValue( $now->format( 'm/d/Y' ) );
			$afs->get( TaskFieldset::STATUS )->setValue( TaskStatus::NOTSTARTED );
			$afs->get( TaskFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
		}
	}

	/**
	 * Sets default form values for a new campaign
	 *
	 * @param CampaignForm $form
	 */
	private function initializeFormValues(CampaignForm $form) {
		/* @var $cfs CampaignFieldset */
		/* @var $owner User */
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		$cfs = $form->get( CampaignFieldset::FIELDSETNAME );
		$cfs->get( CampaignFieldset::OWNER )->setValue( $owner->getId() );
		$cfs->get( CampaignFieldset::EXPECTEDRESPONSE )->setValue( 100 );
		$cfs->get( CampaignFieldset::STATUS )->setValue( CampaignStatus::PROPOSED );
		$cfs->get( CampaignFieldset::TYPE )->setValue( CampaignType::ADVERTISEMENT );
	}

	/**
	 * Sets and formats the form values
	 *
	 * @param CampaignForm $form
	 * @param Campaign $campaign
	 */
	private function setFormValues(CampaignForm $form, Campaign $campaign) {
		$form->get( CampaignForm::SUBMIT )->setValue( 'Save' );
		$cfs = $form->get( CampaignFieldset::FIELDSETNAME );
		$cfs->get( CampaignFieldset::ACTUALEND )->setValue( $campaign->getFormattedActualEnd( 'm/d/Y' ) );
		$cfs->get( CampaignFieldset::ACTUALSTART )->setValue( $campaign->getFormattedActualStart( 'm/d/Y' ) );
		$cfs->get( CampaignFieldset::PROPOSEDEND )->setValue( $campaign->getFormattedProposedEnd( 'm/d/Y' ) );
		$cfs->get( CampaignFieldset::PROPOSEDSTART )->setValue( $campaign->getFormattedProposedStart( 'm/d/Y' ) );
	}
}
?>