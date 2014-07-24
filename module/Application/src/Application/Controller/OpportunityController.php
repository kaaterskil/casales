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
 * @version     SVN $Id: OpportunityController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;
use Application\Model\Account;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Application\Model\Address;
use Application\Model\AppointmentStatus;
use Application\Model\Contact;
use Application\Model\FaxInteraction;
use Application\Model\InitialContact;
use Application\Model\Lead;
use Application\Model\LetterInteraction;
use Application\Model\Opportunity;
use Application\Model\OpportunityClose;
use Application\Model\OpportunityPriority;
use Application\Model\OpportunityState;
use Application\Model\OpportunityStatus;
use Application\Model\OpportunityTimeline;
use Application\Model\PurchaseProcess;
use Application\Model\PurchaseTimeframe;
use Application\Model\SalesStage;
use Application\Model\TaskStatus;
use Application\Model\Telephone;
use Application\Model\TelephoneInteraction;
use Application\Model\VisitInteraction;

use Application\Form\ActivityForm;
use Application\Form\BaseActivityFieldset;
use Application\Form\AppointmentFieldset;
use Application\Form\InteractionFieldset;
use Application\Form\FaxFieldset;
use Application\Form\LetterFieldset;
use Application\Form\LoseOpportunityForm;
use Application\Form\NoteFieldset;
use Application\Form\TaskFieldset;
use Application\Form\OpportunityForm;
use Application\Form\OpportunityFieldset;
use Application\Form\TelephoneFieldset;
use Application\Form\VisitFieldset;
use Application\Form\WinOpportunityForm;

use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\FindByCriteria;
use Application\Service\LoseOpportunityRequest;
use Application\Service\LoseOpportunityResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\TargetCreateActivity;
use Application\Service\TargetCreateOpportunity;
use Application\Service\TargetDeleteOpportunity;
use Application\Service\TargetRetrieveOpportunity;
use Application\Service\TargetRetrieveMultipleOpportunity;
use Application\Service\TargetUpdateOpportunity;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;
use Application\Service\WinOpportunityRequest;
use Application\Service\WinOpportunityResponse;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;
use Application\Service\TargetRetrieveMultipleByQuery;

/**
 * Opportunity action controller
 *
 * @package Application\Controller
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN: $Rev: 13 $
 */
class OpportunityController extends AbstractApplicationController {
	
	/**
	 * Default action
	 *
	 * @return \Zend\View\Model\ViewModel
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		/* @var $response RetrieveMultipleResponse */
		
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		// Create receiver
		$criteria = new FindByCriteria();
		$criteria->setCriteria(array('state' => OpportunityState::OPEN, 'owner' => $owner));
		$criteria->setOrderBy(array('estimatedCloseDate' => 'asc'));
		$target = new TargetRetrieveMultipleOpportunity();
		$target->setCriteria($criteria);
		
		// Create command
		$request = new RetrieveMultipleRequest();
		$request->setTarget($target);
		
		// Fetch records
		$service = $this->getService();
		$start = microtime(true);
		$response = $service->execute($request);
		$recordSet = $response->getRecordSet();
		$end = microtime(true);
		
		$elapsedTime = $end - $start;
		$numRecords = count($recordSet);
		$statusMessage = sprintf(self::MSG_STATISTICS, $numRecords, $elapsedTime);
		
		$view = new ViewModel(array(
			'recordSet' => $recordSet,
			'pageTitle' => 'Open Opportunities',
			'statusMessage' => $statusMessage,
		));
		$view->setTemplate('application/opportunity/index');
		return $view;
	}
	
	public function closedIndexAction() {
		/* @var $response RetrieveMultipleResponse */
		
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		$dql = "select o
				from Application\Model\Opportunity o
				where (o.state='" . OpportunityState::LOST . "'
					or o.state='" . OpportunityState::WON . "')
					and o.owner=" . $owner->getId() . "
				order by o.actualCloseDate desc";
		
		// Create receiver
		$target = new TargetRetrieveMultipleByQuery();
		$target->setDQL($dql);
		
		// Create command
		$request = new RetrieveMultipleRequest();
		$request->setTarget($target);
		
		// Fetch records
		$service = $this->getService();
		$start = microtime(true);
		$response = $service->execute($request);
		$recordSet = $response->getRecordSet();
		$end = microtime(true);
		
		$elapsedTime = $end - $start;
		$numRecords = count($recordSet);
		$statusMessage = sprintf(self::MSG_STATISTICS, $numRecords, $elapsedTime);
		
		$view = new ViewModel(array(
			'recordSet' => $recordSet,
			'pageTitle' => 'Closed Opportunities',
			'statusMessage' => $statusMessage,
		));
		$view->setTemplate('application/opportunity/closedIndex');
		return $view;
	}
	
	/**
	 * Creates a new Opportunity
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createAction() {
		/* @var $request Request */
		/* @var $success CreateResponse */
		
		$service = $this->getService();
		
		// Create the form
		$form = new OpportunityForm($service->getEntityManager());
		
		// Create a new empty entity and bind it to the form
		$opportunity = new Opportunity();
		$form->bind($opportunity);
		
		// Initialize required form values
		$this->initializeFormValues($form);
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Process a form request
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				// Persist the opportunity and return
				$target = new TargetCreateOpportunity();
				$target->setEntity($opportunity);
				$create = new CreateRequest();
				$create->setTarget($target);
				
				$success = $service->execute($create);
				if($success->getResult()) {
					$isRedirect = $request->getPost(OpportunityForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute('opportunity', array('action' => 'index'));
					}
					$statusMessage = $success->getMessage();
				} else {
					$statusMessage = sprintf(self::MSG_ERROR_CREATE, $success->getMessage());
				}
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		
		$view = new ViewModel(array(
			'form' => $form,
			'pageTitle' => 'New Opportunity',
			'statusMessage' => $statusMessage,
		));
		$view->setTemplate('application/opportunity/create');
		return $view;
	}
	
	/**
	 * Edits/Updates an Opportunity
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		/* @var $opportunity Opportunity */
		/* @var $request Request */
		/* @var $response RetrieveResponse */
		/* @var $success UpdateResponse */
		
		// Fetch and test parameters
		$id = $this->params('id', 0);
		if(empty($id)) {
			return $this->redirect()->toRoute('opportunity', array('action' => 'create'));
		}
		
		$service = $this->getService();
		$form = new OpportunityForm($service->getEntityManager());
		$wof = new WinOpportunityForm();
		$lof = new LoseOpportunityForm();
		
		// Fetch the specified entity and bind it to the form
		$target = new TargetRetrieveOpportunity();
		$target->setId($id);
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget($target);
		$response = $service->execute($retrieve);
		$opportunity = $response->getEntity();
		$form->bind($opportunity);
		
		// Set form values
		$this->setFormValues($form, $opportunity);
		$statusMessage = sprintf(self::MSG_STATUS, $opportunity->getStatus());
		
		// Process a request
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				// Persist opportunity and return
				$target = new TargetUpdateOpportunity();
				$target->setEntity($opportunity);
				$update = new UpdateRequest();
				$update->setTarget($target);
				
				$success = $service->execute($update);
				if($success->getResult()) {
					$isRedirect = $request->getPost(OpportunityForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute('opportunity', array('action' => 'index'));
					}
					$statusMessage = $success->getMessage();
				} else {
					$statusMessage = sprintf(self::MSG_ERROR_UPDATE, $success->getMessage());
				}
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM;
			}
		}
		
		// Fetch view parameters
		$auditItems = $opportunity->getAuditItems()->getValues();
		$navigation = $this->activityRibbon('opportunity', $id);
		$closedActivities = $opportunity->getClosedActivities();
		$openActivities = $opportunity->getOpenActivities();
		
		$view = new ViewModel(array(
			'auditItems' => $auditItems,
			'closedActivities' => $closedActivities,
			'id' => $id,
			'form' => $form,
			'loseOpportunityContainerClass' => 'hidden',
			'loseOpportunityForm' => $lof,
			'navigation' => $navigation,
			'openActivities' => $openActivities,
			'opportunity' => $opportunity,
			'pageTitle' => $opportunity->getName(),
			'statusMessage' => $statusMessage,
			'winOpportunityContainerClass' => 'hidden',
			'winOpportunityForm' => $wof,
		));
		$view->setTemplate('application/opportunity/edit');
		return $view;
	}
	
	/**
	 * Deletes an Opportunity
	 *
	 * @return void
	 */
	public function deleteAction() {
		// Fetch and test parameter
		$id = $this->params('id', 0);
		if($id == 0) {
			return $this->redirect()->toRoute('opportunity', array('action' => 'index'));
		}
		
		// Create receiver and command objects
		$target = new TargetDeleteOpportunity();
		$target->setId($id);
		$delete = new DeleteRequest();
		$delete->setTarget($target);
		
		$service = $this->getService();
		$success = $service->execute($delete);
		return $this->redirect()->toRoute('opportunity', array('action' => 'index'));
	}
	
	public function loseOpportunityAction() {
		/* @var $request Request */
		/* @var $response RetrieveResponse */
		/* @var $opportunity Opportunity */
		/* @var $success LoseOpportunityResponse */
		
		$service = $this->getService();
		
		$id = $this->params('id', 0);
		if($id == 0) {
			return $this->redirect()->toRoute('opportunity', array('action' => 'index'));
		}
		
		// Create the main form
		$form = new OpportunityForm($service->getEntityManager());
		$wof = new WinOpportunityForm();
		
		// Fetch the opportunity and bind it to the form
		$target = new TargetRetrieveOpportunity();
		$target->setId($id);
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget($target);
		$response = $service->execute($retrieve);
		$opportunity = $response->getEntity();
		$form->bind($opportunity);
		
		// Set form values
		$this->setFormValues($form, $opportunity);
		$statusMessage = sprintf(self::MSG_STATUS, $opportunity->getStatus());
		
		// Create the close form, and bind an empty activity to it
		$lof = new LoseOpportunityForm();
		$activity = new OpportunityClose();
		$lof->bind($activity);
		
		// Process a request
		$request = $this->getRequest();
		if($request->isPost()) {
			$lof->setData($request->getPost());
			if($lof->isValid()) {
				// Create the command
				$lose = new LoseOpportunityRequest();
				$lose->setOpportunityId($id);
				$lose->setOpportunityClose($activity);
				$lose->setStatus($lof->get(LoseOpportunityForm::STATUS)->getValue());
				
				// Execute the command and return
				$success = $service->execute($lose);
				if($success->getResult()) {
					return $this->redirect()->toRoute('opportunity', array('id' => $id));
				}
				$statusMessage = sprintf(self::MSG_ERROR_UPDATE, $success->getMessage());
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM;
			}
		}
		
		// Fetch view parameters
		$auditItems = $opportunity->getAuditItems()->getValues();
		$navigation = $this->activityRibbon('opportunity', $id);
		$closedActivities = $opportunity->getClosedActivities();
		$openActivities = $opportunity->getOpenActivities();
		
		$view = new ViewModel(array(
			'auditItems' => $auditItems,
			'closedActivities' => $closedActivities,
			'id' => $id,
			'form' => $form,
			'loseOpportunityContainerClass' => '',
			'loseOpportunityForm' => $lof,
			'navigation' => $navigation,
			'openActivities' => $openActivities,
			'opportunity' => $opportunity,
			'pageTitle' => $opportunity->getName(),
			'statusMessage' => $statusMessage,
			'winOpportunityContainerClass' => 'hidden',
			'winOpportunityForm' => $wof,
		));
		$view->setTemplate('application/opportunity/edit');
		return $view;
	}
	
	public function winOpportunityAction() {
		/* @var $request Request */
		/* @var $response RetrieveResponse */
		/* @var $opportunity Opportunity */
		/* @var $success WinOpportunityResponse */
		
		$service = $this->getService();
		
		$id = $this->params('id', 0);
		if($id == 0) {
			return $this->redirect()->toRoute('opportunity', array('action' => 'index'));
		}
		
		// Create the main form
		$form = new OpportunityForm($service->getEntityManager());
		$lof = new LoseOpportunityForm();
		
		// Fetch the opportunity and bind it to the form
		$target = new TargetRetrieveOpportunity();
		$target->setId($id);
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget($target);
		$response = $service->execute($retrieve);
		$opportunity = $response->getEntity();
		$form->bind($opportunity);
		
		// Set form values
		$this->setFormValues($form, $opportunity);
		$statusMessage = sprintf(self::MSG_STATUS, $opportunity->getStatus());
		
		// Create the win form, and bind an empty activity to it
		$wof = new WinOpportunityForm();
		$activity = new OpportunityClose();
		$wof->bind($activity);
		
		// Process a request
		$request = $this->getRequest();
		if($request->isPost()) {
			$wof->setData($request->getPost());
			if($wof->isValid()) {
				// Create the command
				$win = new WinOpportunityRequest();
				$win->setOpportunityId($id);
				$win->setOpportunityClose($activity);
				$win->setStatus(OpportunityStatus::WON);
				
				// Execute the command and return
				$success = $service->execute($win);
				if($success->getResult()) {
					return $this->redirect()->toRoute('opportunity', array('id' => $id));
				}
				$statusMessage = sprintf(self::MSG_ERROR_UPDATE, $success->getMessage());
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM;
			}
		}
		
		// Fetch view parameters
		$auditItems = $opportunity->getAuditItems()->getValues();
		$navigation = $this->activityRibbon('opportunity', $id);
		$closedActivities = $opportunity->getClosedActivities();
		$openActivities = $opportunity->getOpenActivities();
		
		$view = new ViewModel(array(
			'auditItems' => $auditItems,
			'closedActivities' => $closedActivities,
			'id' => $id,
			'form' => $form,
			'loseOpportunityForm' => $lof,
			'loseOpportunityContainerClass' => 'hidden',
			'navigation' => $navigation,
			'openActivities' => $openActivities,
			'opportunity' => $opportunity,
			'pageTitle' => $opportunity->getName(),
			'statusMessage' => $statusMessage,
			'winOpportunityForm' => $wof,
			'winOpportunityContainerClass' => '',
		));
		$view->setTemplate('application/opportunity/edit');
		return $view;
	}
	
	/**
	 * Sets initial values for a new Opportunity
	 *
	 * @param OpportunityForm $form
	 */
	private function initializeFormValues(OpportunityForm $form) {
		/* @var $ofs OpportunityFieldset */
		
		$ofs = $form->get(OpportunityFieldset::FIELDSETNAME);
		$ofs->get(OpportunityFieldset::INITIALCONTACT)->setValue(InitialContact::NOTCONTACTED);
		$ofs->get(OpportunityFieldset::PURCHASEPROCESS)->setValue(PurchaseProcess::UNKNOWN);
		$ofs->get(OpportunityFieldset::PURCHASETIMEFRAME)->setValue(PurchaseTimeframe::UNKNOWN);
		$ofs->get(OpportunityFieldset::SALESSTAGE)->setValue(SalesStage::QUALIFY);
		$ofs->get(OpportunityFieldset::STATUS)->setValue(OpportunityStatus::NEWOPPORTUNITY);
		$ofs->get(OpportunityFieldset::TIMELINE)->setValue(OpportunityTimeline::UNKNOWN);
	}
	
	/**
	 * Sets nonstandard form values
	 *
	 * @param OpportunityForm $form
	 * @param Opportunity $opportunity
	 */
	private function setFormValues(OpportunityForm $form, Opportunity $opportunity) {
		/* @var $ofs OpportunityFieldset */
		
		$form->get(OpportunityForm::SUBMIT)->setValue('Save');
		$form->get(OpportunityForm::SUBMITCLOSE)->setValue('Save and Close');
		$ofs = $form->get(OpportunityFieldset::FIELDSETNAME);
		
		// Set One-to-Many values
		if($opportunity->getAccount() != null) {
			$ofs->get(OpportunityFieldset::ACCOUNT)->setValue($opportunity->getAccount()->getId());
		}
		if($opportunity->getContact() != null) {
			$ofs->get(OpportunityFieldset::CONTACT)->setValue($opportunity->getContact()->getId());
		}
		if($opportunity->getOriginatingLead() != null) {
			$ofs->get(OpportunityFieldset::ORIGINATINGLEAD)->setValue($opportunity->getOriginatingLead()->getId());
		}
		if($opportunity->getOwner() != null) {
			$ofs->get(OpportunityFieldset::OWNER)->setValue($opportunity->getOwner()->getId());
		}
		if($opportunity->getBusinessUnit() != null) {
			$ofs->get(OpportunityFieldset::BUSINESSUNIT)->setValue($opportunity->getBusinessUnit()->getId());
		}
		
		// Set boolean values
		$ofs->get(OpportunityFieldset::DECISIONMAKER)->setValue($opportunity->getDecisionMaker() ? 'true' : 'false');
		$ofs->get(OpportunityFieldset::DEVELOPPROPOSAL)->setValue($opportunity->getDevelopProposal() ? 'true' : 'false');
		$ofs->get(OpportunityFieldset::EVALUATEFIT)->setValue($opportunity->getEvaluateFit() ? 'true' : 'false');
		$ofs->get(OpportunityFieldset::PRESENTPROPOSAL)->setValue($opportunity->getPresentProposal() ? 'true' : 'false');
		$ofs->get(OpportunityFieldset::PURSUITDECISION)->setValue($opportunity->getPursuitDecision() ? 'true' : 'false');
		$ofs->get(OpportunityFieldset::SENDTHANKYOU)->setValue($opportunity->getSendThankYou() ? 'true' : 'false');

		// Set dates in proper format
		$ofs->get(OpportunityFieldset::ESTIMATEDCLOSEDATE)->setValue($opportunity->getFormattedEstimatedCloseDate());
		$ofs->get(OpportunityFieldset::ACTUALCLOSEDATE)->setValue($opportunity->getFormattedActualCloseDate());
		$ofs->get(OpportunityFieldset::FINALDECISIONDATE)->setValue($opportunity->getFormattedFinalDecisionDate());
		$ofs->get(OpportunityFieldset::SCHEDULEFOLLOWUPPROSPECT)->setValue($opportunity->getFormattedScheduleFollowupProspect());
		$ofs->get(OpportunityFieldset::SCHEDULEFOLLOWUPQUALIFY)->setValue($opportunity->getFormattedScheduleFollowupQualify());
		$ofs->get(OpportunityFieldset::SCHEDULEPROPOSALMEETING)->setValue($opportunity->getFormattedScheduleProposalMeeting());
	}
}
?>