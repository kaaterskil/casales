<?php

/**
 * Casales Library
 *
 * PHP version 5.4
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
 * KAATERSKIL MANAGEMENT, LLC BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Casales
 * @package     Application\Service
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: QualifyLeadRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\Account;
use Application\Model\Address;
use Application\Model\Contact;
use Application\Model\Lead;
use Application\Model\LeadStatus;
use Application\Model\Opportunity;
use Application\Service\QualifyLeadResponse;
use Application\Service\Request;
use Application\Service\Response;
use Doctrine\ORM\EntityManager;
use Application\Model\AccountState;
use Application\Model\AccountStatus;
use Application\Model\ContactState;
use Application\Model\ContactStatus;
use Application\Model\OpportunityState;
use Application\Model\OpportunityStatus;
use Application\Model\LeadState;
use Application\Model\Telephone;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Contains the data that is needed to qualify a lead and create account, contact, and
 * opportunity records that are linked to the originating lead record.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: QualifyLeadRequest.php 13 2013-08-05 22:53:55Z  $
 */
class QualifyLeadRequest extends Request {

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var boolean
	 */
	private $createAccount = false;

	/**
	 * @var boolean
	 */
	private $createContact = false;

	/**
	 * @var boolean
	 */
	private $createOpportunity = false;

	/**
	 * @var int
	 */
	private $leadId;

	/**
	 * @var LeadStatus
	 */
	private $status;

	public function __construct(EntityManager $em = null) {
		parent::__construct( 'QualifyLeadRequest' );
		$this->em = $em;
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager() {
		return $this->em;
	}

	/**
	 * @param EntityManager $em
	 */
	public function setEntityManager(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return boolean
	 */
	public function getCreateAccount() {
		return $this->createAccount;
	}

	/**
	 * @param boolean $createAccount
	 */
	public function setCreateAccount($createAccount) {
		if(is_bool($createAccount) || is_numeric($createAccount)) {
			$this->createAccount = (bool) $createAccount;
		} else {
			$this->createAccount = ($createAccount == 'true' ? true : false);
		}
	}

	/**
	 * @return boolean
	 */
	public function getCreateContact() {
		return $this->createContact;
	}

	/**
	 * @param boolean $createContact
	 */
	public function setCreateContact($createContact) {
		if(is_bool($createContact) || is_numeric($createContact)) {
			$this->createContact = (bool) $createContact;
		} else {
			$this->createContact = ($createContact == 'true' ? true : false);
		}
	}

	/**
	 * @return boolean
	 */
	public function getCreateOpportunity() {
		return $this->createOpportunity;
	}

	/**
	 * @param boolean $createOpportunity
	 */
	public function setCreateOpportunity($createOpportunity) {
		if(is_bool($createOpportunity) || is_numeric($createOpportunity)) {
			$this->createOpportunity = (bool) $createOpportunity;
		} else {
			$this->createOpportunity = ($createOpportunity == 'true' ? true : false);
		}
	}

	/**
	 * @return int
	 */
	public function getLeadId() {
		return $this->leadId;
	}

	/**
	 * @param int $id
	 */
	public function setLeadId($id) {
		$this->leadId = (int) $id;
	}

	/**
	 * @return \Application\Model\LeadStatus
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param LeadStatus|string $status
	 */
	public function setStatus($status = null) {
		if($status instanceof LeadStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = LeadStatus::instance($status);
		} else {
			$this->status = null;
		}
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\QualifyLeadResponse
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		/* @var $entity Lead */
		
		$response = new QualifyLeadResponse();
		$response->setResult(false);
		
		if ($this->getEntityManager() == null) {
			$response->setMessage('EntityManager not found.');
			return $response;
		}
		if ($this->getLeadId() == null || $this->getLeadId() < 1) {
			$response->setMessage('The Lead has not yet been saved.');
			return $response;
		}
		if ($this->getStatus() == null) {
			$response->setMessage('Lead status is required.');
			return $response;
		}
		
		$em = $this->getEntityManager();
		$entity = $em->getRepository( 'Application\model\Lead' )->find( $this->getLeadId() );
		if ($entity) {
			try {
				// Set the lead's status
				$entity = $this->prepareLead($entity);
				
				// Create other entities
				if($entity->getState()->getName() != LeadState::DISQUALIFIED) {
					if ($this->createAccount) {
						$account = $this->copyLeadToAccount( $entity );
						$entity->setAccount( $account );
						$em->persist( $account );
					}
					if ($this->createContact) {
						$contact = $this->copyLeadToContact( $entity );
						$entity->setContact( $contact );
						$em->persist( $contact );
					}
					if ($this->createOpportunity) {
						$opportunity = $this->copyLeadToOpportunity( $entity );
						$entity->setOpportunity( $opportunity );
						$em->persist( $opportunity );
					}
				}
				
				// Persist the lead and flush the entity manager
				$em->persist( $entity );
				$em->flush();
				
				$response->setMessage('Qualification succeeded.');
				$response->setResult( true );
			} catch ( Exception $e ) {
				$response->setMessage('Qualification failed: ' . $e->getMessage());
			}
		} else {
			$response->setMessage('The Lead was not found.');
		}
		return $response;
	}
	
	private function prepareLead(Lead $lead) {
		$now = new \DateTime();
		$status = $this->getStatus()->getName();
		
		// Set state
		$lead->setStatus($status);
		switch ($status) {
			case LeadStatus::CANCELED:
				$lead->setState(LeadState::DISQUALIFIED);
				break;
			case LeadStatus::CANNOTCONTACT:
				$lead->setState(LeadState::DISQUALIFIED);
				break;
			case LeadStatus::CONTACTED:
				$lead->setState(LeadState::OPEN);
				break;
			case LeadStatus::LOST:
				$lead->setState(LeadState::DISQUALIFIED);
				break;
			case LeadStatus::NEWLEAD:
				$lead->setState(LeadState::OPEN);
				break;
			case LeadStatus::NOTINTERESTED:
				$lead->setState(LeadState::DISQUALIFIED);
				break;
			case LeadStatus::QUALIFIED:
				$lead->setState(LeadState::QUALIFIED);
				break;
		}
		
		$lead->setLastUpdateDate($now);
		return $lead;
	}

	/**
	 * @param Lead $lead
	 * @return \Application\Model\Account
	 */
	private function copyLeadToAccount(Lead $lead) {
		$now = new \DateTime();
		$name = $lead->getFullName() != '' ? $lead->getFullName() : $lead->getCompanyName();
		
		$account = new Account();
		$account->setCreationDate( $now );
		$account->setDoNotCall( $lead->getDoNotPhone() );
		$account->setDoNotEmail( $lead->getDoNotEmail() );
		$account->setDoNotMail( $lead->getDoNotMail() );
		$account->setEmail1( $lead->getEmail1() );
		$account->setEmail2( $lead->getEmail2() );
		$account->setLastUpdateDate( $now );
		$account->setName( $name );
		$account->setOriginatingLead( $lead );
		$account->setState( AccountState::ACTIVE );
		$account->setStatus( AccountStatus::ACTIVE );
		$account->setWebsite( $lead->getWebsite() );
		
		foreach ($lead->getAddresses()->getValues() as $address) {
			$clone = clone $address;
			$account->addAddress($clone);
		}
		foreach ($lead->getTelephones()->getValues() as $telephone) {
			$clone = clone $telephone;
			$account->addTelephone($clone);
		}
		
		$lead->setAccount($account);
		
		return $account;
	}

	/**
	 * @param Lead $lead
	 * @return \Application\Model\Contact
	 */
	private function copyLeadToContact(Lead $lead) {
		/* @var $account Account */
		
		$now = new \DateTime();
		
		$contact = new Contact();
		$contact->setAccount( $lead->getAccount() );
		$contact->setCreationDate( $now );
		$contact->setDescription( $lead->getDescription() );
		$contact->setDisplayName( $lead->getFullName() );
		$contact->setDoNotCall( $lead->getDoNotPhone() );
		$contact->setDoNotEmail( $lead->getDoNotEmail() );
		$contact->setDoNotMail( $lead->getDoNotMail() );
		$contact->setEmail1( $lead->getEmail1() );
		$contact->setEmail2( $lead->getEmail2() );
		$contact->setFirstName( $lead->getFirstName() );
		$contact->setJobTitle( $lead->getJobTitle() );
		$contact->setLastName( $lead->getLastName() );
		$contact->setLastUpdateDate( $now );
		$contact->setMiddleName( $lead->getMiddleName() );
		$contact->setOriginatingLead( $lead );
		$contact->setPrefix( $lead->getPrefix() );
		$contact->setSalutation( $lead->getSalutation() );
		$contact->setState( ContactState::ACTIVE );
		$contact->setStatus( ContactStatus::ACTIVE );
		$contact->setSuffix( $lead->getSuffix() );
		$contact->setWebsite( $lead->getWebsite() );
		
		foreach ($lead->getAddresses()->getValues() as $address) {
			$clone = clone $address;
			$contact->addAddress($clone);
		}
		foreach ($lead->getTelephones()->getValues() as $telephone) {
			$clone = clone $telephone;
			$contact->addTelephone($clone);
		}
		
		if($lead->getAccount() != null) {
			$lead->getAccount()->addContact($contact);
			if($lead->getAccount()->getPrimaryContact() == null) {
				$lead->getAccount()->setPrimaryContact($contact);
				$contact->setIsPrimaryContact(true);
			}
		}
		
		$lead->setContact($contact);
		
		return $contact;
	}

	/**
	 * @param Lead $lead
	 * @return \Application\Model\Opportunity
	 */
	private function copyLeadToOpportunity(Lead $lead) {
		$now = new \DateTime();
		$name = $lead->getFullName() != '' ? $lead->getFullName() : $lead->getCompanyName();
		
		$opportunity = new Opportunity();
		$opportunity->setAccount( $lead->getAccount() );
		$opportunity->setContact( $lead->getContact() );
		$opportunity->setConfirmInterest( $lead->getConfirmInterest() );
		$opportunity->setCreationDate( $now );
		$opportunity->setDecisionMaker( $lead->getDecisionMaker() );
		$opportunity->setDescription( $lead->getDescription() );
		$opportunity->setEstimatedCloseDate( $lead->getEstimatedCloseDate() );
		$opportunity->getEstimatedValue( $lead->getEstimatedValue() );
		$opportunity->setEvaluateFit( $lead->getEvaluateFit() );
		$opportunity->setInitialContact( $lead->getInitialContact() );
		$opportunity->setLastUpdateDate( $now );
		$opportunity->setName( $name );
		$opportunity->setOriginatingLead( $lead );
		$opportunity->setPriority( $lead->getPriority() );
		$opportunity->setPurchaseProcess( $lead->getPurchaseProcess() );
		$opportunity->setPurchaseTimeframe( $lead->getPurchaseTimeframe() );
		$opportunity->setScheduleFollowupProspect( $lead->getScheduleFollowupProspect() );
		$opportunity->setScheduleFollowupQualify( $lead->getScheduleFollowupQualify() );
		$opportunity->setState( OpportunityState::OPEN );
		$opportunity->setStatus( OpportunityStatus::NEWOPPORTUNITY );
		
		$lead->setOpportunity($opportunity);
		
		return $opportunity;
	}
}
?>