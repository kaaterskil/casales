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
 * @version     SVN $Id: Opportunity.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;
use Application\Model\Account;
use Application\Model\Audit;
use Application\Model\Auditable;
use Application\Model\BusinessUnit;
use Application\Model\Campaign;
use Application\Model\Contact;
use Application\Model\InitialContact;
use Application\Model\Lead;
use Application\Model\PurchaseProcess;
use Application\Model\PurchaseTimeframe;
use Application\Model\Need;
use Application\Model\OpportunityClose;
use Application\Model\OpportunityPriority;
use Application\Model\OpportunityRating;
use Application\Model\OpportunityState;
use Application\Model\OpportunityStatus;
use Application\Model\OpportunityTimeline;
use Application\Model\Regarding;
use Application\Model\SalesStage;
use Application\Model\StatefulActivity;
use Application\Model\User;

use Application\Service\OrderOpenActivitiesRequest;
use Application\Service\OrderClosedActivitiesRequest;

use Application\Stdlib\Comparable;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Application\Stdlib\Exception\NullPointerException;
use Application\Stdlib\Exception\ClassCastException;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * An opportunity entity is used to track each sales engagement. Each opportunity
 * represents a possibility to sell something to a qualified customer. Salespeople use
 * opportunities to keep track of each sales engagement on which they are currently
 * working. You can track things such as what stage (introduction, qualification, needs
 * identification, proposal generation, and so on) an opportunity is in, the revenue
 * potential, the probability of closing the deal, and the orders that are generated.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Opportunity.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_opportunity")
 */
class Opportunity implements Entity, Regarding, Auditable, Comparable {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * The account with which the opportunity is associated
	 * Bidirectional Many-to-One OWNING SIDE
	 *
	 * @ORM\ManyToOne(targetEntity="Account", inversedBy="opportunities")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
	 * @var Account
	 */
	private $account;

	/**
	 * Date when the opportunity was closed.
	 *
	 * @ORM\Column(type="date", name="actual_close_date")
	 * @var \DateTime
	 */
	private $actualCloseDate;

	/**
	 * Actual revenue for the opportunity.
	 *
	 * @ORM\Column(type="integer", name="actual_value")
	 * @var int
	 */
	private $actualValue;
	
	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BusinessUnit")
	 * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
	 * @var BusinessUnit
	 */
	private $businessUnit;
	
	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Campaign")
	 * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
	 * @var Campaign
	 */
	private $campaign;

	/**
	 * Likelihood of closing the opportunity.
	 *
	 * @ORm\Column(type="integer", name="close_probability")
	 * @var int
	 */
	private $closeProbability;

	/**
	 * Information about whether the lead confirmed interest in our offerings.
	 *
	 * @ORM\Column(type="string", name="confirm_interest")
	 * @var boolean
	 */
	private $confirmInterest;

	/**
	 * The contact associated with the opportunity.
	 * Bidirectional Many-to-One OWNING SIDE
	 *
	 * @ORM\ManyToOne(targetEntity="Contact", inversedBy="opportunities")
	 * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
	 * @var Contact
	 */
	private $contact;

	/**
	 * Information about customer needs.
	 *
	 * @ORM\Column(type="string", name="customer_need")
	 * @var string
	 */
	private $customerNeed;

	/**
	 * Information about the pain points faced by the company or organization
	 * associated with the opportunity.
	 *
	 * @ORM\Column(type="string", name="customer_pain_points")
	 * @var string
	 */
	private $customerPainPoints;

	/**
	 * Information about whether the lead is a decision maker.
	 *
	 * @ORM\Column(type="integer", name="decision_maker")
	 * @var boolean
	 */
	private $decisionMaker;

	/**
	 * Description of the opportunity.
	 *
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description;

	/**
	 * Information about whether a proposal was developed for the opportunity.
	 *
	 * @ORM\Column(type="integer", name="develop_proposal")
	 * @var boolean
	 */
	private $developProposal;

	/**
	 * Estimated date on which the opportunity is expected to close.
	 *
	 * @ORM\Column(type="date", name="estimated_close_date")
	 * @var \DateTime
	 */
	private $estimatedCloseDate;

	/**
	 * Estimated value of the opportunity.
	 *
	 * @ORM\Column(type="integer", name="estimated_value")
	 * @var int
	 */
	private $estimatedValue;

	/**
	 * Information about whether the fit between the lead's needs and our
	 * offerings was evaluated.
	 *
	 * @ORM\Column(type="integer", name="evaluate_fit")
	 * @var boolean
	 */
	private $evaluateFit;

	/**
	 * Final decision date for the opportunity.
	 *
	 * @ORM\Column(type="date", name="final_decision_date")
	 * @var \DateTime
	 */
	private $finalDecisionDate;

	/**
	 * Information about whether initial communication was established.
	 *
	 * @ORM\Column(type="string", name="initial_contact")
	 * @var InitialContact
	 */
	private $initialContact;

	/**
	 * Name of the opportunity.
	 *
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $name;

	/**
	 * Information about the needs of the lead's company or organization.
	 *
	 * @ORM\Column(type="string")
	 * @var Need
	 */
	private $need;

	/**
	 * Quality of the opportunity, such as hot.
	 *
	 * @ORM\Column(type="string", name="opportunity_rating")
	 * @var OpportunityRating
	 */
	private $opportunityRating;

	/**
	 * The lead that originated the opportunity.
	 * Bidirectional Many-to-One OWNING SIDE
	 *
	 * @ORM\ManyToOne(targetEntity="Lead", inversedBy="opportunities")
	 * @ORM\JoinColumn(name="originating_lead_id", referencedColumnName="id")
	 * @var Lead
	 */
	private $originatingLead;
	
	/**
	 * Unidirectional Many-to-One
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 * @var User
	 */
	private $owner;

	/**
	 * Information about whether a final proposal was presented for the opportunity.
	 *
	 * @ORM\Column(type="integer", name="present_proposal")
	 * @var boolean
	 */
	private $presentProposal;

	/**
	 * Priority of the opportunity.
	 *
	 * @ORM\Column(type="string")
	 * @var OpportunityPriority
	 */
	private $priority;

	/**
	 * Information about the purchase process of the lead's company or organization.
	 *
	 * @ORM\Column(type="string", name="purchase_process")
	 * @var PurchaseProcess
	 */
	private $purchaseProcess;

	/**
	 * Information about the purchase time frame of the opportunity's company or organization.
	 *
	 * @ORM\Column(type="string", name="purchase_timeframe")
	 * @var PurchaseTimeframe
	 */
	private $purchaseTimeframe;

	/**
	 * Information about whether a Go/No-Go decision was made for the opportunity.
	 *
	 * @ORM\Column(type="integer", name="pursuit_decision")
	 * @var boolean
	 */
	private $pursuitDecision;

	/**
	 * Comments about the qualification of the lead.
	 *
	 * @ORM\Column(type="string", name="qualification_comments")
	 * @var string
	 */
	private $qualificationComments;

	/**
	 * Sales stage of the opportunity.
	 *
	 * @ORM\Column(type="string", name="sales_stage")
	 * @var SalesStage
	 */
	private $salesStage;

	/**
	 * Information about whether a prospecting follow up meeting was scheduled with the lead.
	 *
	 * @ORM\Column(type="datetime", name="schedule_followup_prospect")
	 * @var \DateTime
	 */
	private $scheduleFollowupProspect;

	/**
	 * Information about whether a qualifying follow up meeting was scheduled with the lead.
	 *
	 * @ORM\Column(type="datetime", name="schedule_followup_qualify")
	 * @var \DateTime
	 */
	private $scheduleFollowupQualify;

	/**
	 * Information about whether a proposal meeting was scheduled for the opportunity.
	 *
	 * @ORM\Column(type="datetime", name="schedule_proposal_meeting")
	 * @var \DateTime
	 */
	private $scheduleProposalMeeting;

	/**
	 * Information about whether a thank you note was sent to the opportunity.
	 *
	 * @ORM\Column(type="integer", name="send_thank_you")
	 * @var boolean
	 */
	private $sendThankYou;

	/**
	 * Status of the opportunity.
	 *
	 * @ORM\Column(type="string")
	 * @var OpportunityState
	 */
	private $state;

	/**
	 * Reason for the status of the opportunity.
	 *
	 * @ORM\Column(type="string")
	 * @var OpportunityStatus
	 */
	private $status;

	/**
	 * The step in the sales process.
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $step;

	/**
	 * @ORM\Column(type="string")
	 * @var OpportunityTimeline
	 */
	private $timeline;

	/**
	 * Date and time when the opportunity was created.
	 *
	 * @ORM\Column(type="datetime", name="creation_date")
	 * @var \DateTime
	 */
	private $creationDate;

	/**
	 * Date and time when the opportunity was last modified.
	 *
	 * @ORM\Column(type="datetime", name="last_update_date")
	 * @var \DateTime
	 */
	private $lastUpdateDate;
	
	/* ---------- One-to-Many Associations ---------- */

	/**
	 * Bidirectional One-to-Many INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractAppointment", mappedBy="opportunity", cascade={"persist", "remove"})
	 * @ORM\OrderBy({"scheduledStart" = "desc"})
	 * @var ArrayCollection
	 */
	private $appointments;

	/**
	 * Bidirectional One-to-Many INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Audit", mappedBy="opportunity", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $auditItems;

	/**
	 * Bidirectional One-to-Many INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractInteraction", mappedBy="opportunity", cascade={"persist", "remove"})
	 * @ORM\OrderBy({"actualEnd" = "desc"})
	 * @var ArrayCollection
	 */
	private $interactions;

	/**
	 * Bidirectional One-to-Many INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractNote", mappedBy="opportunity", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $notes;

	/**
	 * Bidirectional One-to-Many INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractTask", mappedBy="opportunity", cascade={"persist", "remove"})
	 * @ORM\OrderBy({"scheduledEnd" = "desc"})
	 * @var ArrayCollection
	 */
	private $tasks;
	
	/**
	 * Bidirectional One-to-Many INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="OpportunityClose", mappedBy="opportunity", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $opportunityClose;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->appointments = new ArrayCollection();
		$this->auditItems = new ArrayCollection();
		$this->interactions = new ArrayCollection();
		$this->notes = new ArrayCollection();
		$this->tasks = new ArrayCollection();
		$this->opportunityClose = new ArrayCollection();
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
	 * @return DateTime
	 */
	public function getActualCloseDate() {
		return $this->actualCloseDate;
	}

	public function setActualCloseDate($actualCloseDate = null) {
		if ($actualCloseDate instanceof \DateTime) {
			$now = new \DateTime();
			if ($actualCloseDate->format( 'Y-m-d h:i' ) == $now->format( 'Y-m-d h:i' )) {
				$this->actualCloseDate = null;
				return;
			}
		}
		$this->actualCloseDate = $actualCloseDate;
	}

	/**
	 * @return int
	 */
	public function getActualValue() {
		return $this->actualValue;
	}

	public function setActualValue($actualValue) {
		$this->actualValue = (int) $actualValue;
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
	 * @return int
	 */
	public function getCloseProbability() {
		return $this->closeProbability;
	}

	public function setCloseProbability($closeProbability) {
		$closeProbability = (int) $closeProbability;
		if (($closeProbability < 0) || ($closeProbability > 100)) {
			throw new \InvalidArgumentException( 'Close Probability must be a value between 0 and 100.' );
		}
		$this->closeProbability = (int) $closeProbability;
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
	 * @return string
	 */
	public function getCustomerNeed() {
		return $this->customerNeed;
	}

	public function setCustomerNeed($customerNeed) {
		$this->customerNeed = (string) $customerNeed;
	}

	/**
	 * @return string
	 */
	public function getCustomerPainPoints() {
		return $this->customerPainPoints;
	}

	public function setCustomerPainPoints($customerPainPoints) {
		$this->customerPainPoints = (string) $customerPainPoints;
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
		$this->description = (string) $description;
	}

	/**
	 * @return boolean
	 */
	public function getDevelopProposal() {
		return $this->developProposal;
	}

	public function setDevelopProposal($developProposal) {
		if (is_bool( $developProposal ) || is_numeric( $developProposal )) {
			$this->developProposal = (bool) $developProposal;
		} else {
			$this->developProposal = $developProposal == 'true' ? true : false;
		}
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
			if ($estimatedCloseDate->format( 'Y-m-d h:i' ) == $now->format( 'Y-m-d h:i' )) {
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
	 * @return DateTime
	 */
	public function getFinalDecisionDate() {
		return $this->finalDecisionDate;
	}

	public function setFinalDecisionDate($finalDecisionDate = null) {
		if ($finalDecisionDate instanceof \DateTime) {
			$now = new \DateTime();
			if ($finalDecisionDate->format( 'Y-m-d h:i' ) == $now->format( 'Y-m-d h:i' )) {
				$this->finalDecisionDate = null;
				return;
			}
		}
		$this->finalDecisionDate = $finalDecisionDate;
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
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
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
	 * @return \Application\Model\OpportunityRating
	 */
	public function getOpportunityRating() {
		return $this->opportunityRating;
	}

	public function setOpportunityRating($opportunityRating = null) {
		if ($opportunityRating instanceof OpportunityRating) {
			$this->opportunityRating = $opportunityRating;
		} elseif ($opportunityRating != null) {
			$this->opportunityRating = OpportunityRating::instance( $opportunityRating );
		} else {
			$this->opportunityRating = null;
		}
	}

	/**
	 * @return \Application\Model\Lead
	 */
	public function getOriginatingLead() {
		return $this->originatingLead;
	}

	public function setOriginatingLead(Lead $lead = null) {
		$this->originatingLead = $lead;
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
	 * @return boolean
	 */
	public function getPresentProposal() {
		return $this->presentProposal;
	}

	public function setPresentProposal($presentProposal) {
		if (is_bool( $presentProposal ) || is_numeric( $presentProposal )) {
			$this->presentProposal = (bool) $presentProposal;
		} else {
			$this->presentProposal = $presentProposal == 'true' ? true : false;
		}
	}

	/**
	 * @return \Application\Model\OpportunityPriority
	 */
	public function getPriority() {
		return $this->priority;
	}

	public function setPriority($priority = null) {
		if ($priority instanceof OpportunityPriority) {
			$this->priority = $priority;
		} elseif ($priority != null) {
			$this->priority = OpportunityPriority::instance( $priority );
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
	 * @return boolean
	 */
	public function getPursuitDecision() {
		return $this->pursuitDecision;
	}

	public function setPursuitDecision($pursuitDecision) {
		if (is_bool( $pursuitDecision ) || is_numeric( $pursuitDecision )) {
			$this->pursuitDecision = (bool) $pursuitDecision;
		} else {
			$this->pursuitDecision = $pursuitDecision == 'true' ? true : false;
		}
	}

	/**
	 * @return string
	 */
	public function getQualificationComments() {
		return $this->qualificationComments;
	}

	public function setQualificationComments($qualificationComments) {
		$this->qualificationComments = $qualificationComments;
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
	 * @return DateTime
	 */
	public function getScheduleFollowupProspect() {
		return $this->scheduleFollowupProspect;
	}

	public function setScheduleFollowupProspect($scheduleFollowupProspect = null) {
		if ($scheduleFollowupProspect instanceof \DateTime) {
			$now = new \DateTime();
			if ($scheduleFollowupProspect->format( 'Y-m-d h:i' ) == $now->format( 'Y-m-d h:i' )) {
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
			if ($scheduleFollowupQualify->format( 'Y-m-d h:i' ) == $now->format( 'Y-m-d h:i' )) {
				$this->scheduleFollowupQualify = null;
				return;
			}
		}
		$this->scheduleFollowupQualify = $scheduleFollowupQualify;
	}

	/**
	 * @return DateTime
	 */
	public function getScheduleProposalMeeting() {
		return $this->scheduleProposalMeeting;
	}

	public function setScheduleProposalMeeting($scheduleProposalMeeting = null) {
		if ($scheduleProposalMeeting instanceof \DateTime) {
			$now = new \DateTime();
			if ($scheduleProposalMeeting->format( 'Y-m-d h:i' ) == $now->format( 'Y-m-d h:i' )) {
				$this->scheduleProposalMeeting = null;
				return;
			}
		}
		$this->scheduleProposalMeeting = $scheduleProposalMeeting;
	}

	/**
	 * @return boolean
	 */
	public function getSendThankYou() {
		return $this->sendThankYou;
	}

	public function setSendThankYou($sendThankYou) {
		if (is_bool( $sendThankYou ) || is_numeric( $sendThankYou )) {
			$this->sendThankYou = (bool) $sendThankYou;
		} else {
			$this->sendThankYou = $sendThankYou == 'true' ? true : false;
		}
	}

	/**
	 * @return \Application\Model\OpportunityState
	 */
	public function getState() {
		return $this->state;
	}

	public function setState($state = null) {
		if ($state instanceof OpportunityState) {
			$this->state = $state;
		} elseif ($state != null) {
			$this->state = OpportunityState::instance( $state );
		} else {
			$this->state = null;
		}
	}

	/**
	 * @return \Application\Model\OpportunityStatus
	 */
	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status = null) {
		if ($status instanceof OpportunityStatus) {
			$this->status = $status;
		} elseif ($status != null) {
			$this->status = OpportunityStatus::instance( $status );
		} else {
			$this->status = null;
		}
	}

	/**
	 * @return string
	 */
	public function getStep() {
		return $this->step;
	}

	public function setStep($step) {
		$this->step = (string) $step;
	}

	/**
	 * @return \Application\Model\OpportunityTimeline
	 */
	public function getTimeline() {
		return $this->timeline;
	}

	public function setTimeline($timeline = null) {
		if ($timeline instanceof OpportunityTimeline) {
			$this->timeline = $timeline;
		} elseif ($timeline != null) {
			$this->timeline = OpportunityTimeline::instance( $timeline );
		} else {
			$this->timeline = null;
		}
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
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getOpportunityClose() {
		return $this->opportunityClose;
	}
	
	public function setOpportunityClose(ArrayCollection $opportunityClose) {
		$this->opportunityClose = $opportunityClose;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAppointments() {
		return $this->appointments;
	}

	public function setAppointments(ArrayCollection $appointments) {
		$this->appointments = $appointments;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAuditItems() {
		return $this->auditItems;
	}
	
	public function setAuditItems(ArrayCollection $auditItems) {
		$this->auditItems = $auditItems;
	}
	
	/**
	 * @param Audit $auditItem
	 */
	public function addAuditItem(Audit $auditItem) {
		$auditItem->setOpportunity($this);
		$this->auditItems->add($auditItem);
	}
	
	/**
	 * @param Audit $auditItem
	 */
	public function removeAuditItem(Audit $auditItem) {
		$auditItem->setOpportunity(null);
		$this->auditItems->removeElement($auditItem);
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getInteractions() {
		return $this->interactions;
	}

	public function setInteractions(ArrayCollection $interactions) {
		$this->interactions = $interactions;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getNotes() {
		return $this->notes;
	}

	public function setNotes(ArrayCollection $notes) {
		$this->notes = $notes;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTasks() {
		return $this->tasks;
	}

	public function setTasks(ArrayCollection $tasks) {
		$this->tasks = $tasks;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * Adds an OpportunityClose activity to this opportunity
	 * @param OpportunityClose $opportunityClose
	 */
	public function addOpportunityClose(OpportunityClose $opportunityClose) {
		$opportunityClose->setOpportunity( $this );
		$this->opportunityClose->add($opportunityClose);
	}
	
	/**
	 * Adds an Appointment to the collection
	 * @param AbstractAppointment $appointment
	 */
	public function addAppointment(AbstractAppointment $appointment) {
		$appointment->setOpportunity( $this );
		$this->appointments->add( $appointment );
	}

	/**
	 * Adds an Interaction to the collection
	 * @param AbstractInteraction $interaction
	 */
	public function addInteraction(AbstractInteraction $interaction) {
		$interaction->setOpportunity( $this );
		$this->interactions->add( $interaction );
	}

	/**
	 * Adds a Note to the collection
	 * @param AbstractNote $note
	 */
	public function addNote(AbstractNote $note) {
		$note->setOpportunity( $this );
		$this->notes->add( $note );
	}

	/**
	 * Adds a task to the collection
	 * @param AbstractTask $task
	 */
	public function addTask(AbstractTask $task) {
		$task->setOpportunity( $this );
		$this->tasks->add( $task );
	}
	
	/**
	 * @param Object $value
	 * @throws NullPointerException
	 * @throws ClassCastException
	 * @return int
	 * @see \Application\Stdlib\Comparable::compareTo()
	 */
	public function compareTo(Object $value = null) {
		if ($value == null) {
			throw new NullPointerException();
		}
		if (!$value instanceof $this) {
			throw new ClassCastException();
		}
		
		if ($this->getActualCloseDate() == null && $value->getActualCloseDate() == null) {
			return 0;
		} elseif ($this->getActualCloseDate() == null && $value->getActualCloseDate() != null) {
			return -1;
		} elseif ($this->getActualCloseDate() != null && $value->getActualCloseDate() == null) {
			return 1;
		} else {
			$t1 = $this->getActualCloseDate()->getTimestamp();
			$t2 = $value->getActualCloseDate()->getTimestamp();
			if($t1 == $t2) {
				return 0;
			} else {
				return ($t1 > $t2 ? 1 : -1);
			}
		}
		return 1;
	}

	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Stdlib\Object::equals()
	 */
	public function equals(Object $o) {
		if (!$o instanceof Opportunity) {
			return false;
		}
		if ($o->getId() == $this->getId()) {
			return true;
		}
		if (($o->getName() == $this->getName()) && ($o->getAccount() != null
				&& $o->getAccount()->equals( $this->getAccount() ))
				&& ($o->getContact() != null && $o->getContact()->equals( $this->getContact() ))
				&& ($o->getOriginatingLead() != null && $o->getOriginatingLead()->equals( $this->getOriginatingLead() ))
				&& ($o->getCreationDate() == $this->getCreationDate())) {
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
			'appointments',
			'auditItems',
			'interactions',
			'notes',
			'tasks',
			'opportunityClose'
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
	 * @param boolean $includeLink
	 * @return string
	 * @see \Application\Model\Regarding::getDisplayName()
	 */
	public function getDisplayName($includeLink = false) {
		$result = $this->getName();
		if(strlen($result > 25)) {
			$result = substr($result, 0, 25) . '...';
		}
		if($includeLink) {
			$result = '<a href="/opportunity/edit/' . $this->getId() . '" target="_blank">' . $result . '</a>';
		}
		return $result;
	}

	/**
	 * Returns the actual close date as a string in the given format
	 * @param string $format
	 * @return string
	 */
	public function getFormattedActualCloseDate($format = null) {
		if ($format == null) {
			$format = 'm/d/Y';
		}
		return $this->formatDate( $this->getActualCloseDate(), $format );
	}

	/**
	 * Returns the estimated close date as a string in the given format
	 * @param string $format
	 * @return string
	 */
	public function getFormattedEstimatedCloseDate($format = null) {
		if ($format == null) {
			$format = 'm/d/Y';
		}
		return $this->formatDate( $this->getEstimatedCloseDate(), $format );
	}

	/**
	 * Returns the final decision date as a string in the given format
	 * @param string $format
	 * @return string
	 */
	public function getFormattedFinalDecisionDate($format = null) {
		if ($format == null) {
			$format = 'm/d/Y';
		}
		return $this->formatDate( $this->getFinalDecisionDate(), $format );
	}

	/**
	 * Returns the prospect followup date as a string in the given format
	 * @param string $format
	 * @return string
	 */
	public function getFormattedScheduleFollowupProspect($format = null) {
		if ($format == null) {
			$format = 'm/d/Y h:i';
		}
		return $this->formatDate( $this->getScheduleFollowupProspect(), $format );
	}

	/**
	 * Returns the qualification followup date as a string in the given format
	 * @param string $format
	 * @return string
	 */
	public function getFormattedScheduleFollowupQualify($format = null) {
		if ($format == null) {
			$format = 'm/d/Y h:i';
		}
		return $this->formatDate( $this->getScheduleFollowupQualify(), $format );
	}

	/**
	 * Returns the proposal meeting date as a string in the given format
	 * @param string $format
	 * @return string
	 */
	public function getFormattedScheduleProposalMeeting($format = null) {
		if ($format == null) {
			$format = 'm/d/Y h:i';
		}
		return $this->formatDate( $this->getScheduleProposalMeeting(), $format );
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
		
		$comparator = new OrderOpenActivitiesRequest($result);
		$response = $comparator->execute();
		if($response->getResult()) {
			$result = $response->getCollection();
		}
		return $result;
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
		foreach ( $this->getOpportunityClose()->getValues() as $activity ) {
			$result[] = $activity;
		}
		
		$comparator = new OrderClosedActivitiesRequest($result);
		$response = $comparator->execute();
		if($response->getResult()) {
			$result = $response->getCollection();
		}
		return $result;
	}

	/**
	 * Returns the potential customer name, either the Account, the Contact or the
	 * originating Lead, whichever exists.
	 * @return string
	 */
	public function getPotentialCustomer() {
		$result = '';
		if ($this->getAccount() != null) {
			$result = $this->getAccount()->getName();
		} elseif ($this->getContact() != null) {
			$result = $this->getContact()->getDisplayName();
		} elseif ($this->getOriginatingLead() != null) {
			$result = $this->getOriginatingLead()->getFullName();
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'Opportunity[id=' . $this->getId()
		. ',account=' . ($this->getAccount() != null ? $this->getAccount()->getId() : '')
		. ',businessUnit=' . ($this->getBusinessUnit() != null ? $this->getBusinessUnit()->getId() : '')
		. ',campaign=' . ($this->getCampaign() != null ? $this->getCampaign()->getId() : '')
		. ',actualCloseDate=' . $this->getFormattedActualCloseDate()
		. ',actualValue=' . $this->actualValue
		. ',closeProbability=' . $this->closeProbability
		. ',confirmInterest=' . ($this->confirmInterest ? 'true' : 'false')
		. ',contact=' . ($this->contact ? $this->contact->getId() : '')
		. ',customerNeed=' . $this->customerNeed
		. ',customerPainPoints=' . $this->customerPainPoints
		. ',decisionMaker=' . ($this->decisionMaker ? 'true' : 'false')
		. ',description=' . $this->description
		. ',developProposal=' . ($this->developProposal ? 'true' : 'false')
		. ',estimatedCloseDate=' . $this->getFormattedEstimatedCloseDate()
		. ',estimatedValue=' . $this->estimatedValue
		. ',evaluateFit=' . ($this->evaluateFit ? 'true' : 'false')
		. ',finalDecisionDate=' . $this->getFormattedFinalDecisionDate()
		. ',initialContact=' . $this->initialContact
		. ',name=' . $this->name
		. ',need=' . $this->need
		. ',opportunityRating=' . $this->opportunityRating
		. ',originatingLead=' . ($this->originatingLead ? $this->originatingLead->getId() : '')
		. ',owner=' . ($this->getOwner() != null ? $this->getOwner()->getId() : '')
		. ',presentProposal=' . ($this->presentProposal ? 'true' : 'false')
		. ',priority=' . $this->priority
		. ',purchaseProcess=' . $this->purchaseProcess
		. ',purchaseTimeframe=' . $this->purchaseTimeframe
		. ',pursuitDecision=' . ($this->pursuitDecision ? 'true' : 'false')
		. ',qualificationComments=' . $this->qualificationComments
		. ',salesStage=' . $this->salesStage
		. ',scheduleFollowupProspect=' . $this->getFormattedScheduleFollowupProspect()
		. ',scheduleFollowupQualify=' . $this->getFormattedScheduleFollowupQualify()
		. ',scheduleProposalMeeting=' . $this->getFormattedScheduleProposalMeeting()
		. ',sendThankYou=' . ($this->sendThankYou ? 'true' : 'false')
		. ',state=' . $this->state
		. ',status=' . $this->status
		. ',step=' . $this->step
		. ',timeline=' . $this->timeline
		. ',creationDate=' . $this->formatDate($this->getCreationDate(), 'Y-m-d H:i:s')
		. ',lastUpdateDate=' . $this->formatDate($this->getLastUpdateDate(), 'Y-m-d H:i:s')
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
			$scheduled = AppointmentState::instance(AppointmentState::SCHEDULED);
			if (($activity->getState() == $open) || ($activity->getState() == $scheduled)) {
				return true;
			}
		}
		return false;
	}
}
?>