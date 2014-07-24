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
 * @package     Application\Model\Activity
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: AbstractActivity.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\Account;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Application\Model\Audit;
use Application\Model\Auditable;
use Application\Model\BusinessUnit;
use Application\Model\Campaign;
use Application\Model\CampaignActivity;
use Application\Model\Contact;
use Application\Model\Lead;
use Application\Model\Opportunity;
use Application\Model\Regarding;
use Application\Model\User;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the base class for all activity types (i.e. meetings, interactions, notes and tasks).
 *
 * @package		Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AbstractActivity.php 13 2013-08-05 22:53:55Z  $
 * @abstract
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"BreakfastAppointment"="BreakfastAppointment",
 *          "LunchAppointment"="LunchAppointment", "DinnerAppointment"="DinnerAppointment",
 *          "MeetingAppointment"="MeetingAppointment", "CallbackTask"="CallbackTask",
 *          "FollowUpTask"="FollowUpTask", "OtherTask"="OtherTask",
 *          "EmailInteraction"="EmailInteraction", "FaxInteraction"="FaxInteraction",
 *          "TelephoneInteraction"="TelephoneInteraction", "VisitInteraction"="VisitInteraction",
 *          "LetterInteraction"="LetterInteraction", "AccountNote"="AccountNote",
 *          "UserNote"="UserNote", "OpportunityClose"="OpportunityClose",
 *          "CampaignActivity"="CampaignActivity", "CampaignResponse"="CampaignResponse",
 *          "BulkOperation"="BulkOperation"})
 * @ORM\Table(name="crm_activity")
 */
abstract class AbstractActivity implements Entity, Auditable {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	protected $discriminator;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Account", inversedBy="activities")
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
	 * @ORM\ManyToOne(targetEntity="Campaign", inversedBy="activities")
	 * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
	 * @var Campaign
	 */
	private $campaign;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="CampaignActivity", inversedBy="activities")
	 * @ORM\JoinColumn(name="campaign_activity_id", referencedColumnName="id")
	 * @var CampaignActivity
	 */
	private $campaignActivity;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Contact", inversedBy="activities")
	 * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
	 * @var Contact
	 */
	private $contact;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Lead", inversedBy="activities")
	 * @ORM\JoinColumn(name="lead_id", referencedColumnName="id")
	 * @var Lead
	 */
	private $lead;

	/**
	 * @ORM\Column(type="string", name="long_notes")
	 * @var string
	 */
	private $longNotes;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $notes;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Opportunity", inversedBy="activities")
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
	 * A high level description of the activity
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $subject;

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
	 * @ORM\OneToMany(targetEntity="Audit", mappedBy="activity", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $auditItems;
	
	/* ----------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->auditItems = new ArrayCollection();
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
	 * @return string
	 */
	public function getDiscriminator() {
		return $this->discriminator;
	}

	public function setDiscriminator($discriminator) {
		$this->discriminator = (string) $discriminator;
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
	 * @return \Application\Model\CampaignActivity
	 */
	public function getCampaignActivity() {
		return $this->campaignActivity;
	}

	public function setCampaignActivity(CampaignActivity $campaignActivity = null) {
		$this->campaignActivity = $campaignActivity;
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
	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = (string) $description;
	}

	/**
	 * @return \Application\Model\Lead
	 */
	public function getLead() {
		return $this->lead;
	}

	public function setLead(Lead $lead = null) {
		$this->lead = $lead;
	}

	/**
	 * @return string
	 */
	public function getLongNotes() {
		return $this->longNotes;
	}

	public function setLongNotes($longNotes) {
		$this->longNotes = (string) $longNotes;
	}

	/**
	 * @return string
	 */
	public function getNotes() {
		return $this->notes;
	}

	public function setNotes($notes) {
		$this->notes = (string) $notes;
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
	public function getSubject() {
		return $this->subject;
	}

	public function setSubject($subject) {
		$this->subject = (string) $subject;
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
	
	/* ---------- One-to-Many Association Getter/Setters ---------- */
	
	/**
	 * Returns a collection of audit items
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
	 * @see \Application\Model\Auditable::addAuditItem()
	 */
	public function addAuditItem(Audit $auditItem) {
		$auditItem->setActivity( $this );
		$this->auditItems->add( $auditItem );
	}

	/**
	 * @param Audit $auditItem
	 * @see \Application\Model\Auditable::removeAuditItem()
	 */
	public function removeAuditItem(Audit $auditItem) {
		$auditItem->setActivity( null );
		$this->auditItems->removeElement( $auditItem );
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Stdlib\Object::equals()
	 */
	public function equals(Object $o) {
		if (!$o instanceof $this) {
			return false;
		}
		if ($o->getId() == $this->getId()) {
			return true;
		}
		if (($o->getDiscriminator() ==
			 $this->getDiscriminator()) &&
			 ($o->getAccount() ==
			 $this->getAccount()) &&
			 ($o->getContact() ==
			 $this->getContact()) &&
			 ($o->getOpportunity() ==
			 $this->getOpportunity()) &&
			 ($o->getDescription() ==
			 $this->getDescription()) &&
			 ($o->getCreationDate() ==
			 $this->getCreationDate())) {
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
			'auditItems'
		);
		
		$result = array();
		foreach ( get_object_vars( $this ) as $key => $value ) {
			if (!in_array( $key, $collectionProperties )) {
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
	 * Returns the description as a link to its edit page
	 *
	 * @param int $id
	 * 		The parent id
	 * @param string $route
	 * 		The return route to the parent edit page
	 * @return string
	 */
	public function getDescriptionLink($id = null, $route = null) {
		$link = '/activity/edit/' . $this->getId() . '/' . $this->getDiscriminator() . '/' . $id . '/' . $route;
		$text = ($this->getSubject() != '' ? $this->getSubject() : $this->getDescription());
		return '<a href="' . $link . '">' . $text . '</a>';
	}

	/**
	 * Returns the discriminator value as separate words
	 * @return string
	 */
	public function getDiscriminatorTitle() {
		$words = preg_split( '/(?<=[a-z])(?=[A-Z])/', $this->getDiscriminator() );
		array_pop( $words );
		return implode( ' ', $words );
	}

	/**
	 * Returns a title for the Activity index page of the description plus an optional second
	 * line for the linked Account, Contact or Opportunity whichever is applicable.
	 * @return string
	 */
	public function getListTitle() {
		if ($this->getSubject() != '') {
			$result = '<div class="bold">' . $this->getSubject() . '</div>';
		} else {
			$result = '<div class="bold">' . $this->getDescription() . '</div>';
		}
		$result .= '<div>' . $this->getTopic() . '</div>';
		return $result;
	}

	/**
	 * Returns the (first) parent association
	 *
	 * @return Regarding
	 */
	public function getRegardingObject() {
		$result = null;
		if ($this->getAccount()) {
			$result = $this->getAccount();
		} elseif ($this->getContact()) {
			$result = $this->getContact();
		} elseif ($this->getLead()) {
			$result = $this->getLead();
		} elseif ($this->getOpportunity()) {
			$result = $this->getOpportunity();
		} elseif ($this->getCampaign()) {
			$result = $this->getCampaign();
		} elseif ($this->getCampaignActivity()) {
			$result = $this->getCampaignActivity();
		}
		return $result;
	}

	/**
	 * Returns the name of the parent association
	 *
	 * @param boolean $includeLink
	 * 		If true, returns a link to the regarding object record
	 * @return string
	 */
	public function getRegardingObjectName($includeLink = false) {
		$result = '';
		$ro = $this->getRegardingObject();
		if ($ro != null) {
			$result = $ro->getDisplayName( $includeLink );
		}
		return $result;
	}

	/**
	 * Returns the applicable linked Account, Contact or Opportunity
	 * @return string
	 */
	public function getTopic() {
		$result = '';
		if ($this->getAccount()) {
			$result = $this->getAccount()->getName() . ' (Account)';
		} elseif ($this->getContact()) {
			$result = $this->getContact()->getDisplayName() . ' (Contact)';
		} elseif ($this->getOpportunity()) {
			$result = $this->getOpportunity()->getName() . ' (Opportunity)';
		} elseif ($this->getLead()) {
			$result = $this->getLead()->getFullName() . ' (Lead)';
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return get_class( $this ) .
			 '[id=' .
			 $this->getId() .
			 ',discriminator=' .
			 $this->getDiscriminator() .
			 ',account=' .
			 ($this->getAccount() !=
			 null ? $this->getAccount()->getId() : '') .
			 ',businessUnit=' .
			 ($this->getBusinessUnit() !=
			 null ? $this->getBusinessUnit()->getId() : '') .
			 ',campaign=' .
			 ($this->getCampaign() !=
			 null ? $this->getCampaign()->getId() : '') .
			 ',campaignActivity=' .
			 ($this->getCampaignActivity() !=
			 null ? $this->getCampaignActivity()->getId() : '') .
			 ',contact=' .
			 ($this->getContact() !=
			 null ? $this->getContact()->getId() : '') .
			 ',description="' .
			 ($this->getDescription() .
			 '"') .
			 ',lead=' .
			 ($this->getLead() !=
			 null ? $this->getLead()->getId() : '') .
			 ',longNotes="' .
			 ($this->getLongNotes() .
			 '"' .
			 ',notes="' .
			 $this->getNotes() .
			 '"') .
			 ',opportunity=' .
			 ($this->getOpportunity() !=
			 null ? $this->getOpportunity()->getId() : '') .
			 ',owner=' .
			 ($this->getOwner() !=
			 null ? $this->getOwner()->getId() : '') .
			 ',subject="' .
			 ($this->getSubject() .
			 '"') .
			 ',creationDate=' .
			 $this->getCreationDate() .
			 ',lastUpdateDate=' .
			 $this->getLastUpdateDate();
	}
}
?>