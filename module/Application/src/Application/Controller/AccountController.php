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
 * @version     SVN $Id: AccountController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

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
use Application\Model\Telephone;

use Application\Form\AccountForm;
use Application\Form\AccountFieldset;
use Application\Form\ActivityForm;
use Application\Form\AddressFieldset;
use Application\Form\AppointmentFieldset;
use Application\Form\BaseActivityFieldset;
use Application\Form\FaxFieldset;
use Application\Form\InteractionFieldset;
use Application\Form\LetterFieldset;
use Application\Form\TaskFieldset;
use Application\Form\TelephoneFieldset;

use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\FindByCriteria;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\TargetCreateAccount;
use Application\Service\TargetDeleteAccount;
use Application\Service\TargetRetrieveAccount;
use Application\Service\TargetRetrieveMultipleAccount;
use Application\Service\TargetUpdateAccount;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;

use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;
use Application\Service\TargetCreateActivity;
use Application\Model\AppointmentStatus;
use Application\Model\TaskStatus;
use Application\Model\EmailInteraction;
use Application\Model\LetterInteraction;
use Application\Model\LetterStatus;
use Application\Model\FaxInteraction;
use Application\Model\FaxStatus;
use Application\Model\TelephoneInteraction;
use Application\Model\TelephoneStatus;
use Application\Model\VisitInteraction;
use Application\Form\TelephoneInteractionFieldset;
use Application\Form\VisitFieldset;
use Application\Service\TargetRetrieveMultipleAccountByPost;

/**
 * Account action controller
 *
 * @package		Application\Controller
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AccountController.php 13 2013-08-05 22:53:55Z  $
 */
class AccountController extends AbstractApplicationController {

	/**
	 * Retrieves a collection of accounts
	 *
	 * @return \Zend\View\Model\ViewModel
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		/* @var $request Request */
		/* @var $response RetrieveMultipleResponse */

		$service = $this->getService();
		$form = new AccountForm($service->getEntityManager());

		// Create receiver
		$request = $this->getRequest();
		if($request->isPost()) {
			$target = new TargetRetrieveMultipleAccountByPost();
			$target->setParams($request->getPost(AccountFieldset::FIELDSETNAME));
		} else {
			$criteria = new FindByCriteria();
			$criteria->setOrderBy(array('name' => 'asc'));
			$target = new TargetRetrieveMultipleAccount();
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
		$statusMessage = $numRecords . ' records retrieved in ' . $elapsedTime . ' seconds';

		$view = new ViewModel(array(
			'form' => $form,
			'pageTitle' => 'Active Accounts',
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage,
		));
		$view->setTemplate('application/account/index');
		return $view;
	}

	/**
	 * Use this message to create an account.
	 * The information that is used to create the entity instance is specified in
	 * the TargetCreateAccount class.
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createAction() {
		/* @var $request Request */

		$service = $this->getService();

		// Create the form
		$form = new AccountForm($service->getEntityManager());

		// Create a new empty entity and bind it to the form
		$account = new Account();
		$form->bind($account);
		
		// Initialize required form values
		$this->initializeFormValues($form);
		$statusMessage = 'Status: New record.';

		// Process a form request
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				// Persist the account and return
				$target = new TargetCreateAccount();
				$target->setEntity($account);
				$create = new CreateRequest();
				$create->setTarget($target);
				
				$success = $service->execute($create);
				if($success->getResult()) {
					$isRedirect = $request->getPost(AccountForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute('account', array('action' => 'index'));
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
			'pageTitle' => 'New Account',
			'statusMessage' => $statusMessage,
		));
		$view->setTemplate('application/account/create');
		return $view;
	}

	/**
	 * Use this message to update an account.
	 * The entity instance to update is specified in the TargetUpdateAccount class.
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		/* @var $response RetrieveResponse */
		/* @var $request Request */
		/* @var $account Account */
		/* @var $success UpdateResponse */
		
		// Fetch and test parameters
		$id = $this->params('id', 0);
		if(empty($id)) {
			return $this->redirect()->toRoute('account', array('action' => 'create'));
		}
		
		$service = $this->getService();

		// Create the form
		$form = new AccountForm($service->getEntityManager());
		
		// Fetch the specified entity and bind it to the form
		$target = new TargetRetrieveAccount();
		$target->setId($id);
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget($target);
		$response = $service->execute($retrieve);
		$account = $response->getEntity();
		$form->bind($account);
		
		// Set form values
		$this->setFormValues($form, $account);
		$statusMessage = 'Status: ' . $account->getState();

		// Process a form request
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				// Persist the account and return
				$target = new TargetUpdateAccount();
				$target->setEntity($account);
				$update = new UpdateRequest();
				$update->setTarget($target);
				
				$success = $service->execute($update);
				if($success->getResult()) {
					$isRedirect = $request->getPost(AccountForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute('account', array('action' => 'index'));
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
		$auditItems = $account->getAuditItems()->getValues();
		$contacts = $account->getContacts();
		$opportunities = $account->getOpportunities();
		$navigation = $this->activityRibbon('account', $id);
		$numAddresses = max($account->getAddresses()->count(), 1);
		$numTelephones = max($account->getTelephones()->count(), 1);
		$closedActivities = $account->getClosedActivities();
		$openActivities = $account->getOpenActivities();

		$view = new ViewModel(array(
			'auditItems' => $auditItems,
			'closedActivities' => $closedActivities,
			'id' => $id,
			'contacts' => $contacts,
			'form' => $form,
			'navigation' => $navigation,
			'numAddresses' => $numAddresses,
			'numTelephones' => $numTelephones,
			'openActivities' => $openActivities,
			'opportunities' => $opportunities,
			'pageTitle' => $account->getName(),
			'statusMessage' => $statusMessage,
		));
		$view->setTemplate('application/account/edit');
		return $view;
	}

	/**
	 * Use this message to delete an account.
	 * The entity instance to delete is specified in the TargetDeleteAccount class.
	 *
	 * @return \Zend\Stdlib\ResponseInterface>
	 */
	public function deleteAction() {
		/* @var $success DeleteResponse */
		
		// Fetch and test parameter
		$id = $this->params('id', 0);
		if(empty($id)) {
			return $this->redirect()->toRoute('account', array('action' => 'index'));
		}
		
		// Create receiver and command objects
		$target = new TargetDeleteAccount();
		$target->setId($id);
		$delete = new DeleteRequest();
		$delete->setTarget($target);
		
		// Invoke the deletion and return
		$service = $this->getService();
		$success = $service->execute($delete);
		return $this->redirect()->toRoute('account', array('action' => 'index'));
	}
	
	/**
	 * Initializes form values for a new account
	 *
	 * @param AccountForm $form
	 */
	private function initializeFormValues(AccountForm $form) {
		/* @var $afs AccountFieldset */
		
		$afs = $form->get(AccountFieldset::FIELDSETNAME);
		$afs->get(AccountFieldset::CATEGORY)->setValue('Standard');
		$afs->get(AccountFieldset::STATE)->setValue('Active');
	}
	
	/**
	 * Sets nonstandard form values for an existing account
	 *
	 * @param AccountForm $form
	 * @param Account $account
	 */
	private function setFormValues(AccountForm $form, Account $account) {
		/* @var $afs AccountFieldset */
		/* @var $fieldset AddressFieldset */
		/* @var $address Address */

		$form->get(AccountForm::SUBMIT)->setAttribute('value', 'Save');
		$form->get(AccountForm::SUBMITCLOSE)->setAttribute('value', 'Save and Close');
		$afs = $form->get(AccountFieldset::FIELDSETNAME);
		
		if($account->getAccountGroup() != null) {
			$afs->get(AccountFieldset::ACCOUNTGROUP)->setValue($account->getAccountGroup()->getId());
		}
		if($account->getOriginatingLead() != null) {
			$afs->get(AccountFieldset::ORIGINATINGLEAD)->setValue($account->getOriginatingLead()->getId());
		}
		if($account->getParentAccount() != null) {
			$afs->get(AccountFieldset::PARENTACCOUNT)->setValue($account->getParentAccount()->getId());
		}
		if($account->getPrimaryContact() != null) {
			$afs->get(AccountFieldset::PRIMARYCONTACT)->setValue($account->getPrimaryContact()->getId());
		}
		if($account->getReferrer() != null) {
			$afs->get(AccountFieldset::REFERRER)->setValue($account->getReferrer()->getId());
		}
		if($account->getOwner() != null) {
			$afs->get(AccountFieldset::OWNER)->setValue($account->getOwner()->getId());
		}
		if($account->getBusinessUnit() != null) {
			$afs->get(AccountFieldset::BUSINESSUNIT)->setValue($account->getBusinessUnit()->getId());
		}
		
		$afs->get(AccountFieldset::DONOTCALL)->setValue($account->getDoNotCall() ? 'true' : 'false');
		$afs->get(AccountFieldset::DONOTEMAIL)->setValue($account->getDoNotEmail() ? 'true' : 'false');
		$afs->get(AccountFieldset::DONOTMAIL)->setValue($account->getDoNotMail() ? 'true' : 'false');

		// Set region values for each address
		$regions = array();
		foreach ($account->getAddresses()->getValues() as $address) {
			$regions[] = ($address->getRegion() ? $address->getRegion()->getId() : 0);
		}
		$i = 0;
		foreach ($afs->get(AccountFieldset::ADDRESSES) as $fieldset) {
			if(isset($regions[$i])) {
				$fieldset->get(AddressFieldset::REGION)->setValue($regions[$i]);
				$i++;
			}
		}
	}
}
?>