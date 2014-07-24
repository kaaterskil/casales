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
 * @package     Application\Model
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: Lead.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;
use Application\Model\Account;
use Application\Model\Address;
use Application\Model\Audit;
use Application\Model\Auditable;
use Application\Model\BusinessUnit;
use Application\Model\Campaign;
use Application\Model\Contact;
use Application\Model\Contactable;
use Application\Model\InitialContact;
use Application\Model\LeadPriority;
use Application\Model\LeadQuality;
use Application\Model\LeadSource;
use Application\Model\LeadState;
use Application\Model\LeadStatus;
use Application\Model\MarketingList;
use Application\Model\Need;
use Application\Model\Opportunity;
use Application\Model\PurchaseProcess;
use Application\Model\PurchaseTimeframe;
use Application\Model\SalesStage;
use Application\Model\Salutation;
use Application\Model\Telephone;
use Application\Model\User;
use Application\Service\OrderOpenActivitiesRequest;
use Application\Service\OrderClosedActivitiesRequest;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A lead is an individual who has indicated an interest in finding out more about the
 * products or services offered by a business unit. The lead has been identified by a
 * salesperson as a recipient for targeted information through e-mail or other
 * communication activities.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Lead.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_lead")
 */
class Lead implements Entity, Contactable, Auditable {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Account", inversedBy="leads")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
	 * @var Account
	 */
	private $account;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BusinessUnit")
	 * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
	 * @var BusinessUnit
	 */
	private $businessUnit;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Campaign", inversedBy="leads")
	 * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
	 * @var Campaign
	 */
	private $campaign;

	/**
	 * @ORM\Column(type="string", name="company_name")
	 * @var string
	 */
	private $companyName;

	/**
	 * @ORM\Column(type="integer", name="confirm_interest")
	 * @var boolean
	 */
	private $confirmInterest = false;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Contact", inversedBy="leads")
	 * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
	 * @var Contact
	 */
	private $contact;

	/**
	 * @ORM\Column(type="integer", name="decision_maker")
	 * @var boolean
	 */
	private $decisionMaker = false;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description;

	/**
	 * @ORM\Column(type="integer", name="do_not_email")
	 * @var boolean
	 */
	private $doNotEmail = false;

	/**
	 * @ORM\Column(type="integer", name="do_not_mail")
	 * @var boolean
	 */
	private $doNotMail = false;

	/**
	 * @ORM\Column(type="integer", name="do_not_phone")
	 * @var boolean
	 */
	private $doNotPhone = false;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $email1;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $email2;

	/**
	 * @ORM\Column(type="date", name="estimated_close_date")
	 * @var \DateTime
	 */
	private $estimatedCloseDate;

	/**
	 * @ORM\Column(type="integer", name="estimated_value")
	 * @var int
	 */
	private $estimatedValue;

	/**
	 * @ORM\Column(type="integer", name="evaluate_fit")
	 * @var boolean
	 */
	private $evaluateFit = false;

	/**
	 * @ORM\Column(type="string", name="first_name")
	 * @var string
	 */
	private $firstName;

	/**
	 * @ORM\Column(type="string", name="full_name")
	 * @var string
	 */
	private $fullName;

	/**
	 * @ORM\Column(type="string", name="initial_contact")
	 * @var InitialContact
	 */
	private $initialContact;

	/**
	 * @ORM\Column(type="string", name="job_title")
	 * @var string
	 */
	private $jobTitle;

	/**
	 * @ORM\Column(type="string", name="last_name")
	 * @var string
	 */
	private $lastName;

	/**
	 * @ORM\Column(type="string", name="lead_quality")
	 * @var LeadQuality
	 */
	private $leadQuality;

	/**
	 * @ORM\Column(type="string", name="lead_source")
	 * @var LeadSource
	 */
	private $leadSource;

	/**
	 * @ORM\Column(type="string", name="middle_name")
	 * @var string
	 */
	private $middleName;

	/**
	 * @ORM\Column(type="string")
	 * @var Need
	 */
	private $need;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Opportunity", inversedBy="leads")
	 * @ORM\JoinColumn(name="opportunity_id", referencedColumnName="id")
	 * @var Opportunity
	 */
	private $opportunity;

	/**
	 * Unidirectional Many-to-One
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 * @var User
	 */
	private $owner;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $prefix;

	/**
	 * @ORM\Column(type="string")
	 * @var LeadPriority
	 */
	private $priority;

	/**
	 * @ORM\Column(type="string", name="purchase_process")
	 * @var PurchaseProcess
	 */
	private $purchaseProcess;

	/**
	 * @ORM\Column(type="string", name="purchase_timeframe")
	 * @var PurchaseTimeframe
	 */
	private $purchaseTimeframe;

	/**
	 * @ORM\Column(type="string", name="qualification_comments")
	 * @var string
	 */
	private $qualificationComments;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $revenue;

	/**
	 * @ORM\Column(type="string", name="sales_stage")
	 * @var SalesStage
	 */
	private $salesStage;

	/**
	 * @ORM\Column(type="string", name="sales_stage_code")
	 * @var string
	 */
	private $salesStageCode;

	/**
	 * @ORM\Column(type="string")
	 * @var Salutation
	 */
	private $salutation;

	/**
	 * @ORM\Column(type="datetime", name="schedule_followup_prospect")
	 * @var \DateTime
	 */
	private $scheduleFollowupProspect;

	/**
	 * @ORM\Column(type="datetime", name="schedule_followup_qualify")
	 * @var \DateTime
	 */
	private $scheduleFollowupQualify;

	/**
	 * @ORM\Column(type="string")
	 * @var LeadState
	 */
	private $state;

	/**
	 * @ORM\Column(type="string")
	 * @var LeadStatus
	 */
	private $status;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $suffix;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $website;

	/**
	 * @ORM\Column(type="datetime", name="creation_date")
	 * @var \DateTime
	 */
	private $creationDate;

	/**
	 * @ORM\Column(type="datetime", name="last_update_date")
	 * @var \DateTime
	 */
	private $lastUpdateDate;
	
	/* ---------- One-to-Many Associations ---------- */
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Address", mappedBy="lead", cascade={"persist", "remove"}, fetch="EAGER")
	 * @var ArrayCollection
	 */
	private $addresses;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractAppointment", mappedBy="lead", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $appointments;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Audit", mappedBy="lead", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $auditItems;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractInteraction", mappedBy="lead", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $interactions;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractNote", mappedBy="lead", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $notes;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Opportunity", mappedBy="originatingLead", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $opportunities;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractTask", mappedBy="lead", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $tasks;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Telephone", mappedBy="lead", cascade={"persist", "remove"}, fetch="EAGER")
	 * @var ArrayCollection
	 */
	private $telephones;
	
	/* ---------- Many-to-Many Associations ---------- */
	
	/**
	 * INVERSE SIDE
	 * @ORM\ManyToMany(targetEntity="MarketingList", mappedBy="leads")
	 * @var ArrayCollection
	 */
	private $lists;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->addresses = new ArrayCollection();
		$this->appointments = new ArrayCollection();
		$this->auditItems = new ArrayCollection();
		$this->interactions = new ArrayCollection();
		$this->lists = new ArrayCollection();
		$this->notes = new ArrayCollection();
		$this->opportunities = new ArrayCollection();
		$this->tasks = new ArrayCollection();
		$this->telephones = new ArrayCollection();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return int
	 * @see \Application\Stdlib\Entity::getId()
	 */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/**
	 * @return \Application\Model\Account
	 */
	public function getAccount() {
		return $this->account;
	}

	public function setAccount(Account $account = null) {
		$this->account = $account;
	}

	/**
	 * @return BusinessUnit
	 */
	public function getBusinessUnit() {
		return $this->businessUnit;
	}

	public function setBusinessUnit(BusinessUnit $businessUnit = null) {
		$this->businessUnit = $businessUnit;
	}

	/**
	 * @return \Application\Model\Campaign
	 */
	public function getCampaign() {
		return $this->campaign;
	}

	public function setCampaign(Campaign $campaign = null) {
		$this->campaign = $campaign;
	}

	/**
	 * @return string
	 */
	public function getCompanyName() {
		return $this->companyName;
	}

	public function setCompanyName($companyName) {
		$this->companyName = $companyName;
	}

	/**
	 * @return boolean
	 */
	public function getConfirmInterest() {
		return $this->confirmInterest;
	}

	public function setConfirmInterest($confirmInterest) {
		if (is_bool( $confirmInterest ) || is_numeric( $confirmInterest )) {
			$this->confirmInterest = (bool) $confirmInterest;
		} else {
			$this->confirmInterest = $confirmInterest == 'true' ? true : false;
		}
	}

	/**
	 * @return \Application\Model\Contact
	 */
	public function getContact() {
		return $this->contact;
	}

	public function setContact(Contact $contact = null) {
		$this->contact = $contact;
	}

	/**
	 * @return boolean
	 */
	public function getDecisionMaker() {
		return $this->decisionMaker;
	}

	public function setDecisionMaker($decisionMaker) {
		if (is_bool( $decisionMaker ) || is_numeric( $decisionMaker )) {
			$this->decisionMaker = (bool) $decisionMaker;
		} else {
			$this->decisionMaker = $decisionMaker == 'true' ? true : false;
		}
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return boolean
	 */
	public function getDoNotEmail() {
		return $this->doNotEmail;
	}

	public function setDoNotEmail($doNotEmail) {
		if (is_bool( $doNotEmail ) || is_numeric( $doNotEmail )) {
			$this->doNotEmail = (bool) $doNotEmail;
		} else {
			$this->doNotEmail = $doNotEmail == 'true' ? true : false;
		}
	}

	/**
	 * @return boolean
	 */
	public function getDoNotMail() {
		return $this->doNotMail;
	}

	public function setDoNotMail($doNotMail) {
		if (is_bool( $doNotMail ) || is_numeric( $doNotMail )) {
			$this->doNotMail = (bool) $doNotMail;
		} else {
			$this->doNotMail = $doNotMail == 'true' ? true : false;
		}
	}

	/**
	 * @return boolean
	 */
	public function getDoNotPhone() {
		return $this->doNotPhone;
	}

	public function setDoNotPhone($doNotPhone) {
		if (is_bool( $doNotPhone ) || is_numeric( $doNotPhone )) {
			$this->doNotPhone = (bool) $doNotPhone;
		} else {
			$this->doNotPhone = $doNotPhone == 'true' ? true : false;
		}
	}

	/**
	 * @return string
	 * @see \Application\Model\Regarding::getEmail1()
	 */
	public function getEmail1() {
		return $this->email1;
	}

	public function setEmail1($email1) {
		$this->email1 = (string) $email1;
	}

	/**
	 * @return string
	 */
	public function getEmail2() {
		return $this->email2;
	}

	public function setEmail2($email2) {
		$this->email2 = (string) $email2;
	}

	/**
	 * @return DateTime
	 */
	public function getEstimatedCloseDate() {
		return $this->estimatedCloseDate;
	}

	public function setEstimatedCloseDate($estimatedCloseDate = null) {
		if ($estimatedCloseDate instanceof \DateTime) {
			$now = new \DateTime();
			if ($estimatedCloseDate->format( 'Y-m-d g:i' ) == $now->format( 'Y-m-d g:i' )) {
				$this->estimatedCloseDate = null;
				return;
			}
		}
		$this->estimatedCloseDate = $estimatedCloseDate;
	}

	/**
	 * @return int
	 */
	public function getEstimatedValue() {
		return $this->estimatedValue;
	}

	public function setEstimatedValue($estimatedValue) {
		$this->estimatedValue = (int) $estimatedValue;
	}

	/**
	 * @return boolean
	 */
	public function getEvaluateFit() {
		return $this->evaluateFit;
	}

	public function setEvaluateFit($evaluateFit) {
		if (is_bool( $evaluateFit ) || is_numeric( $evaluateFit )) {
			$this->evaluateFit = (bool) $evaluateFit;
		} else {
			$this->evaluateFit = $evaluateFit == 'true' ? true : false;
		}
	}

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	public function setFirstName($firstName) {
		$this->firstName = (string) $firstName;
	}

	/**
	 * @return string
	 */
	public function getFullName() {
		return $this->fullName;
	}

	public function setFullName($fullName) {
		$this->fullName = (string) $fullName;
	}

	/**
	 * @return \Application\Model\InitialContact
	 */
	public function getInitialContact() {
		return $this->initialContact;
	}

	public function setInitialContact($initialContact = null) {
		if ($initialContact instanceof InitialContact) {
			$this->initialContact = $initialContact;
		} elseif ($initialContact != null) {
			$this->initialContact = InitialContact::instance( $initialContact );
		} else {
			$this->initialContact = null;
		}
	}

	/**
	 * @return string
	 */
	public function getJobTitle() {
		return $this->jobTitle;
	}

	public function setJobTitle($jobTitle) {
		$this->jobTitle = (string) $jobTitle;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	public function setLastName($lastName) {
		$this->lastName = (string) $lastName;
	}

	/**
	 * @return \Application\Model\LeadQuality
	 */
	public function getLeadQuality() {
		return $this->leadQuality;
	}

	public function setLeadQuality($leadQuality = null) {
		if ($leadQuality instanceof LeadQuality) {
			$this->leadQuality = $leadQuality;
		} elseif ($leadQuality != null) {
			$this->leadQuality = LeadQuality::instance( $leadQuality );
		} else {
			$this->leadQuality = null;
		}
	}

	/**
	 * @return LeadSource
	 */
	public function getLeadSource() {
		return $this->leadSource;
	}

	public function setLeadSource($leadSource) {
		if ($leadSource instanceof LeadSource) {
			$this->leadSource = $leadSource;
		} elseif ($leadSource != null) {
			$this->leadSource = LeadSource::instance( $leadSource );
		} else {
			$this->leadSource = null;
		}
	}

	/**
	 * @return string
	 */
	public function getMiddleName() {
		return $this->middleName;
	}

	public function setMiddleName($middleName) {
		$this->middleName = (string) $middleName;
	}

	/**
	 * @return \Application\Model\Need
	 */
	public function getNeed() {
		return $this->need;
	}

	public function setNeed($need = null) {
		if ($need instanceof Need) {
			$this->need = $need;
		} elseif ($need != null) {
			$this->need = Need::instance( $need );
		} else {
			$this->need = null;
		}
	}

	/**
	 * @return \Application\Model\Opportunity
	 */
	public function getOpportunity() {
		return $this->opportunity;
	}

	public function setOpportunity(Opportunity $opportunity = null) {
		$this->opportunity = $opportunity;
	}

	/**
	 * @return User
	 */
	public function getOwner() {
		return $this->owner;
	}

	public function setOwner(User $user = null) {
		$this->owner = $user;
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	public function setPrefix($prefix) {
		$this->prefix = (string) $prefix;
	}

	/**
	 * @return \Application\Model\LeadPriority
	 */
	public function getPriority() {
		return $this->priority;
	}

	public function setPriority($priority = null) {
		if ($priority instanceof LeadPriority) {
			$this->priority = $priority;
		} elseif ($priority != null) {
			$this->priority = LeadPriority::instance( $priority );
		} else {
			$this->priority = null;
		}
	}

	/**
	 * @return \Application\Model\PurchaseProcess
	 */
	public function getPurchaseProcess() {
		return $this->purchaseProcess;
	}

	public function setPurchaseProcess($purchaseProcess = null) {
		if ($purchaseProcess instanceof PurchaseProcess) {
			$this->purchaseProcess = $purchaseProcess;
		} elseif ($purchaseProcess != null) {
			$this->purchaseProcess = PurchaseProcess::instance( $purchaseProcess );
		} else {
			$this->purchaseProcess = null;
		}
	}

	/**
	 * @return \Application\Model\PurchaseTimeframe
	 */
	public function getPurchaseTimeframe() {
		return $this->purchaseTimeframe;
	}

	public function setPurchaseTimeframe($purchaseTimeframe = null) {
		if ($purchaseTimeframe instanceof PurchaseTimeframe) {
			$this->purchaseTimeframe = $purchaseTimeframe;
		} elseif ($purchaseTimeframe != null) {
			$this->purchaseTimeframe = PurchaseTimeframe::instance( $purchaseTimeframe );
		} else {
			$this->purchaseTimeframe = null;
		}
	}

	/**
	 * @return string
	 */
	public function getQualificationComments() {
		return $this->qualificationComments;
	}

	public function setQualificationComments($qualificationComments) {
		$this->qualificationComments = (string) $qualificationComments;
	}

	/**
	 * @return int
	 */
	public function getRevenue() {
		return $this->revenue;
	}

	public function setRevenue($revenue) {
		$this->revenue = (int) $revenue;
	}

	/**
	 * @return \Application\Model\SalesStage
	 */
	public function getSalesStage() {
		return $this->salesStage;
	}

	public function setSalesStage($salesStage = null) {
		if ($salesStage instanceof SalesStage) {
			$this->salesStage = $salesStage;
		} elseif ($salesStage != null) {
			$this->salesStage = SalesStage::instance( $salesStage );
		} else {
			$this->salesStage = null;
		}
	}

	/**
	 * @return string
	 */
	public function getSalesStageCode() {
		return $this->salesStageCode;
	}

	public function setSalesStageCode($salesStageCode) {
		$this->salesStageCode = (string) $salesStageCode;
	}

	/**
	 * @return \Application\Model\Salutation
	 */
	public function getSalutation() {
		return $this->salutation;
	}

	public function setSalutation($salutation = null) {
		if ($salutation instanceof Salutation) {
			$this->salutation = $salutation;
		} elseif ($salutation != null) {
			$this->salutation = Salutation::instance( $salutation );
		} else {
			$this->salutation = null;
		}
	}

	/**
	 * @return DateTime
	 */
	public function getScheduleFollowupProspect() {
		return $this->scheduleFollowupProspect;
	}

	public function setScheduleFollowupProspect($scheduleFollowupProspect = null) {
		if ($scheduleFollowupProspect instanceof \DateTime) {
			$now = new \DateTime();
			if ($scheduleFollowupProspect->format( 'Y-m-d g:i' ) == $now->format( 'Y-m-d g:i' )) {
				$this->scheduleFollowupProspect = null;
				return;
			}
		}
		$this->scheduleFollowupProspect = $scheduleFollowupProspect;
	}

	/**
	 * @return DateTime
	 */
	public function getScheduleFollowupQualify() {
		return $this->scheduleFollowupQualify;
	}

	public function setScheduleFollowupQualify($scheduleFollowupQualify = null) {
		if ($scheduleFollowupQualify instanceof \DateTime) {
			$now = new \DateTime();
			if ($scheduleFollowupQualify->format( 'Y-m-d g:i' ) == $now->format( 'Y-m-d g:i' )) {
				$this->scheduleFollowupQualify = null;
				return;
			}
		}
		$this->scheduleFollowupQualify = $scheduleFollowupQualify;
	}

	/**
	 * @return \Application\Model\LeadState
	 */
	public function getState() {
		return $this->state;
	}

	public function setState($state = null) {
		if ($state instanceof LeadState) {
			$this->state = $state;
		} elseif ($state != null) {
			$this->state = LeadState::instance( $state );
		} else {
			$this->state = null;
		}
	}

	/**
	 * @return \Application\Model\LeadStatus
	 */
	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status = null) {
		if ($status instanceof LeadStatus) {
			$this->status = $status;
		} elseif ($status != null) {
			$this->status = LeadStatus::instance( $status );
		} else {
			$this->status = null;
		}
	}

	/**
	 * @return string
	 */
	public function getSuffix() {
		return $this->suffix;
	}

	public function setSuffix($suffix) {
		$this->suffix = (string) $suffix;
	}

	/**
	 * @return string
	 */
	public function getWebsite() {
		return $this->website;
	}

	public function setWebsite($website) {
		$this->website = (string) $website;
	}

	/**
	 * @return DateTime
	 * @see \Application\Stdlib\Entity::getCreationDate()
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	/**
	 * @return DateTime
	 * @see \Application\Stdlib\Entity::getLastUpdateDate()
	 */
	public function getLastUpdateDate() {
		return $this->lastUpdateDate;
	}

	public function setLastUpdateDate($lastUpdateDate) {
		$this->lastUpdateDate = $lastUpdateDate;
	}
	
	/* ---------- Association Getter/Setters ---------- */

	/**
	 * @return ArrayCollection
	 */
	public function getAddresses() {
		return $this->addresses;
	}

	public function setAddresses(ArrayCollection $addresses) {
		$this->addresses = $addresses;
	}

	public function addAddresses(ArrayCollection $addresses) {
		foreach ( $addresses->getValues() as $address ) {
			$this->addAddress( $address );
		}
	}

	public function removeAddresses(ArrayCollection $addresses) {
		foreach ( $addresses->getValues() as $address ) {
			$this->removeAddress( $address );
		}
	}

	/**
	 * Adds an Address to the collection
	 * @param Address $address
	 */
	public function addAddress(Address $address) {
		$address->setLead( $this );
		$this->addresses->add( $address );
	}

	public function removeAddress(Address $address) {
		$address->setLead( null );
		$this->addresses->removeElement( $address );
	}
	
	/**
	 * @return ArrayCollection
	 */
	public function getAppointments() {
		return $this->appointments;
	}

	public function setAppointments(ArrayCollection $appointments) {
		$this->appointments = $appointments;
	}
	
	/**
	 * Adds an Appointment to the collection
	 * @param AbstractAppointment $appointment
	 */
	public function addAppointment(AbstractAppointment $appointment) {
		$appointment->setLead( $this );
		$this->appointments->add( $appointment );
	}

	/**
	 * @return ArrayCollection
	 */
	public function getAuditItems() {
		return $this->auditItems;
	}

	public function setAuditItems(ArrayCollection $auditItems) {
		$this->auditItems = $auditItems;
	}

	/**
	 * @param Audit $auditItem
	 * @see \Application\Model\Auditable::addAuditItem()
	 */
	public function addAuditItem(Audit $auditItem) {
		$auditItem->setLead( $this );
		$this->auditItems->add( $auditItem );
	}

	/**
	 * @param Audit $auditItem
	 * @see \Application\Model\Auditable::removeAuditItem()
	 */
	public function removeAuditItem(Audit $auditItem) {
		$auditItem->setLead( null );
		$this->auditItems->removeElement( $auditItem );
	}

	/**
	 * @return ArrayCollection
	 */
	public function getInteractions() {
		return $this->interactions;
	}

	public function setInteractions(ArrayCollection $interactions) {
		$this->interactions = $interactions;
	}

	/**
	 * Adds an Interaction to the collection
	 * @param AbstractInteraction $interaction
	 */
	public function addInteraction(AbstractInteraction $interaction) {
		$interaction->setLead( $this );
		$this->interactions->add( $interaction );
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getLists() {
		return $this->lists;
	}

	public function setLists(ArrayCollection $lists) {
		$this->lists = $lists;
	}

	/**
	 * @param MarketingList $list
	 */
	public function addList(MarketingList $list) {
		$this->lists->add( $list );
	}

	/**
	 * @param MarketingList $list
	 */
	public function removeList(MarketingList $list) {
		$this->lists->removeElement( $list );
	}

	/**
	 * @return ArrayCollection
	 */
	public function getNotes() {
		return $this->notes;
	}

	public function setNotes(ArrayCollection $notes) {
		$this->notes = $notes;
	}

	/**
	 * Adds a Note to the collection
	 * @param AbstractNote $note
	 */
	public function addNote(AbstractNote $note) {
		$note->setLead( $this );
		$this->notes->add( $note );
	}

	/**
	 * @return ArrayCollection
	 */
	public function getOpportunities() {
		return $this->opportunities;
	}

	public function setOpportunities(ArrayCollection $opportunities) {
		$this->opportunities = $opportunities;
	}

	public function addOpportunity(Opportunity $opportunity) {
		$opportunity->setOriginatingLead( $this );
		$this->opportunities->add( $opportunity );
	}

	/**
	 * @return ArrayCollection
	 */
	public function getTasks() {
		return $this->tasks;
	}

	public function setTasks(ArrayCollection $tasks) {
		$this->tasks = $tasks;
	}

	/**
	 * Adds a Task to the collection
	 * @param AbstractTask $task
	 */
	public function addTask(AbstractTask $task) {
		$task->setLead( $this );
		$this->tasks->add( $task );
	}

	/**
	 * @return ArrayCollection
	 */
	public function getTelephones() {
		return $this->telephones;
	}

	public function setTelephones(ArrayCollection $telephones) {
		$this->telephones = $telephones;
	}

	public function addTelephones(ArrayCollection $telephones) {
		foreach ( $telephones->getValues() as $telephone ) {
			$this->addTelephone( $telephone );
		}
	}

	public function removeTelephones(ArrayCollection $telephones) {
		foreach ( $telephones->getValues() as $telephone ) {
			$this->removeTelephone( $telephone );
		}
	}

	/**
	 * Adds a Telephone to the collection
	 * @param Telephone $telephone
	 */
	public function addTelephone(Telephone $telephone) {
		$telephone->setLead( $this );
		$this->telephones->add( $telephone );
	}

	public function removeTelephone(Telephone $telephone) {
		$telephone->setLead( null );
		$this->telephones->removeElement( $telephone );
	}
	
	/* ---------- Methods ---------- */

	/**
	 * Returns a formatted string of the Lead's name
	 * @return string
	 */
	public function computeFullName() {
		$value = '';
		$has_string = false;
		if ($this->prefix != '') {
			$value = $this->prefix;
			$has_string = true;
		}
		if ($this->firstName != '') {
			$value = $has_string ? $value . ' ' : $value;
			$value .= $this->firstName;
			$has_string = true;
		}
		if ($this->middleName != '') {
			$value = $has_string ? $value . ' ' : $value;
			$value .= $this->middleName;
			$has_string = true;
		}
		if ($this->lastName != '') {
			$value = $has_string ? $value . ' ' : $value;
			$value .= $this->lastName;
			$has_string = true;
		}
		if ($this->suffix != '') {
			$value = $has_string ? $value . ', ' : $value;
			$value .= $this->suffix;
		}
		return $value;
	}

	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Stdlib\Object::equals()
	 */
	public function equals(Object $o) {
		if (!$o instanceof Lead) {
			return false;
		}
		if ($o->getId() == $this->getId()) {
			return true;
		}
		if ($o->getFullName() == $this->getFullName()
				&& $o->getCompanyName() == $this->getCompanyName()
				&& $o->getCreationDate() == $this->getCreationDate()) {
			return true;
		}
		return false;
	}

	/**
	 * @return array
	 * @see \Application\Model\Auditable::getAuditableProperties()
	 */
	public function getAuditableProperties() {
		$collectionProperties = array(
			'addresses',
			'appointments',
			'auditItems',
			'interactions',
			'leads',
			'lists',
			'notes',
			'opportunities',
			'tasks',
			'telephones'
		);
		
		$result = array();
		foreach (get_object_vars($this) as $key => $value) {
			if(!in_array($key, $collectionProperties)) {
				$result[] = $key;
			}
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Stdlib\Object::getClass()
	 */
	public function getClass() {
		return get_class( $this );
	}

	/**
	 * Returns a collection of closed activities
	 * @return array
	 */
	public function getClosedActivities() {
		$result = array();
		foreach ( $this->getAppointments()->getValues() as $activity ) {
			if (!$this->isOpenActivity( $activity )) {
				$result[] = $activity;
			}
		}
		foreach ( $this->getInteractions()->getValues() as $activity ) {
			if (!$this->isOpenActivity( $activity )) {
				$result[] = $activity;
			}
		}
		foreach ( $this->getNotes()->getValues() as $activity ) {
			$result[] = $activity;
		}
		foreach ( $this->getTasks()->getValues() as $activity ) {
			if (!$this->isOpenActivity( $activity )) {
				$result[] = $activity;
			}
		}
		
		$comparator = new OrderClosedActivitiesRequest( $result );
		$response = $comparator->execute();
		if ($response->getResult()) {
			$result = $response->getCollection();
		}
		return $result;
	}

	/**
	 * @param boolean $includeLink
	 * @return string
	 * @see \Application\Model\Regarding::getDisplayName()
	 */
	public function getDisplayName($includeLink = false) {
		$result = $this->getFullName();
		if (strlen( $result > 25 )) {
			$result = substr( $result, 0, 25 ) . '...';
		}
		if ($includeLink) {
			$result = '<a href="/lead/edit/' . $this->getId() . '" target="_blank">' . $result . '</a>';
		}
		return $result;
	}

	/**
	 * Returns an anchor link for the Lead's email listing, if exists
	 * @return string
	 */
	public function getEmailLink() {
		$result = '';
		if ($this->getEmail1() != '') {
			$result = '<a href="mailto:' . $this->getEmail1() . '">' . $this->getEmail1() . '</a>';
		}
		return $result;
	}

	/**
	 * Returns a collection of open activities
	 * @return array
	 */
	public function getOpenActivities() {
		$result = array();
		foreach ( $this->getAppointments()->getValues() as $activity ) {
			if ($this->isOpenActivity( $activity )) {
				$result[] = $activity;
			}
		}
		foreach ( $this->getInteractions()->getValues() as $activity ) {
			if ($this->isOpenActivity( $activity )) {
				$result[] = $activity;
			}
		}
		foreach ( $this->getTasks()->getValues() as $activity ) {
			if ($this->isOpenActivity( $activity )) {
				$result[] = $activity;
			}
		}
		
		$comparator = new OrderOpenActivitiesRequest( $result );
		$response = $comparator->execute();
		if ($response->getResult()) {
			$result = $response->getCollection();
		}
		return $result;
	}

	/**
	 * Returns primary address (or first address if applicable) information for listing
	 * @return AddressDTO
	 */
	public function getPrimaryAddress() {
		/* @var $address Address */
		$result = new AddressDTO();
		if ($this->getAddresses()->count()) {
			$address = $this->getAddresses()->first();
			$street = strlen( $address->getAddress1() ) > 25
					? substr( $address->getAddress1(), 0, 25 ) . '...' : $address->getAddress1();
			$region = $address->getRegion() != null ? $address->getRegion()->getAbbreviation() : '';
			
			$result->setStreet( $street );
			$result->setCity( $address->getCity() );
			$result->setRegion( $region );
			$result->setPostalCode( $address->getPostalCode() );
		}
		return $result;
	}

	/**
	 * Returns the primary telephone for listing
	 * @return string
	 */
	public function getPrimaryTelephone() {
		/* @var $telephone Telephone */
		$result = '';
		if ($this->getTelephones()->count()) {
			foreach ( $this->getTelephones()->getValues() as $telephone ) {
				if ($telephone->getIsPrimary()) {
					$result = $telephone->getPhone();
					break;
				} else {
					$result = $telephone->getPhone();
				}
			}
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Model\Contactable::getSmtpEmailAddress()
	 */
	public function getSmtpEmailAddress() {
		$result = '';
		if ($this->getEmail1() != '') {
			$displayName = $this->getFirstName();
			$addrSpec = $this->getEmail1();
			
			// Test for special characters
			if (preg_match( '[@<>,]', $displayName )) {
				$displayName = '"' . $displayName . '"';
			}
			
			// Test length
			$result = $displayName . ' <' . $addrSpec . '>';
			if (strlen( $result ) > 255) {
				$result = $addrSpec;
			}
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'Lead[id=' . $this->getId()
		. ',account=' . ($this->getAccount() ? $this->getAccount()->getId() : '')
		. ',businessUnit=' . ($this->getBusinessUnit() ? $this->getBusinessUnit()->getId() : '')
		. ',campaign=' . ($this->getCampaign() ? $this->getCampaign()->getId() : '')
		. ',contact=' . ($this->getContact() ? $this->getContact()->getId() : '')
		. ',companyName=' . $this->getCompanyName()
		. ',confirmInterest=' . ($this->confirmInterest ? 'true' : 'false')
		. ',decisionMaker=' . ($this->decisionMaker ? 'true' : 'false')
		. ',description=' . $this->getDescription()
		. ',doNotEmail=' . ($this->getDoNotEmail() ? 'true' : 'false')
		. ',doNotMail=' . ($this->getDoNotMail() ? 'true' : 'false')
		. ',doNotPhone=' . ($this->getDoNotPhone() ? 'true' : 'false')
		. ',email1=' . $this->getEmail1()
		. ',email2=' . $this->getEmail1()
		. ',estimatedCloseDate=' . $this->formatDate( $this->estimatedCloseDate, 'Y-m-d' )
		. ',estimatedValue=' . $this->getEstimatedValue()
		. ',evaluateFit=' . ($this->getEvaluateFit() ? 'true' : 'false')
		. ',firstName=' . $this->getFirstName()
		. ',fullName=' . $this->getFullName()
		. ',initialContact=' . $this->getInitialContact()
		. ',jobTitle=' . $this->getJobTitle()
		. ',lastName=' . $this->getLastName()
		. ',leadQuality=' . $this->getLeadQuality()
		. ',leadSource=' . $this->getLeadSource()
		. ',middleName=' . $this->getMiddleName()
		. ',need=' . $this->getNeed()
		. ',opportunity=' . ($this->getOpportunity() ? $this->getOpportunity()->getId() : '')
		. ',owner=' . ($this->getOwner() ? $this->getOwner()->getId() : '')
		. ',priority=' . $this->getPriority()
		. ',purchaseProcess=' . $this->getPurchaseProcess()
		. ',purchaseTimeframe=' . $this->getPurchaseTimeframe()
		. ',qualificationComments=' . $this->getQualificationComments()
		. ',revenue=' . $this->getRevenue()
		. ',salesStage=' . $this->getSalesStage()
		. ',salesStageCode=' . $this->getSalesStageCode()
		. ',salutation=' . $this->getSalutation()
		. ',scheduleFollowupProspect=' . $this->formatDate( $this->scheduleFollowupProspect, 'Y-m-d' )
		. ',scheduleFollowupQualify=' . $this->formatDate( $this->scheduleFollowupQualify, 'Y-m-d' )
		. ',state=' . $this->getState()
		. ',status=' . $this->getStatus()
		. ',suffix=' . $this->getSuffix()
		. ',website=' . $this->getWebsite()
		. ',creationDate=' . $this->formatDate( $this->creationDate, 'Y-m-d H:i:s' )
		. ',lastUpdateDate=' . $$this->formatDate( $this->lastUpdateDate, 'Y-m-d H:i:s' )
		. ']';
	}

	/**
	 * Returns a date in string form with the given format
	 *
	 * @param DateTime $date
	 * @param string $format
	 * @return string
	 */
	private function formatDate(\DateTime $date = null, $format) {
		$result = '';
		if ($date != null) {
			$result = $date->format( $format );
		}
		return $result;
	}

	/**
	 * Tests if the given Activity is open or closed
	 * @param AbstractActivity $activity
	 * @return boolean
	 */
	private function isOpenActivity(AbstractActivity $activity) {
		if ($activity instanceof StatefulActivity) {
			$open = ActivityState::instance( ActivityState::OPEN );
			$scheduled = AppointmentState::instance( AppointmentState::SCHEDULED );
			if (($activity->getState() == $open) || ($activity->getState() == $scheduled)) {
				return true;
			}
		}
		return false;
	}
}
?>