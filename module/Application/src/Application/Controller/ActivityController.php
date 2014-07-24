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
 * @version     SVN $Id: ActivityController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Form\ActivityForm;
use Application\Form\ActivityTypeForm;
use Application\Form\AppointmentFieldset;
use Application\Form\BaseActivityFieldset;
use Application\Form\CampaignActivityFieldset;
use Application\Form\CampaignResponseFieldset;
use Application\Form\CloseActivityForm;
use Application\Form\EmailFieldset;
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
use Application\Model\AbstractTask;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Application\Model\AppointmentState;
use Application\Model\AppointmentStatus;
use Application\Model\Attachment;
use Application\Model\CampaignActivity;
use Application\Model\CampaignActivityStatus;
use Application\Model\CampaignResponse;
use Application\Model\CampaignResponseStatus;
use Application\Model\ChannelType;
use Application\Model\Contactable;
use Application\Model\Direction;
use Application\Model\EmailInteraction;
use Application\Model\EmailStatus;
use Application\Model\FaxStatus;
use Application\Model\LetterStatus;
use Application\Model\MarketingList;
use Application\Model\ScheduledActivity;
use Application\Model\StatefulActivity;
use Application\Model\TaskStatus;
use Application\Model\TelephoneInteraction;
use Application\Model\TelephoneStatus;
use Application\Model\TrackedActivity;
use Application\Model\User;
use Application\Service\AddListsCampaignActivityRequest;
use Application\Service\AddListsCampaignActivityResponse;
use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\DistributeCampaignActivityRequest;
use Application\Service\DistributeCampaignActivityResponse;
use Application\Service\ClosedActivityOrderRequest;
use Application\Service\OrderOpenActivitiesRequest;
use Application\Service\OrderClosedActivitiesRequest;
use Application\Service\OrderResponse;
use Application\Service\PropagationOwnershipOptions;
use Application\Service\RemoveListsCampaignActivityRequest;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\SendEmailRequest;
use Application\Service\SendEmailResponse;
use Application\Service\TargetCreateActivity;
use Application\Service\TargetDeleteActivity;
use Application\Service\TargetRetrieve;
use Application\Service\TargetRetrieveAccount;
use Application\Service\TargetRetrieveActivity;
use Application\Service\TargetRetrieveContact;
use Application\Service\TargetRetrieveLead;
use Application\Service\TargetRetrieveMultipleByQuery;
use Application\Service\TargetRetrieveMultipleMarketingListByPost;
use Application\Service\TargetUpdateActivity;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;
use Application\Service\UploadActivityMimeAttachmentRequest;
use Application\Service\UploadActivityMimeAttachmentResponse;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Zend\File\Transfer\Adapter\Http;
use Zend\Form\Element\Hidden;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

/**
 * Activity action controller
 *
 * @package		Application\Controller
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: ActivityController.php 13 2013-08-05 22:53:55Z  $
 */
class ActivityController extends AbstractApplicationController {

	/**
	 * Retrieves a collection of open activities
	 *
	 * @return \Zend\View\Model\ViewModel
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		$service = $this->getService();
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		// Create receiver and command objects
		$dql = "select a
				from Application\Model\ScheduledActivity a
				where a.owner = " . $owner->getId() . "
					and (a.state = '" . ActivityState::OPEN . "'
					or a.state = '" . AppointmentState::SCHEDULED . "')
				order by a.scheduledStart, a.status";
		$target = new TargetRetrieveMultipleByQuery();
		$target->setDQL( $dql );
		$retrieveMultipleRequest = new RetrieveMultipleRequest();
		$retrieveMultipleRequest->setTarget( $target );
		
		// Fetch records
		$start = microtime( true );
		$response = $service->retrieveMultiple($retrieveMultipleRequest);
		$recordSet = $response->getRecordSet();
		$end = microtime( true );
		
		$order = new OrderOpenActivitiesRequest( $recordSet );
		$orderResponse = $order->execute();
		if ($orderResponse->getResult()) {
			$recordSet = $orderResponse->getCollection();
			
			// Compute statistics
			$elapsedTime = $end - $start;
			$numRecords = count( $recordSet );
			$statusMessage = sprintf( self::MSG_STATISTICS, $numRecords, $elapsedTime );
		} else {
			$statusMessage = $orderResponse->getMessage();
		}
		
		// Set view parameters
		$navigation = $this->activityRibbon( 'activity', 0 );
		
		$view = new ViewModel( array(
			'activities' => $recordSet,
			'navigation' => $navigation,
			'pageTitle' => 'Open Activities',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/activity/index' );
		return $view;
	}

	/**
	 * Retrieves a collection of closed activities
	 * @return \Zend\View\Model\ViewModel
	 */
	public function closedIndexAction() {
		$owner = $this->zfcUserAuthentication()->getIdentity();
		$service = $this->getService();
		
		// Create receiver and command objects
		$dql = "select a
				from Application\Model\TrackedActivity a
				where a.owner = " . $owner->getId() . "
					and (a.state = '" . ActivityState::COMPLETED . "'
					or a.state = '" . ActivityState::CANCELED . "')
				order by a.actualEnd desc, a.scheduledEnd desc";
		$target = new TargetRetrieveMultipleByQuery();
		$target->setDQL( $dql );
		$retrieveMultipleRequest = new RetrieveMultipleRequest();
		$retrieveMultipleRequest->setTarget( $target );
		
		// Fetch records
		$start = microtime( true );
		$response = $service->retrieveMultiple($retrieveMultipleRequest);
		$recordSet = $response->getRecordSet();
		$end = microtime( true );
		
		$order = new OrderClosedActivitiesRequest( $recordSet );
		$orderResponse = $order->execute();
		if ($orderResponse->getResult()) {
			$recordSet = $orderResponse->getCollection();
			
			// Compute statistics
			$elapsedTime = $end - $start;
			$numRecords = count( $recordSet );
			$statusMessage = sprintf( self::MSG_STATISTICS, $numRecords, $elapsedTime );
		} else {
			$statusMessage = $orderResponse->getMessage();
		}
		
		$view = new ViewModel( array(
			'activities' => $recordSet,
			'pageTitle' => 'Closed Activities',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/activity/closedIndex' );
		return $view;
	}

	/**
	 * Retrieves all appointments from now on
	 * @return ViewModel
	 */
	public function calendarAction() {
		/* @var $response RetrieveMultipleResponse */
		$service = $this->getService();
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		// Create receiver and command objects
		$dql = "select a
				from Application\Model\AbstractAppointment a
				where a.owner = " . $owner->getId() . "
					and (a.state = '" . ActivityState::OPEN . "'
					or a.state = '" . AppointmentState::SCHEDULED . "')
					and a.scheduledStart >= '" . time() . "'
				order by a.scheduledStart desc";
		$target = new TargetRetrieveMultipleByQuery();
		$target->setDQL( $dql );
		$retrieveMultipleRequest = new RetrieveMultipleRequest();
		$retrieveMultipleRequest->setTarget( $target );
		
		// Fetch records
		$start = microtime( true );
		$response = $service->retrieveMultiple($retrieveMultipleRequest);
		$recordSet = $response->getRecordSet();
		$end = microtime( True );
		
		// Compute statistics
		$elapsedTime = $end - $start;
		$numRecords = count( $recordSet );
		$statusMessage = sprintf( self::MSG_STATISTICS, $numRecords, $elapsedTime );
		
		// Set view parameters
		$navigation = $this->activityRibbon( 'calendar', 0 );
		$view = new ViewModel( array(
			'activities' => $recordSet,
			'navigation' => $navigation,
			'pageTitle' => 'Calendar',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/activity/index' );
		return $view;
	}

	/**
	 * Creates a new Activity
	 *
	 * @throws \InvalidArgumentException
	 * 		Throws an exception if the given Activity class is not valid.
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createAction() {
		/* @var $ei EmailInteraction */
		/* @var $object AbstractActivity */
		/* @var $owner User */
		/* @var $request Request */
		
		$service = $this->getService();
		$owner = $this->zfcUserAuthentication()->getIdentity();
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Fetch and test the parameters
		$activityType = $this->params( 'type', null );
		$id = $this->params( 'entityId', 0 );
		$entityType = $this->params( 'entityRoute', 'activity' );
		if ($activityType == null) {
			return $this->redirect()->toRoute( 'activity', array(
				'action' => 'index'
			) );
		}
		
		// Create the form and bind an empty activity to it
		$activityClazz = 'Application\Model\\' . $activityType;
		$rc = new \ReflectionClass( $activityClazz );
		$activity = $rc->newInstance();
		$form = new ActivityForm( $service->getEntityManager(), $activityClazz );
		$form->bind( $activity );
		
		// Initialize values
		$this->initializeFormValues( $form, $activity, $entityType, $id );
		
		// Process request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$postData = $this->fetchPostData( $request, $activityType );
			$form->setData( $postData );
			if ($form->isValid()) {
				$canSave = true;
				
				// Process mail
				if ($activity instanceof EmailInteraction) {
					$ei = $activity;
					if($ei->getDirection() == Direction::OUTBOUND) {
						$statusMessage = $this->processMail( $activity, $form, $postData );
						$canSave = ($statusMessage == '' ? true : false);
					} else {
						$activity->setStatus(EmailStatus::RECEIVED);
					}
				}
				
				// Persist the entity and return
				if ($canSave) {
					$target = new TargetCreateActivity();
					$target->setEntity( $activity );
					$createRequest = new CreateRequest();
					$createRequest->setTarget( $target );
					$createResponse = $service->create($createRequest);
					if ($createResponse->getResult()) {
						$isRedirect = $request->getPost( ActivityForm::SUBMITCLOSE, false );
						if ($isRedirect) {
							return $this->redirect()->toRoute( $entityType, array(
								'action' => 'edit',
								'id' => $id
							) );
						}
						$statusMessage = $createResponse->getMessage();
					} else {
						$statusMessage = sprintf( self::MSG_ERROR_CREATE, $createResponse->getMessage() );
					}
				}
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
				print_r( $form->getMessages() );
			}
		}
		
		$view = new ViewModel( array(
			'entityId' => $id,
			'entityRoute' => $entityType,
			'form' => $form,
			'id' => 0,
			'pageTitle' => $this->constructPageTitle( $activityType, 'New' ),
			'type' => $activityType,
			'statusMessage' => $statusMessage
		) );
		if ($activity instanceof EmailInteraction) {
			$view->setTemplate( 'application/activity/createEmail' );
		} elseif ($activity instanceof CampaignResponse) {
			$view->setTemplate( 'application/activity/createResponse' );
		} else {
			$view->setTemplate( 'application/activity/create' );
		}
		return $view;
	}

	/**
	 * Updates an activity
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		/* @var $ei EmailInteraction */
		/* @var $request Request */

		$service = $this->getService();
		$statusMessage = 'Status: Normal';
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$activityType = $this->params( 'type', null );
		$entityId = $this->params( 'entityId', 0 );
		$entityType = $this->params( 'entityRoute', 'activity' );
		if (empty( $id ) || empty( $activityType )) {
			return $this->redirect()->toRoute( 'activity', array(
				'action' => 'index'
			) );
		}
		
		// Create the form
		$clazz = 'Application\Model\\' . $activityType;
		$form = new ActivityForm( $service->getEntityManager(), $clazz );
		
		// Fetch the activity and bind it to the form
		$activity = $this->fetchActivity( $id, $clazz );
		$form->bind( $activity );
		
		// Set non-standard form values
		$this->setFormValues( $form, $activity );
		if ($activity instanceof StatefulActivity) {
			$statusMessage = sprintf( self::MSG_STATUS, $activity->getStatus() );
		}
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$postData = $this->fetchPostData( $request, $activityType );
			$form->setData( $postData );
			if ($form->isValid()) {
				$canSave = true;
				
				// Process mail
				if ($activity instanceof EmailInteraction) {
					$ei = $activity;
					if(($ei->getState() == ActivityState::OPEN) && ($ei->getDirection() == Direction::OUTBOUND)) {
						$statusMessage = $this->processMail( $activity, $form, $postData );
						$canSave = ($statusMessage == '' ? true : false);
					}
				}
				
				// Persist the activity and return
				if ($canSave) {
					$target = new TargetUpdateActivity();
					$target->setEntity( $activity );
					$updateRequest = new UpdateRequest();
					$updateRequest->setTarget( $target );
					$updateResponse = $service->update($updateRequest);
					if ($updateResponse->getResult()) {
						$isRedirect = $request->getPost( ActivityForm::SUBMITCLOSE, false );
						if ($isRedirect) {
							return $this->redirect()->toRoute( $entityType, array(
								'action' => 'edit',
								'id' => $entityId
							) );
						}
						$statusMessage = $updateResponse->getMessage();
					} else {
						$statusMessage = sprintf( self::MSG_ERROR_UPDATE, $updateResponse->getMessage() );
					}
				} elseif ($activity instanceof EmailInteraction) {
					return $this->redirect()->toRoute( $entityType, array(
						'action' => 'edit',
						'id' => $entityId
					) );
				}
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM . $this->parseErrorMessages( $form->getMessages() );
			}
		}
		
		$caf = new CloseActivityForm( $activityType );
		$auditItems = $activity->getAuditItems()->getValues();
		
		// Create the view
		$view = new ViewModel( array(
			'activity' => $activity,
			'auditItems' => $auditItems,
			'caf' => $caf,
			'closeActivityContainerClass' => 'hidden',
			'entityId' => $entityId,
			'entityRoute' => $entityType,
			'object' => $activity,
			'form' => $form,
			'id' => $id,
			'pageTitle' => 'Edit ' . $activity->getSubject(),
			'state' => $activity->getState(),
			'type' => $activityType,
			'statusMessage' => $statusMessage
		) );
		
		// Set the view template and additional parameters depending on the activity type
		if ($activity instanceof CampaignActivity) {
			$view = $this->setCampaignActivityViewParameters( $activity, $view, true );
			$view->setTemplate( 'application/activity/editCampaignActivity' );
		} elseif ($activity instanceof EmailInteraction) {
			$view->setTemplate( 'application/activity/editEmail' );
		} elseif ($activity instanceof CampaignResponse) {
			$view->setTemplate( 'application/activity/editResponse' );
		} else {
			$view->setTemplate( 'application/activity/edit' );
		}
		return $view;
	}

	/**
	 * Deletes an activity
	 * @return \Zend\Stdlib\ResponseInterface>
	 */
	public function deleteAction() {
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$activityType = $this->params( 'type', null );
		$entityId = $this->params( 'entityId', 0 );
		$entityType = $this->params( 'entityRoute', 'activity' );
		if (empty( $id ) || empty( $activityType )) {
			return $this->redirect()->toRoute( $entityType, array(
				'action' => 'index'
			) );
		}
		
		$clazz = 'Application\Model\\' . $activityType;
		$service = $this->getService();
		
		// Create receiver and command
		$target = new TargetDeleteActivity( $clazz );
		$target->setId( $id );
		$deleteRequest = new DeleteRequest();
		$deleteRequest->setTarget( $target );
		
		// Invoke method and return
		$success = $service->delete($deleteRequest);
		return $this->redirect()->toRoute( $entityType, array(
			'action' => 'index'
		) );
	}

	/**
	 * Closes an activity
	 * @return \Zend\View\Model\ViewModel
	 */
	public function closeAction() {
		/* @var $ai AbstractInteraction */
		/* @var $request Request */

		$service = $this->getService();
		$statusMessage = 'Status: Normal';
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$activityType = $this->params( 'type', null );
		$entityId = $this->params( 'entityId', 0 );
		$entityType = $this->params( 'entityRoute', 'activity' );
		if (empty( $id ) || empty( $activityType )) {
			return $this->redirect()->toRoute( 'activity', array(
				'action' => 'index'
			) );
		}
		
		// Create the form
		$clazz = 'Application\Model\\' . $activityType;
		$form = new ActivityForm( $service->getEntityManager(), $clazz );
		
		// Fetch the activity and bind it to the form
		$ai = $this->fetchActivity( $id, $clazz );
		$form->bind( $ai );
		
		// Set non-standard form values
		$this->setFormValues( $form, $ai );
		if ($ai instanceof StatefulActivity) {
			$statusMessage = sprintf( self::MSG_STATUS, $ai->getStatus() );
		}
		
		$caf = new CloseActivityForm( $activityType );
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$caf->setData( $request->getPost() );
			if ($caf->isValid()) {
				$status = $caf->get( CloseActivityForm::STATUS )->getValue();
				$actualEnd = new \DateTime( $caf->get( CloseActivityForm::ACTUALEND )->getValue() );
				$ai->setActualEnd( $actualEnd, true );
				$ai->setStatus( $status );
				
				$target = new TargetUpdateActivity();
				$target->setEntity( $ai );
				$updateRequest = new UpdateRequest();
				$updateRequest->setTarget( $target );
				
				$updateResponse = $service->update($updateRequest);
				if ($updateResponse->getResult()) {
					return $this->redirect()->toRoute( $entityType, array(
						'action' => 'edit',
						'id' => $entityId
					) );
				}
				$statusMessage = sprintf( self::MSG_ERROR_UPDATE, $success->getm );
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM;
			}
		}
		
		// Create the view
		$auditItems = $ai->getAuditItems()->getValues();
		$view = new ViewModel( array(
			'activity' => $ai,
			'auditItems' => $auditItems,
			'caf' => $caf,
			'closeActivityContainerClass' => '',
			'entityId' => $entityId,
			'entityRoute' => $entityType,
			'object' => $ai,
			'form' => $form,
			'id' => $id,
			'pageTitle' => 'Edit ' . $activity->getSubject(),
			'type' => $activityType,
			'statusMessage' => $statusMessage
		) );
		
		// Set the view template and additional parameters depending on the activity type
		if ($activity instanceof CampaignActivity) {
			$view = $this->setCampaignActivityViewParameters( $activity, $view, true );
			$view->setTemplate( 'application/activity/editCampaignActivity' );
		} elseif ($activity instanceof EmailInteraction) {
			$view->setTemplate( 'application/activity/editEmail' );
		} elseif ($activity instanceof CampaignResponse) {
			$view->setTemplate( 'application/activity/editResponse' );
		} else {
			$view->setTemplate( 'application/activity/edit' );
		}
		return $view;
	}

	/**
	 * Distributes an activity across the members of the included list
	 * @return \Zend\View\Model\ViewModel
	 */
	public function distributeAction() {
		/* @var $campaignActivity CampaignActivity */
		/* @var $owner User */
		/* @var $request Request */
		/* @var $dcaResponse DistributeCampaignActivityResponse */
		
		$service = $this->getService();
		$owner = $this->zfcUserAuthentication()->getIdentity();
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$activityType = $this->params( 'type', null );
		$entityId = $this->params( 'entityId', 0 );
		$entityType = $this->params( 'entityRoute', 'activity' );
		if (empty( $id ) || empty( $activityType )) {
			return $this->redirect()->toRoute( 'activity', array(
				'action' => 'index'
			) );
		}
		
		// Create the campaign activity form and bind the specified campaign activity to
		// it
		$caType = 'CampaignActivity';
		$caClazz = 'Application\Model\\' . $caType;
		$form = new ActivityForm( $service->getEntityManager(), $caClazz );
		$campaignActivity = $this->fetchActivity( $id, $caClazz );
		$form->bind( $campaignActivity );
		$this->setFormValues( $form, $campaignActivity );
		
		// Create the distribution form and bind an empty activity to it
		$clazz = 'Application\Model\\' . $activityType;
		$rc = new \ReflectionClass( $clazz );
		$activity = $rc->newInstance();
		$df = new ActivityForm( $service->getEntityManager(), $clazz );
		$df->bind( $activity );
		
		// Initialize values
		// Make the parent entity null so that it can be set in the post request
		$this->initializeFormValues( $df, $activity, null, $id );
		
		// Process request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$postData = $this->fetchPostData( $request, $activityType );
			$df->setData( $postData );
			if ($df->isValid()) {
				$sendEmail = false;
				if ($activity instanceof EmailInteraction) {
					$sendEmail = ($activity->getIssueSend() == 0 ? true : false);
				}
				
				$dcaRequest = new DistributeCampaignActivityRequest();
				$dcaRequest->setActivity( $activity );
				$dcaRequest->setCampaignActivity( $campaignActivity );
				$dcaRequest->setOwner( $owner );
				$dcaRequest->setOwnershipOptions( PropagationOwnershipOptions::CALLER );
				$dcaRequest->setSendEmail( $sendEmail );
				
				$dcaResponse = $service->execute( $dcaRequest );
				if ($dcaResponse->getResult()) {
					return $this->redirect()->toRoute( 'activity', array(
						'action' => 'edit',
						'id' => $id,
						'type' => 'CampaignActivity'
					) );
				} else {
					$statusMessage = $dcaResponse->getMessage();
				}
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
				var_dump( $df->getMessages() );
			}
		}
		
		// Create the view
		$auditItems = $campaignActivity->getAuditItems()->getValues();
		$view = new ViewModel( array(
			'activity' => $campaignActivity,
			'auditItems' => $auditItems,
			'channel' => $activityType,
			'channelType' => $campaignActivity->getChannelType(),
			'df' => $df,
			'distributeActivityContainerClass' => '',
			'entityId' => $entityId,
			'entityRoute' => $entityType,
			'form' => $form,
			'id' => $id,
			'pageTitle' => $this->constructPageTitle( $caType, 'Edit' ),
			'pageSubTitle' => $this->constructPageSubTitle( $activityType ),
			'type' => $caType,
			'statusMessage' => $statusMessage
		) );
		$view = $this->setCampaignActivityViewParameters( $campaignActivity, $view, false );
		$view->setTemplate( 'application/activity/editCampaignActivity' );
		return $view;
	}

	/**
	 * Manages a marketin glist
	 * @return \Zend\View\Model\ViewModel
	 */
	public function marketingListIndexAction() {
		/* @var $activity CampaignActivity */
		/* @var $response RetrieveMultipleResponse */
		
		$service = $this->getService();
		$statusMessage = 'No records found.';
		$recordSet = array();
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$type = $this->params( 'type', null );
		$entityId = $this->params( 'entityId', 0 );
		$entityType = $this->params( 'entityRoute', 'activity' );
		if (empty( $id ) || empty( $type )) {
			return $this->redirect()->toRoute( 'activity', array(
				'action' => 'index'
			) );
		}
		
		// Create the form and bind the specified entity to it
		$clazz = 'Application\Model\CampaignActivity';
		$cf = new ActivityForm( $service->getEntityManager(), $clazz );
		$activity = $this->fetchActivity( $id, $clazz );
		$cf->bind( $activity );
		$this->setFormValues( $cf, $activity );
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			if ($type == 'Query') {
				$target = new TargetRetrieveMultipleMarketingListByPost();
				$target->setParams( $request->getPost( MarketingListFieldset::FIELDSETNAME ) );
				$retrieveMultipleRequest = new RetrieveMultipleRequest();
				$retrieveMultipleRequest->setTarget( $target );
				
				$start = microtime( true );
				$retrieveMultipleResponse = $service->retrieveMultiple($retrieveMultipleRequest);
				$recordSet = $activity->removeMatchedCandidates( $retrieveMultipleResponse->getRecordSet() );
				$end = microtime( true );
				
				$elapsedTime = $end - $start;
				$numRecords = count( $recordSet );
				$statusMessage = sprintf( self::MSG_STATISTICS, $numRecords, $elapsedTime );
				$front = 'tab-3';
			} elseif ($type == 'Add') {
				$selectedItems = $request->getPost( 'selected_fld' );
				
				$add = new AddListsCampaignActivityRequest();
				$add->setEntity( $activity );
				$add->setListIds( $selectedItems );
				
				$success1 = $service->execute( $add );
				if ($success1->getResult()) {
					return $this->redirect()->toRoute( 'activity', array(
						'action' => 'edit',
						'id' => $id,
						'type' => 'CampaignActivity'
					) );
				}
				$statusMessage = $response->getMessage();
				$front = 'tab-2';
			} elseif ($type == 'Remove') {
				$selectedItems = $request->getPost( 'selected_fld' );
				
				$remove = new RemoveListsCampaignActivityRequest();
				$remove->setEntity( $activity );
				$remove->setListIds( $selectedItems );
				
				$success2 = $service->execute( $remove );
				if ($success2->getResult()) {
					return $this->redirect()->toRoute( 'activity', array(
						'action' => 'edit',
						'id' => $id,
						'type' => 'CampaignActivity'
					) );
				}
				$statusMessage = $response->getMessage();
				$front = 'tab-2';
			}
		}
		
		$queryForm = new MarketingListForm( $service->getEntityManager() );
		$queryForm->bind( new MarketingList() );
		
		// Set view parameters
		$auditItems = $activity->getAuditItems()->getValues();
		$closedActivities = $activity->getClosedActivities();
		$marketingLists = $activity->getListsAsArray();
		$navigation = $this->activityRibbon( 'activity', $id );
		$openActivities = $activity->getOpenActivities();
		
		$view = new ViewModel( array(
			'auditItems' => $auditItems,
			'closedActivities' => $closedActivities,
			'entityId' => $entityId,
			'entityRoute' => $entityType,
			'id' => $id,
			'form' => $cf,
			'front' => $front,
			'marketingLists' => $marketingLists,
			'navigation' => $navigation,
			'openActivities' => $openActivities,
			'pageTitle' => $activity->getSubject(),
			'queryForm' => $queryForm,
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage
		) );
		$view = $this->setCampaignActivityViewParameters( $activity, $view, true );
		$view->setTemplate( 'application/activity/editCampaignActivity' );
		return $view;
	}

	/**
	 * Promotes an activity to a response
	 * @return \Zend\View\Model\ViewModel
	 */
	public function promoteToResponseAction() {
		/* @var $request Request */
		/* @var $response RetrieveResponse */
		/* @var $success CreateResponse */

		$service = $this->getService();
		$statusMessage = 'Status: Normal';
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$activityType = $this->params( 'type', null );
		$entityId = $this->params( 'entityId', 0 );
		$entityType = $this->params( 'entityRoute', 'activity' );
		if (empty( $id ) || empty( $activityType )) {
			return $this->redirect()->toRoute( 'activity', array(
				'action' => 'index'
			) );
		}
		
		// Fetch the activity
		$clazz = 'Application\Model\\' . $activityType;
		$activity = $this->fetchActivity( $id, $clazz );
		
		// Create the form and bind a new campaign response entity to it
		$responseClazz = 'Application\Model\CampaignResponse';
		$form = new ActivityForm( $service->getEntityManager(), $responseClazz );
		$campaignResponse = new CampaignResponse();
		$form->bind( $campaignResponse );
		
		// Set initial form values
		$this->initializeResponseFormValues( $form, $activity );
		if ($activity instanceof StatefulActivity) {
			$statusMessage = sprintf( self::MSG_STATUS, $activity->getStatus() );
		}
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the response and return
				$target = new TargetCreateActivity();
				$target->setEntity( $campaignResponse );
				$createRequest = new CreateRequest();
				$createRequest->setTarget( $target );
				
				$createResponse = $service->create($createRequest);
				if ($createResponse->getResult()) {
					$isRedirect = $request->getPost( ActivityForm::SUBMITCLOSE, false );
					if ($isRedirect) {
						return $this->redirect()->toRoute( 'campaign', array(
							'action' => 'edit',
							'id' => $activity->getCampaign()
								->getId(),
							'front' => 'tab-6'
						) );
					}
					$statusMessage = $createResponse->getMessage();
				} else {
					$statusMessage = sprintf( self::MSG_ERROR_CREATE, $createResponse->getMessage() );
				}
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		
		$caf = new CloseActivityForm( 'CampaignResponse' );
		$auditItems = $activity->getAuditItems()->getValues();
		
		// Create the view
		$view = new ViewModel( array(
			'activity' => $activity,
			'auditItems' => $auditItems,
			'caf' => $caf,
			'entityId' => $entityId,
			'entityRoute' => $entityType,
			'object' => $activity,
			'form' => $form,
			'id' => $id,
			'pageTitle' => 'New Response for ' . $activity->getSubject(),
			'type' => $activityType,
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/activity/createResponse' );
		return $view;
	}

	/**
	 * Creates the page title text from the activity type
	 *
	 * @param string $activityType
	 * @param string $activityState 'New' or 'Edit
	 */
	private function constructPageTitle($activityType, $activityState) {
		$words = preg_split( '/(?<=[a-z])(?=[A-Z])/x', $activityType );
		return $activityState . ' ' . implode( ' ', $words );
	}

	/**
	 * Returns a subtitle for the distribute campaign activity form dialog
	 *
	 * @param string $activityType
	 * @return string
	 */
	private function constructPageSubTitle($activityType) {
		$result = '';
		if ($activityType != '') {
			$plural = '';
			$singular = '';
			switch ($activityType) {
				case 'EmailInteraction' :
					$plural = 'emails';
					$singular = 'email';
					break;
				case 'FaxInteraction' :
					$plural = 'faxes';
					$singular = 'fax';
					break;
				case 'LetterInteraction' :
					$plural = 'letters';
					$singular = 'letter';
					break;
				case 'TelephoneInteraction' :
					$plural = 'phone calls';
					$singular = 'phone call';
					break;
				case 'VisitInteraction' :
					$plural = 'visits';
					$singular = 'visit';
					break;
				case 'MeetingAppointment' :
					$plural = 'appointments';
					$singular = 'appointment';
					break;
			}
			$format = "Fill out this form to create new %s for the members you selected in the marketing lists." . " To add this %s as a new %s in each member's record, click Distribute.";
			$result = sprintf( $format, $plural, $singular, $singular );
		}
		return $result;
	}

	/**
	 * Fetches the specified activity
	 *
	 * @param int $id
	 * @param string $clazz
	 * @return AbstractActivity
	 */
	private function fetchActivity($id, $clazz) {
		/* @var $response RetrieveResponse */
		
		// Create the receiver and command objects
		$target = new TargetRetrieveActivity( $clazz );
		$target->setId( $id );
		$retrieveRequest = new RetrieveRequest();
		$retrieveRequest->setTarget( $target );
		
		// Fetch the specified entity
		$service = $this->getService();
		$retrieveResponse = $service->retrieve($retrieveRequest);
		return $retrieveResponse->getEntity();
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	private function fetchPostData(Request $request, $activityType) {
		// Merge the form post data with any file uploads
		$post = array_merge_recursive( $request->getPost()->toArray(), $request->getFiles()->toArray() );
		
		// Test for a file upload
		switch ($activityType) {
			case 'EmailInteraction' :
				$fieldset = EmailFieldset::FIELDSETNAME;
				$key = EmailFieldset::FILEUPLOAD;
				if (isset( $post[$fieldset][$key] )) {
					$post[$fieldset]['filename'] = $post[$fieldset][$key]['name'];
					$post[$fieldset]['filesize'] = $post[$fieldset][$key]['size'];
					$post[$fieldset]['mimetype'] = $post[$fieldset][$key]['type'];
				}
				break;
			default :
				break;
		}
		
		return $post;
	}

	/**
	 * Initialize form values for a new activity
	 *
	 * @param ActivityForm $form
	 * @param AbstractActivity $activity
	 * @param string|null $entityType
	 * @param int|null $id
	 */
	private function initializeFormValues(ActivityForm $form, AbstractActivity $activity, $entityType = null, $id = 0) {
		/* @var $afs BaseActivityFieldset */
		/* @var $owner User */

		$owner = $this->zfcUserAuthentication()->getIdentity();
		$afs = $form->get( BaseActivityFieldset::FIELDSETNAME );
		
		// Initialize ownership
		$afs->get( BaseActivityFieldset::OWNER )->setValue( $owner->getId() );
		
		// Initialize One-to-Many associations
		switch ($entityType) {
			case 'account' :
				$afs->get( BaseActivityFieldset::ACCOUNT )->setValue( $id );
				break;
			case 'campaign' :
				$afs->get( BaseActivityFieldset::CAMPAIGN )->setValue( $id );
				break;
			case 'contact' :
				$afs->get( BaseActivityFieldset::CONTACT )->setValue( $id );
				break;
			case 'lead' :
				$afs->get( BaseActivityFieldset::LEAD )->setValue( $id );
				break;
			case 'opportunity' :
				$afs->get( BaseActivityFieldset::OPPORTUNITY )->setValue( $id );
				break;
		}
		
		// Initialize values
		if ($afs instanceof CampaignActivityFieldset) {
			$afs->get( CampaignActivityFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
			$afs->get( CampaignActivityFieldset::STATUS )->setValue( CampaignActivityStatus::PROPOSED );
		} elseif ($afs instanceof AppointmentFieldset) {
			$ro = $this->fetchRegardingObject( $entityType, $id );
			if ($ro != null) {
				$address = $ro->getPrimaryAddress()->getFullAddress();
				$afs->get( AppointmentFieldset::LOCATION )->setValue( $address );
			}
			$afs->get( AppointmentFieldset::STATUS )->setValue( AppointmentStatus::FREE );
			$afs->get( AppointmentFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
		} elseif ($afs instanceof InteractionFieldset) {
			$ro = $this->fetchRegardingObject( $entityType, $id );
			if ($ro != null) {
				if ($afs instanceof EmailFieldset) {
					$afs->get( InteractionFieldset::TO )->setValue( $ro->getEmail1() );
				} else {
					$afs->get( InteractionFieldset::TO )->setValue( $ro->getDisplayName() );
				}
			}
			
			$afs->get( InteractionFieldset::FROM )->setValue( $owner->getFullName() );
			$afs->get( InteractionFieldset::DIRECTION )->setValue( Direction::OUTBOUND );
			$afs->get( InteractionFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
			
			if ($afs instanceof EmailFieldset) {
				$afs->get( EmailFieldset::FROM )->setValue( $owner->getEmail() );
				$afs->get( EmailFieldset::DESCRIPTION )->setValue( $owner->getEmailSignature() );
				$afs->get( EmailFieldset::STATUS )->setValue( EmailStatus::DRAFT );
				$form->get( ActivityForm::SUBMITCLOSE )->setValue( 'Send And Save' );
			} elseif ($afs instanceof LetterFieldset) {
				$afs->get( LetterFieldset::STATUS )->setValue( LetterStatus::OPEN );
				if ($ro != null) {
					$address = $ro->getPrimaryAddress()->getFullAddress();
					$afs->get( LetterFieldset::ADDRESS )->setValue( $address );
				}
			} elseif ($afs instanceof FaxFieldset) {
				$afs->get( FaxFieldset::STATUS )->setValue( FaxStatus::OPEN );
			} elseif ($afs instanceof TelephoneInteractionFieldset) {
				$afs->get( TelephoneInteractionFieldset::STATUS )->setValue( TelephoneStatus::OPEN );
				if ($ro != null) {
					$afs->get( TelephoneInteractionFieldset::TELEPHONE )->setValue( $ro->getPrimaryTelephone() );
				}
			} elseif ($afs instanceof VisitFieldset) {
				$afs->get( VisitFieldset::DIRECTION )->setValue( Direction::INBOUND );
				$afs->get( VisitFieldset::STATUS )->setValue( AppointmentStatus::COMPLETED );
			}
		} elseif ($afs instanceof NoteFieldset) {
			$afs->get( NoteFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
		} elseif ($afs instanceof TaskFieldset) {
			$afs->get( TaskFieldset::STATUS )->setValue( TaskStatus::NOTSTARTED );
			$afs->get( TaskFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
		}
		
		// Initialize dates
		$now = new \DateTime();
		if ($activity instanceof AbstractAppointment) {
			$afs->get( AppointmentFieldset::SCHEDULEDSTART )->setValue( $now->format( 'm/d/Y g:i a' ) );
		} elseif ($activity instanceof CampaignActivity) {
			$afs->get( CampaignActivityFieldset::SCHEDULEDSTART )->setValue( $now->format( 'm/d/Y' ) );
		} elseif ($activity instanceof AbstractInteraction) {
			$afs->get( InteractionFieldset::ACTUALSTART )->setValue( $now->format( 'm/d/Y' ) );
			$afs->get( InteractionFieldset::SCHEDULEDEND )->setValue( $now->format( 'm/d/Y' ) );
		} elseif ($afs instanceof TaskFieldset) {
			$afs->get( TaskFieldset::ACTUALSTART )->setValue( $now->format( 'm/d/Y' ) );
			$afs->get( TaskFieldset::SCHEDULEDEND )->setValue( $now->format( 'm/d/Y' ) );
		}
	}

	/**
	 * Initialize new Campaign Repsonse form values from a parent activity
	 *
	 * @param ActivityForm $form
	 * @param AbstractInteraction $ai
	 */
	private function initializeResponseFormValues(ActivityForm $form, AbstractInteraction $ai) {
		/* @var $afs BaseActivityFieldset */
		$now = new \DateTime();
		$owner = $this->zfcUserAuthentication()->getIdentity();
		$afs = $form->get( BaseActivityFieldset::FIELDSETNAME );
		
		// Set One-to-Many associations
		if ($ai->getAccount()) {
			$afs->get( BaseActivityFieldset::ACCOUNT )->setValue( $ai->getAccount()
				->getId() );
		}
		if ($ai->getContact()) {
			$afs->get( BaseActivityFieldset::CONTACT )->setValue( $ai->getContact()
				->getId() );
		}
		if ($ai->getLead()) {
			$afs->get( BaseActivityFieldset::LEAD )->setValue( $ai->getLead()
				->getId() );
		}
		if ($ai->getOpportunity()) {
			$afs->get( BaseActivityFieldset::OPPORTUNITY )->setValue( $ai->getOpportunity()
				->getId() );
		}
		if ($ai->getCampaign()) {
			$afs->get( BaseActivityFieldset::CAMPAIGN )->setValue( $ai->getCampaign()
				->getId() );
		}
		if ($ai->getCampaignActivity()) {
			$afs->get( CampaignResponseFieldset::CAMPAIGNACTIVITY )->setValue( $ai->getCampaignActivity()
				->getId() );
		}
		
		$afs->get( BaseActivityFieldset::OWNER )->setValue( $owner->getId() );
		$afs->get( BaseActivityFieldset::SUBJECT )->setValue( $ai->getSubject() );
		$afs->get( CampaignResponseFieldset::CHANNEL )->setValue( $ai->getCampaignActivity()
			->getChannelType() );
		$afs->get( CampaignResponseFieldset::FROM )->setValue( $ai->getTo() );
		$afs->get( CampaignResponseFieldset::PRIORITY )->setValue( $ai->getCampaignActivity()
			->getPriority() );
		$afs->get( CampaignResponseFieldset::RECEIVEDON )->setValue( $now->format( 'm/d/Y' ) );
		$afs->get( CampaignResponseFieldset::STATUS )->setValue( CampaignResponseStatus::OPEN );
	}

	/**
	 * Set nonstandard form values for an existing activity
	 *
	 * @param ActivityForm $form
	 * @param AbstractActivity $activity
	 */
	private function setFormValues(ActivityForm $form, AbstractActivity $activity) {
		/* @var $afs BaseActivityFieldset */
		$owner = $this->zfcUserAuthentication()->getIdentity();
		$afs = $form->get( BaseActivityFieldset::FIELDSETNAME );
		$form->get( ActivityForm::SUBMITCLOSE )->setValue( 'Save and Close' );
		
		// Set One-to-Many associations
		if ($activity->getAccount()) {
			$afs->get( BaseActivityFieldset::ACCOUNT )->setValue( $activity->getAccount()
				->getId() );
		}
		if ($activity->getContact()) {
			$afs->get( BaseActivityFieldset::CONTACT )->setValue( $activity->getContact()
				->getId() );
		}
		if ($activity->getLead()) {
			$afs->get( BaseActivityFieldset::LEAD )->setValue( $activity->getLead()
				->getId() );
		}
		if ($activity->getOpportunity()) {
			$afs->get( BaseActivityFieldset::OPPORTUNITY )->setValue( $activity->getOpportunity()
				->getId() );
		}
		if ($activity->getCampaign()) {
			$afs->get( BaseActivityFieldset::CAMPAIGN )->setValue( $activity->getCampaign()
				->getId() );
		}
		
		// Initialize stateful values
		$afs->get( BaseActivityFieldset::OWNER )->setValue( $owner->getId() );
		if ($afs instanceof CampaignActivityFieldset) {
			$afs->get( CampaignActivityFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
			$afs->get( CampaignActivityFieldset::STATUS )->setValue( CampaignActivityStatus::PROPOSED );
		} elseif ($afs instanceof CampaignResponseFieldset) {
			$afs->get( CampaignResponseFieldset::RECEIVEDON )->setValue( $activity->getFormattedReceivedOn( 'm/d/Y' ) );
		} elseif ($activity instanceof AbstractAppointment) {
			$afs->get( AppointmentFieldset::STATUS )->setValue( AppointmentStatus::FREE );
			$afs->get( AppointmentFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
		} elseif ($activity instanceof AbstractInteraction) {
			if ($activity instanceof EmailInteraction) {
				if ($activity->getState() != ActivityState::OPEN) {
					$afs->remove( EmailFieldset::TO );
					$afs->remove( EmailFieldset::SUBJECT );
					$afs->add( array(
						'type' => 'Hidden',
						'name' => EmailFieldset::TO
					) );
					$afs->add( array(
						'type' => 'Hidden',
						'name' => EmailFieldset::SUBJECT
					) );
					$afs->get( EmailFieldset::TO )->setValue( $activity->getTo() );
					$afs->get( EmailFieldset::SUBJECT )->setValue( $activity->getSubject() );
				}
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
			$afs->get( TaskFieldset::STATUS )->setValue( TaskStatus::NOTSTARTED );
			$afs->get( TaskFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
		}
		
		// Format dates
		if ($afs instanceof AppointmentFieldset) {
			$afs->get( AppointmentFieldset::ACTUALEND )->setValue( $activity->getFormattedActualEnd( 'm/d/Y g:i a' ) );
			$afs->get( AppointmentFieldset::ACTUALSTART )->setValue( $activity->getFormattedActualStart( 'm/d/Y g:i a' ) );
			$afs->get( AppointmentFieldset::SCHEDULEDEND )->setValue( $activity->getFormattedScheduledEnd( 'm/d/Y g:i a' ) );
			$afs->get( AppointmentFieldset::SCHEDULEDSTART )->setValue( $activity->getFormattedScheduledStart( 'm/d/Y g:i a' ) );
		} elseif (($afs instanceof TaskFieldset)) {
			$afs->get( TaskFieldset::ACTUALEND )->setValue( $activity->getFormattedActualEnd( 'm/d/Y' ) );
			$afs->get( TaskFieldset::ACTUALSTART )->setValue( $activity->getFormattedActualStart( 'm/d/Y' ) );
			$afs->get( TaskFieldset::SCHEDULEDEND )->setValue( $activity->getFormattedScheduledEnd( 'm/d/Y' ) );
			$afs->get( TaskFieldset::SCHEDULEDSTART )->setValue( $activity->getFormattedScheduledStart( 'm/d/Y' ) );
		} elseif (($afs instanceof CampaignActivityFieldset)) {
			$afs->get( CampaignActivityFieldset::ACTUALEND )->setValue( $activity->getFormattedActualEnd( 'm/d/Y' ) );
			$afs->get( CampaignActivityFieldset::ACTUALSTART )->setValue( $activity->getFormattedActualStart( 'm/d/Y' ) );
			$afs->get( CampaignActivityFieldset::SCHEDULEDEND )->setValue( $activity->getFormattedScheduledEnd( 'm/d/Y' ) );
			$afs->get( CampaignActivityFieldset::SCHEDULEDSTART )->setValue( $activity->getFormattedScheduledStart( 'm/d/Y' ) );
		} elseif ($afs instanceof InteractionFieldset) {
			$afs->get( InteractionFieldset::ACTUALEND )->setValue( $activity->getFormattedActualEnd( 'm/d/Y' ) );
			$afs->get( InteractionFieldset::ACTUALSTART )->setValue( $activity->getFormattedActualStart( 'm/d/Y' ) );
			$afs->get( InteractionFieldset::SCHEDULEDEND )->setValue( $activity->getFormattedScheduledEnd( 'm/d/Y' ) );
		}
	}

	/**
	 * Returns form error messages as a string
	 *
	 * @param array $messages
	 * @return string
	 */
	private function parseErrorMessages(array $messages) {
		$result = '';
		foreach ( $messages as $form ) {
			foreach ( $form as $element => $message ) {
				if (is_array( $message )) {
					foreach ( $message as $test => $error ) {
						$result .= '<strong>' . $element . '</strong>: ' . $test . ' ' . $error . '; ';
					}
				} else {
					$result .= '<strong>' . $element . '</strong>: ' . $message . '; ';
				}
			}
		}
		return substr( $result, 0, -2 );
	}

	/**
	 * Processes an email activity by uploading any attachments and performing
	 * any send request. Returns an empty string on success and an error message on failure.
	 *
	 * @param EmailInteraction $ei
	 * @param ActivityForm $form
	 * @param array $postData
	 * @return string
	 */
	private function processMail(EmailInteraction $ei, ActivityForm $form, array $postData) {
		/* @var $response1 UploadActivityMimeAttachmentResponse */
		/* @var $response2 SendEmailResponse */
		
		$result = '';
		
		$ei->setStatus( EmailStatus::PENDINGSEND );
		
		// Process any file uploads
		$mar = new UploadActivityMimeAttachmentRequest();
		$mar->setActivity( $ei );
		$mar->setForm( $form );
		$mar->setPostData( $postData );
		
		$service = $this->getService();
		$response1 = $service->execute( $mar );
		if ($response1->getResult()) {
			$ei->setStatus( EmailStatus::SENDING );
			
			// Process any send request
			$send = new SendEmailRequest();
			$send->setActivity( $ei );
			$send->setIssueSend( $ei->getIssueSend() );
			
			$response2 = $service->execute( $send );
			if ($response2->getResult()) {
				$ei->setStatus( EmailStatus::SENT );
			} else {
				$ei->setStatus( EmailStatus::FAILED );
				$result = sprintf( 'Email error: %s', $response2->getMessage() );
			}
		} else {
			$result = $response1->getMessage();
		}
		
		return $result;
	}

	/**
	 * Sets additional view paramters for a CampaignActivity
	 *
	 * @param CampaignActivity $activity
	 * @param ViewModel $view
	 * @param boolean $createDistributionForm
	 * @return ViewModel
	 */
	private function setCampaignActivityViewParameters(CampaignActivity $activity, ViewModel $view,
			$createDistributionForm = false) {
		$service = $this->getService();
		
		// Create the marketing list query form
		$queryForm = new MarketingListForm( $service->getEntityManager() );
		$queryForm->bind( new MarketingList() );
		
		// Create the distribution form
		$df = null;
		if ($createDistributionForm) {
			$channelType = ($activity->getChannelType() != null ? $activity->getChannelType() : ChannelType::PHONE);
			if ($channelType == ChannelType::APPOINTMENT) {
				$channel = 'MeetingAppointment';
			} else {
				$channel = $channelType . 'Interaction';
			}
			$dfClazz = 'Application\Model\\' . $channel;
			$df = new ActivityForm( $service->getEntityManager(), $dfClazz );
			$rc = new \ReflectionClass( $dfClazz );
			$object = $rc->newInstance();
			$df->bind( $object );
			$this->initializeFormValues( $df, $object, null, 0 );
			
			if ($object instanceof EmailInteraction) {
				// Set the body of the email to the parent campaign offer
				$afs = $df->get( EmailFieldset::FIELDSETNAME );
				$afs->get( EmailFieldset::DESCRIPTION )->setValue( $activity->getCampaign()
					->getObjective() );
			}
		}
		
		// Create the other view parameters
		$closedActivities = $activity->getClosedActivities();
		$openActivities = $activity->getOpenActivities();
		$marketingLists = $activity->getListsAsArray();
		$navigation = $this->activityRibbon( 'activity', $activity->getId() );
		
		$view->setVariables( array(
			'closedActivities' => $closedActivities,
			'indexTabTitle' => $channelType . 's Created',
			'marketingLists' => $marketingLists,
			'navigation' => $navigation,
			'openActivities' => $openActivities,
			'queryForm' => $queryForm
		) );
		
		if ($createDistributionForm) {
			$view->setVariables( array(
				'channel' => $channel,
				'channelType' => $channelType,
				'df' => $df,
				'distributeActivityContainerClass' => 'hidden',
				'pageSubTitle' => $this->constructPageSubTitle( $channel )
			) );
		}
		return $view;
	}

	/**
	 * Returns the parent entity to an activity
	 *
	 * @param string $entityType
	 * @param int $id
	 * @return Contactable
	 */
	private function fetchRegardingObject($entityType, $id) {
		/* @var $target TargetRetrieve */
		/* @var $request RetrieveRequest */
		/* @var $response RetrieveResponse */
		
		$target = null;
		switch ($entityType) {
			case 'account' :
				$target = new TargetRetrieveAccount();
				break;
			case 'contact' :
				$target = new TargetRetrieveContact();
				break;
			case 'lead' :
				$target = new TargetRetrieveLead();
		}
		
		if ($target) {
			$service = $this->getService();
			
			$target->setEntityManager( $service->getEntityManager() );
			$target->setId( $id );
			$retrieveRequest = new RetrieveRequest();
			$retrieveRequest->setTarget( $target );
			$retrieveResponse = $service->retrieve($retrieveRequest);
			return $retrieveResponse->getEntity();
		}
		return null;
	}
}
?>