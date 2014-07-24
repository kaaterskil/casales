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
 * @version     SVN $Id: LeadController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Controller\AbstractApplicationController;

use Application\Form\ActivityForm;
use Application\Form\AddressFieldset;
use Application\Form\AppointmentFieldset;
use Application\Form\BaseActivityFieldset;
use Application\Form\InteractionFieldset;
use Application\Form\LeadForm;
use Application\Form\LeadFieldset;
use Application\Form\NoteFieldset;
use Application\Form\QualifyLeadForm;
use Application\Form\TaskFieldset;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Application\Model\Address;
use Application\Model\AppointmentStatus;
use Application\Model\Lead;
use Application\Model\LeadStatus;
use Application\Model\Telephone;

use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\FindByCriteria;
use Application\Service\QualifyLeadRequest;
use Application\Service\QualifyLeadResponse;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\Service;
use Application\Service\TargetCreateActivity;
use Application\Service\TargetCreateLead;
use Application\Service\TargetDeleteLead;
use Application\Service\TargetRetrieveLead;
use Application\Service\TargetRetrieveMultipleLead;
use Application\Service\TargetUpdateLead;
use Application\Service\UpdateRequest;

use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\View\Model\ViewModel;
use Application\Model\LeadState;
use Application\Model\InitialContact;

/**
 * Lead action controller
 *
 * @package Application\Controller
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN: $Rev: 13 $
 */
class LeadController extends AbstractApplicationController {

	/**
	 * Retrieves a collection of leads
	 *
	 * @return \Zend\View\Model\ViewModel
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		/* @var $response RetrieveMultipleResponse */
		
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		// Create receiver
		$criteria = new FindByCriteria();
		$criteria->setCriteria(array('owner' => $owner));
		$criteria->setOrderBy(array('status' => 'asc', 'lastName' => 'asc'));
		$target = new TargetRetrieveMultipleLead();
		$target->setCriteria($criteria);
		
		// Create a concrete command
		$request = new RetrieveMultipleRequest();
		$request->setTarget($target);
		
		// Fetch records
		$service = $this->getService();
		$start = microtime(true);
		$response = $service->execute($request);
		$recordSet = $response->getRecordSet();
		$end = microtime(true);
		
		// Compute statistics
		$elapsedTime = $end - $start;
		$numRecords = count($recordSet);
		$statusMessage = sprintf(self::MSG_STATISTICS, $numRecords, $elapsedTime);
		
		$view = new ViewModel( array (
			'pageTitle' => 'Open Leads',
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage,
		) );
		$view->setTemplate( 'application/lead/index' );
		return $view;
	}

	/**
	 * Creates a new lead
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createAction() {
		/* @var $service Service */
		/* @var $request Request */
		
		// Fetch service
		$service = $this->getService();
		
		// Create the form
		$form = new LeadForm( $service->getEntityManager() );
		
		// Create an empty Lead and bind it to the form
		$lead = new Lead();
		$form->bind( $lead );
		$this->initializeFormValues($form);
		
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the lead and return
				$target = new TargetCreateLead();
				$target->setEntity($lead);
				$create = new CreateRequest();
				$create->setTarget($target);
				$success = $service->execute($create);
				if($success->getResult()) {
					$isRedirect = $request->getPost(LeadForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute('lead', array ('action' => 'index'));
					}
					$statusMessage = $success->getMessage();
				} else {
					$statusMessage = sprintf(self::MSG_ERROR_CREATE, $success->getMessage());
				}
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		
		$view = new ViewModel( array (
			'form' => $form,
			'pageTitle' => 'New Lead',
			'statusMessage' => $statusMessage,
		) );
		$view->setTemplate( 'application/lead/create' );
		return $view;
	}

	/**
	 * Updates a lead
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		/* @var $request Request */
		/* @var $lead Lead */
		/* @var $address Address */
		/* @var $telephone Telephone */
		/* @var $response RetrieveResponse */
		
		$service = $this->getService();
		
		// Fetch and test the parameters
		$id = $this->params('id', 0);
		if (empty($id)) {
			return $this->redirect()->toRoute('lead', array ('action' => 'create'));
		}
		
		// Create the forms
		$form = new LeadForm( $service->getEntityManager() );
		$qlf = new QualifyLeadForm();
		
		// Fetch the given entity and bind it to the form
		$target = new TargetRetrieveLead();
		$target->setId($id);
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget($target);
		$response = $service->execute($retrieve);
		$lead = $response->getEntity();
		$form->bind( $lead );
		
		// Set form values
		$this->setFormValues($form, $lead);
		$statusMessage = sprintf(self::MSG_STATUS, $lead->getStatus());
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the lead and return
				$target = new TargetUpdateLead();
				$target->setEntity($lead);
				$update = new UpdateRequest();
				$update->setTarget($target);
				
				$success = $service->execute($update);
				if($success->getResult()) {
					$isRedirect = $request->getPost(LeadForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute( 'lead', array ('action' => 'index') );
					}
					$statusMessage = $success->getMessage();
				} else {
					$statusMessage = sprintf(self::MSG_ERROR_UPDATE, $success->getMessage());
				}
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM;
			}
		}
		
		// Set view parameters
		$auditItems = $lead->getAuditItems()->getValues();
		$navigation = $this->activityRibbon('lead', $id);
		$numAddresses = max($lead->getAddresses()->count(), 1);
		$numTelephones = max($lead->getTelephones()->count(), 1);
		$closedActivities = $lead->getClosedActivities();
		$openActivities = $lead->getOpenActivities();
		$showQualifyLeadButton = ($lead->getState() == LeadState::OPEN ? true : false);
		
		$view = new ViewModel( array (
			'auditItems' => $auditItems,
			'closedActivities' => $closedActivities,
			'id' => $id,
			'form' => $form,
			'navigation' => $navigation,
			'numAddresses' => $numAddresses,
			'numTelephones' => $numTelephones,
			'openActivities' => $openActivities,
			'pageTitle' => $lead->getFullName(),
			'qualifyLeadContainerClass' => 'hidden',
			'qualifyLeadForm' => $qlf,
			'showQualifyLeadButton' => $showQualifyLeadButton,
			'statusMessage' => $statusMessage,
		) );
		$view->setTemplate( 'application/lead/edit' );
		return $view;
	}

	/**
	 * Deletes a lead
	 *
	 * @return \Zend\Stdlib\ResponseInterface>
	 */
	public function deleteAction() {
		/* @var $success DeleteResponse */
		
		// Create the receiver
		$id = $this->params( 'id', 0 );
		if(empty($id)) {
			return $this->redirect()->toRoute('lead', array('action' => 'index'));
		}
		
		// Create the receiver and concrete command
		$target = new TargetDeleteLead();
		$target->setId( $id );
		$delete = new DeleteRequest();
		$delete->setTarget( $target );
		
		// Execute the deletion and return
		$service = $this->getService();
		$success = $service->execute( $delete );
		
		if($success->getResult()) {
			$statusMessage = self::MSG_DELETE_SUCCESS;
		} else {
			$statusMessage = sprintf(self::MSG_ERROR_DELETE, $success->getMessage());
		}
		return $this->redirect()->toRoute( 'lead', array (
			'action' => 'index'
		) );
	}

	/**
	 * Qualifies a lead
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function qualifyAction() {
		/* @var $request Request */
		/* @var $lead Lead */
		/* @var $address Address */
		/* @var $telephone Telephone */
		/* @var $response RetrieveResponse */
		
		$service = $this->getService();
		
		// Fetch and test the parameters
		$id = $this->params('id', 0);
		if (empty($id)) {
			return $this->redirect()->toRoute('lead', array ('action' => 'create'));
		}
		
		// Create the forms
		$form = new LeadForm( $service->getEntityManager() );
		$qlf = new QualifyLeadForm();
		
		// Fetch the given entity and bind it to the form
		$target = new TargetRetrieveLead();
		$target->setId($id);
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget($target);
		$response = $service->execute($retrieve);
		$lead = $response->getEntity();
		$form->bind( $lead );
		
		// Set form values
		$this->setFormValues($form, $lead);
		$statusMessage = sprintf(self::MSG_STATUS, $lead->getStatus());
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$qlf->setData( $request->getPost() );
			if ($qlf->isValid()) {
				$qualify = $qlf->get(QualifyLeadForm::QUALIFY)->getValue();
				if($qualify == LeadState::QUALIFIED) {
					$status = $qlf->get(QualifyLeadForm::QUALIFYSTATUS)->getValue();
				} else {
					$status = $qlf->get(QualifyLeadForm::DISQUALIFYSTATUS)->getValue();
				}
				
				// Create command
				$qlr = new QualifyLeadRequest();
				$qlr->setCreateAccount($qlf->get(QualifyLeadForm::CREATEACCOUNT)->getValue());
				$qlr->setCreateContact($qlf->get(QualifyLeadForm::CREATECONTACT)->getValue());
				$qlr->setCreateOpportunity($qlf->get(QualifyLeadForm::CREATEOPPORTUNITY)->getValue());
				$qlr->setLeadId($id);
				$qlr->setStatus($status);
				
				$success = $service->execute($qlr);
				if($success->getResult() == true) {
					return $this->redirect()->toRoute('lead', array('action' => 'edit', 'id' => $id));
				}
				$statusMessage = sprintf(self::MSG_ERROR_UPDATE, $success->getMessage());
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM;
			}
		}
		
		// Set view parameters
		$auditItems = $lead->getAuditItems()->getValues();
		$navigation = $this->activityRibbon('lead', $id);
		$numAddresses = max($lead->getAddresses()->count(), 1);
		$numTelephones = max($lead->getTelephones()->count(), 1);
		$closedActivities = $lead->getClosedActivities();
		$openActivities = $lead->getOpenActivities();
		
		$view = new ViewModel( array (
			'auditItems' => $auditItems,
			'closedActivities' => $closedActivities,
			'id' => $id,
			'form' => $form,
			'navigation' => $navigation,
			'numAddresses' => $numAddresses,
			'numTelephones' => $numTelephones,
			'openActivities' => $openActivities,
			'pageTitle' => $lead->getFullName(),
			'qualifyLeadForm' => $qlf,
			'statusMessage' => $statusMessage,
			'qualifyLeadContainerClass' => '',
		) );
		$view->setTemplate( 'application/lead/edit' );
		return $view;
	}
	
	/**
	 * Initialize form values for a new lead
	 *
	 * @param LeadForm $form
	 */
	private function initializeFormValues(LeadForm $form) {
		/* @var $lfs LeadFieldset */
		$lfs = $form->get(LeadFieldset::FIELDSETNAME);
		$lfs->get(LeadFieldset::OWNER)->setValue($this->zfcUserAuthentication()->getIdentity()->getId());
		$lfs->get(LeadFieldset::STATUS)->setValue(LeadStatus::NEWLEAD);
		$lfs->get(LeadFieldset::INITIALCONTACT)->setValue(InitialContact::NOTCONTACTED);
	}
	
	/**
	 * @param LeadForm $form
	 * @param Lead $lead
	 */
	private function setFormValues(LeadForm $form, Lead $lead) {
		/* @var $lfs LeadFieldset */
		/* @var $fieldset AddressFieldset */
		
		$form->get( LeadForm::SUBMIT )->setAttribute( 'value', 'Save' );
		$form->get( LeadForm::SUBMITCLOSE )->setAttribute( 'value', 'Save and Close' );
		$lfs = $form->get(LeadFieldset::FIELDSETNAME);
		
		if($lead->getAccount() != null) {
			$lfs->get(LeadFieldset::ACCOUNT)->setValue($lead->getAccount()->getId());
		}
		if($lead->getContact() != null) {
			$lfs->get(LeadFieldset::CONTACT)->setValue($lead->getContact()->getId());
		}
		if($lead->getOpportunity() != null) {
			$lfs->get(LeadFieldset::OPPORTUNITY)->setValue($lead->getOpportunity()->getId());
		}
		if($lead->getOwner() != null) {
			$lfs->get(LeadFieldset::OWNER)->setValue($lead->getOwner()->getId());
		}
		if($lead->getBusinessUnit() != null) {
			$lfs->get(LeadFieldset::BUSINESSUNIT)->setValue($lead->getBusinessUnit()->getId());
		}
		
		$lfs->get(LeadFieldset::CONFIRMINTEREST)->setValue($lead->getConfirmInterest() ? 'true' : 'false');
		$lfs->get(LeadFieldset::DECISIONMAKER)->setValue($lead->getDecisionMaker() ? 'true' : 'false');
		$lfs->get(LeadFieldset::DONOTEMAIL)->setValue($lead->getDoNotEmail() ? 'true' : 'false');
		$lfs->get(LeadFieldset::DONOTMAIL)->setValue($lead->getDoNotMail() ? 'true' : 'false');
		$lfs->get(LeadFieldset::DONOTPHONE)->setValue($lead->getDoNotPhone() ? 'true' : 'false');
		$lfs->get(LeadFieldset::EVALUATEFIT)->setValue($lead->getEvaluateFit() ? 'true' : 'false');
		
		// Set region values for select elements
		$regions = array ();
		foreach ( $lead->getAddresses()->getValues() as $address ) {
			$regions[] = ($address->getRegion() ? $address->getRegion()->getId() : 0);
		}
		$i = 0;
		foreach ($lfs->get(LeadFieldset::ADDRESS) as $fieldset) {
			if(isset($regions[$i])) {
				$fieldset->get(AddressFieldset::REGION)->setValue($regions[$i]);
				$i++;
			}
		}
	}
}
?>