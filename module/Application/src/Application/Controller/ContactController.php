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
 * @version     SVN $Id: ContactController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Form\AccountForm;
use Application\Form\AccountFieldset;
use Application\Form\ActivityForm;
use Application\Form\AddressFieldset;
use Application\Form\AppointmentFieldset;
use Application\Form\BaseActivityFieldset;
use Application\Form\InteractionFieldset;
use Application\Form\TaskFieldset;
use Application\Form\ContactForm;
use Application\Form\ContactFieldset;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractTask;
use Application\Model\Account;
use Application\Model\AccountCategory;
use Application\Model\AccountGroup;
use Application\Model\AccountSource;
use Application\Model\AccountState;
use Application\Model\AccountType;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Application\Model\Address;
use Application\Model\Contact;
use Application\Model\ContactStatus;
use Application\Model\TaskStatus;
use Application\Model\Telephone;

use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\FindByCriteria;
use Application\Service\TargetCreateActivity;
use Application\Service\TargetCreateContact;
use Application\Service\TargetDeleteContact;
use Application\Service\TargetRetrieveContact;
use Application\Service\TargetRetrieveMultipleContact;
use Application\Service\TargetUpdateContact;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveResponse;
use Application\Service\RetrieveRequest;
use Application\Service\Service;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;

use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;
use Application\Service\TargetRetrieveMultipleContactByPost;

/**
 * Contact action controller
 *
 * @package		Application\Controller
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN: $Id: ContactController.php 13 2013-08-05 22:53:55Z  $
 */
class ContactController extends AbstractApplicationController {
	
	/**
	 * Retrieves a collection of contacts
	 *
	 * @return void
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		/* @var $request Request */
		/* @var $response RetrieveMultipleResponse */

		$service = $this->getService();
		$form = new ContactForm($service->getEntityManager());

		// Create receiver
		$request = $this->getRequest();
		if($request->isPost()) {
			$target = new TargetRetrieveMultipleContactByPost();
			$target->setParams($request->getPost(ContactFieldset::FIELDSETNAME));
		} else {
			$criteria = new FindByCriteria();
			$criteria->setCriteria(array('state' => 'Active'));
			$criteria->setOrderBy(array('lastName' => 'asc'));
			$target = new TargetRetrieveMultipleContact();
			$target->setCriteria($criteria);
		}
		
		// Create command
		$request = new RetrieveMultipleRequest();
		$request->setTarget($target);
		
		// Fetch records
		$start = microtime(true);
		$response = $service->execute($request);
		$recordSet = $response->getRecordSet();
		$end = microtime(true);
		
		$elapsedTime = $end - $start;
		$numRecords = count($recordSet);
		$statusMessage = sprintf(self::MSG_STATISTICS, $numRecords, $elapsedTime);

		$view = new ViewModel(array(
			'form' => $form,
			'pageTitle' => 'Active Contacts',
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage,
		));
		$view->setTemplate('application/contact/index');
		return $view;
	}

	/**
	 * Creates a contact
	 *
	 * @return ViewModel
	 */
	public function createAction() {
		/* @var $service Service */
		/* @var $request Request */
		/* @var $success CreateResponse */
		
		// Fetch service
		$service = $this->getService();

		// Create the form
		$form = new ContactForm($service->getEntityManager());

		// Create a new empty contact and bind it to the form
		$contact = new Contact();
		$form->bind($contact);
		
		// Set form values
		$this->initializeFormValues($form);
		$statusMessage = 'Status: New record.';

		// Process a form request
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				
				// Persist the contact and return
				$target = new TargetCreateContact();
				$target->setEntity($contact);
				$create = new CreateRequest();
				$create->setTarget($target);
				$success = $service->execute($create);
				if($success->getResult()) {
					$isRedirect = $request->getPost(ContactForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute('contact', array('action' => 'index'));
					}
					$statusMessage = $success->getMessage();
				} else {
					$statusMessage = 'The record could not be created: ' . $success->getMessage();
				}
			} else {
				$statusMessage = 'The record has invalid form values and could not be saved.';
			}
		}

		$view = new ViewModel(array(
			'form' => $form,
			'pageTitle' => 'New Contact',
			'statusMessage' => $statusMessage,
		));
		$view->setTemplate('application/contact/create');
		return $view;
	}

	/**
	 * Updates a contact
	 *
	 * @return ViewModel
	 */
	public function editAction() {
		/* @var $request Request */
		/* @var $response RetrieveResponse */
		/* @var $contact Contact */
		
		// Fetch and test parameters
		$id = (int) $this->params('id', 0);
		if(empty($id)) {
			return $this->redirect()->toRoute('contact', array('action' => 'create'));
		}
		
		$service = $this->getService();

		// Create the forms
		$form = new ContactForm($service->getEntityManager());

		// Fetch the specified entity and bind it to the form
		$target = new TargetRetrieveContact();
		$target->setId($id);
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget($target);
		$response = $service->execute($retrieve);
		$contact = $response->getEntity();
		$form->bind($contact);
		
		// Set form values
		$this->setFormValues($form, $contact);
		$statusMessage = 'Status: ' . $contact->getState();

		// Process a form request
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				// Persist the contact and return
				$target = new TargetUpdateContact();
				$target->setEntity($contact);
				$update = new UpdateRequest();
				$update->setTarget($target);
				
				$success = $service->execute($update);
				if($success->getResult()) {
					$isRedirect = $request->getPost(ContactForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute('contact', array('action' => 'index'));
					}
					$statusMessage = $success->getMessage();
				} else {
					$statusMessage = 'The record could not be updated: ' . $success->getMessage();
				}
			} else {
				$statusMessage = 'The record has invalid form values and could not be updated.';
			}
		}
		
		// Fetch view parameters
		$auditItems = $contact->getAuditItems()->getValues();
		$navigation = $this->activityRibbon('contact', $id);
		$numAddresses = max($contact->getAddresses()->count(), 1);
		$numTelephones = max($contact->getTelephones()->count(), 1);
		$closedActivities = $contact->getClosedActivities();
		$openActivities = $contact->getOpenActivities();

		// Create and return a View Model
		$view = new ViewModel(array(
			'auditItems' => $auditItems,
			'closedActivities' => $closedActivities,
			'id' => $id,
			'form' => $form,
			'navigation' => $navigation,
			'numAddresses' => $numAddresses,
			'numTelephones' => $numTelephones,
			'openActivities' => $openActivities,
			'pageTitle' => $contact->getDisplayName(),
			'statusMessage' => $statusMessage,
		));
		$view->setTemplate('application/contact/edit');
		return $view;
	
	}

	/**
	 * Deletes a contact
	 *
	 * @return void
	 */
	public function deleteAction() {
		// Fetch and test the parameters
		$id = $this->params('id', 0);
		if($id == 0) {
			return $this->redirect()->toRoute('contact');
		}
		
		// Create/fetch the receiver, command and invoker
		$target = new TargetDeleteContact();
		$target->setId($id);
		$delete = new DeleteRequest();
		$delete->setTarget($target);
		$service = $this->getService();
		
		// Remove the contact and return
		$success = $service->execute($delete);
		return $this->redirect()->toRoute('contact');
	}
	
	/**
	 * Initializes form values for a new contact
	 *
	 * @param ContactForm $form
	 */
	private function initializeFormValues(ContactForm $form) {
		/* @var $cfs ContactFieldset */
		
		$cfs = $form->get(ContactFieldset::FIELDSETNAME);
		$cfs->get(ContactFieldset::STATUS)->setValue(ContactStatus::ACTIVE);
	}
	
	/**
	 * Sets nonstandard form values for an existing contact
	 *
	 * @param ContactForm $form
	 * @param Contact $contact
	 */
	private function setFormValues(ContactForm $form, Contact $contact) {
		/* @var $cfs ContactFieldset */
		/* @var $fieldset AddressFieldset */
		/* @var $address Address */
		
		$form->get(ContactForm::SUBMIT)->setAttribute('value', 'Save');
		$form->get(ContactForm::SUBMITCLOSE)->setAttribute('value', 'Save and Close');
		$cfs = $form->get(ContactFieldset::FIELDSETNAME);
		if($contact->getAccount() != null) {
			$cfs->get(ContactFieldset::ACCOUNT)->setValue($contact->getAccount()->getId());
		}
		if($contact->getOwner() != null) {
			$cfs->get(ContactFieldset::OWNER)->setValue($contact->getOwner()->getId());
		}
		if($contact->getBusinessUnit() != null) {
			$cfs->get(ContactFieldset::BUSINESSUNIT)->setValue($contact->getBusinessUnit()->getId());
		}
		
		// Set region values for each address
		$regions = array();
		foreach ($contact->getAddresses()->getValues() as $address) {
			$regions[] = ($address->getRegion() ? $address->getRegion()->getId() : 0);
		}
		$i = 0;
		foreach ($cfs->get(ContactFieldset::ADDRESS) as $fieldset) {
			if(isset($regions[$i])) {
				$fieldset->get(AddressFieldset::REGION)->setValue($regions[$i]);
				$i++;
			}
		}
	}
}
?>