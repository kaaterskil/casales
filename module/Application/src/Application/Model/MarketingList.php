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
 * @version     SVN $Id: MarketingList.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\Account;
use Application\Model\BusinessUnit;
use Application\Model\Campaign;
use Application\Model\CampaignItem;
use Application\Model\CampaignActivity;
use Application\Model\Contact;
use Application\Model\Lead;
use Application\Model\ListState;
use Application\Model\ListStatus;
use Application\Model\MemberType;
use Application\Model\User;
use Application\Stdlib\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Application\Stdlib\Object;

/**
 * Represents a group of existing or potential customers created for a marketing campaign
 * or other sales purposes. All list members must be of the same entity type: account,
 * contact, or lead.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: MarketingList.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_list")
 */
class MarketingList implements Entity, CampaignItem {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BusinessUnit")
	 * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
	 * @var BusinessUnit
	 */
	private $businessUnit;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description;

	/**
	 * @ORM\Column(type="date", name="last_used")
	 * @var \DateTime
	 */
	private $lastUsed;

	/**
	 * @ORM\Column(type="integer", name="lock_status")
	 * @var boolean
	 */
	private $lockStatus = false;

	/**
	 * @ORM\Column(type="integer", name="member_count")
	 * @var int
	 */
	private $memberCount;

	/**
	 * @ORM\Column(type="string", name="member_type")
	 * @var MemberType
	 */
	private $memberType;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $name;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 * @var User
	 */
	private $owner;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $purpose;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $source;

	/**
	 * @ORM\Column(type="string")
	 * @var ListState
	 */
	private $state;

	/**
	 * @ORM\Column(type="string")
	 * @var ListStatus
	 */
	private $status;

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
	 * @ORM\OneToMany(targetEntity="BulkOperation", mappedBy="marketingList", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $bulkOperations;
	
	/* ---------- Many-to-Many Associations ---------- */

	/**
	 * INVERSE SIDE
	 * @ORM\ManyToMany(targetEntity="CampaignActivity", mappedBy="lists")
	 * @var ArrayCollection
	 */
	private $campaignActivities;
	
	/**
	 * INVERSE SIDE
	 * @ORM\ManyToMany(targetEntity="Campaign", mappedBy="lists")
	 * @var ArrayCollection
	 */
	private $campaigns;

	/**
	 * OWNING SIDE
	 * @ORM\ManyToMany(targetEntity="Account")
	 * @ORM\JoinTable(name="crm_list_account_association",
	 * 		joinColumns={@ORM\JoinColumn(name="list_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")})
	 * @var ArrayCollection
	 */
	private $accounts;

	/**
	 * OWNING SIDE
	 * @ORM\ManyToMany(targetEntity="Contact")
	 * @ORM\JoinTable(name="crm_list_contact_association",
	 * 		joinColumns={@ORM\JoinColumn(name="list_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id")})
	 * @var ArrayCollection
	 */
	private $contacts;

	/**
	 * OWNING SIDE
	 * @ORM\ManyToMany(targetEntity="Lead")
	 * @ORM\JoinTable(name="crm_list_lead_association",
	 * 		joinColumns={@ORM\JoinColumn(name="list_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="lead_id", referencedColumnName="id")})
	 * @var ArrayCollection
	 */
	private $leads;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->bulkOperations = new ArrayCollection();
		$this->campaignActivities = new ArrayCollection();
		$this->campaigns = new ArrayCollection();
		$this->accounts = new ArrayCollection();
		$this->contacts = new ArrayCollection();
		$this->leads = new ArrayCollection();
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
	 * @return \Application\Model\BusinessUnit
	 */
	public function getBusinessUnit() {
		return $this->businessUnit;
	}

	public function setBusinessUnit(BusinessUnit $businessUnit = null) {
		$this->businessUnit = $businessUnit;
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
	 * @return DateTime
	 */
	public function getLastUsed() {
		return $this->lastUsed;
	}

	public function setLastUsed($lastUsed) {
		$this->lastUsed = $lastUsed;
	}

	/**
	 * @return boolean
	 */
	public function getLockStatus() {
		return $this->lockStatus;
	}

	public function setLockStatus($lockStatus) {
		if (is_bool( $lockStatus ) || is_numeric( $lockStatus )) {
			$this->lockStatus = (bool) $lockStatus;
		} else {
			$this->lockStatus = ($lockStatus == 'true' ? true : false);
		}
	}

	/**
	 * @return int
	 */
	public function getMemberCount() {
		return $this->memberCount;
	}

	public function setMemberCount($memberCount) {
		$this->memberCount = (int) $memberCount;
	}

	/**
	 * @return MemberType
	 */
	public function getMemberType() {
		return $this->memberType;
	}

	public function setMemberType($memberType = null) {
		if ($memberType instanceof MemberType) {
			$this->memberType = $memberType;
		} elseif ($memberType != null) {
			$this->memberType = MemberType::instance( $memberType );
		} else {
			$this->memberType = null;
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = (string) $name;
	}

	/**
	 * @return \Application\Model\User
	 */
	public function getOwner() {
		return $this->owner;
	}

	public function setOwner(User $owner = null) {
		$this->owner = $owner;
	}

	/**
	 * @return string
	 */
	public function getPurpose() {
		return $this->purpose;
	}

	public function setPurpose($purpose) {
		$this->purpose = (string) $purpose;
	}

	/**
	 * @return string
	 */
	public function getSource() {
		return $this->source;
	}

	public function setSource($source) {
		$this->source = (string) $source;
	}

	/**
	 * @return \Application\Model\ListState
	 */
	public function getState() {
		return $this->state;
	}

	public function setState($state = null) {
		if ($state instanceof ListState) {
			$this->state = $state;
		} elseif ($state != null) {
			$this->state = ListState::instance( $state );
		} else {
			$this->state = null;
		}
	}

	/**
	 * @return \Application\Model\ListStatus
	 */
	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status = null) {
		if ($status instanceof ListStatus) {
			$this->status = $status;
		} elseif ($status != null) {
			$this->status = ListStatus::instance( $status );
		} else {
			$this->status = null;
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
	
	/* ---------- One-to-Many Association Getter/Setters ---------- */
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getBulkOperations() {
		return $this->bulkOperations;
	}
	
	public function setBulkOperations(ArrayCollection $bulkOperations) {
		$this->bulkOperations = $bulkOperations;
	}
	
	public function addBulkOperation(BulkOperation $bulkOperation) {
		$bulkOperation->setMarketingList( $this );
		$this->bulkOperations->add( $bulkOperation );
	}
	
	public function removeBulkOperation(BulkOperation $bulkOperation) {
		$bulkOperation->setMarketingList( null );
		$this->bulkOperations->removeElement( $bulkOperation );
	}
	
	/* ---------- Many-to-Many Association Getter/Setters ---------- */
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCampaignActivities() {
		return $this->campaignActivities;
	}

	public function setCampaignActivities(ArrayCollection $campaignActivities) {
		$this->campaignActivities = $campaignActivities;
	}

	/**
	 * Inverse side. Integrity maintained on owning side.
	 * @param CampaignActivity $campaignActivity
	 */
	public function addCampaignActivity(CampaignActivity $campaignActivity) {
		$this->campaignActivities->add( $campaignActivity );
	}

	/**
	 * Inverse side. Integrity maintained on owning side.
	 * @param CampaignActivity $campaignActivity
	 */
	public function removeCampaignActivity(CampaignActivity $campaignActivity) {
		$this->campaignActivities->removeElement( $campaignActivity );
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCampaigns() {
		return $this->campaigns;
	}

	public function setCampaigns(ArrayCollection $campaigns) {
		$this->campaigns = $campaigns;
	}

	/**
	 * Inverse side. Integrity maintained on owning side.
	 * @param Campaign $campaign
	 */
	public function addCampaign(Campaign $campaign) {
		$this->campaigns->add( $campaign );
	}

	/**
	 * Inverse side. Integrity maintained on owning side.
	 * @param Campaign $campaign
	 */
	public function removeCampaign(Campaign $campaign) {
		$this->campaigns->removeElement( $campaign );
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAccounts() {
		return $this->accounts;
	}

	public function setAccounts(ArrayCollection $accounts) {
		$this->accounts = $accounts;
	}

	/**
	 * @param Account $account
	 */
	public function addAccount(Account $account) {
		$account->addList( $this );
		$this->accounts->add( $account );
	}

	/**
	 * @param Account $account
	 */
	public function removeAccount(Account $account) {
		$account->removeList( $this );
		$this->accounts->removeElement( $account );
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getContacts() {
		return $this->contacts;
	}

	public function setContacts(ArrayCollection $contacts) {
		$this->contacts = $contacts;
	}

	/**
	 * @param Contact $contact
	 */
	public function addContact(Contact $contact) {
		$contact->addList( $this );
		$this->contacts->add( $contact );
	}

	/**
	 * @param Contact $contact
	 */
	public function removeContact(Contact $contact) {
		$contact->removeList( $this );
		$this->contacts->removeElement( $contact );
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getLeads() {
		return $this->leads;
	}

	public function setLeads(ArrayCollection $leads) {
		$this->leads = $leads;
	}

	/**
	 * @param Lead $lead
	 */
	public function addLead(Lead $lead) {
		$lead->addList( $this );
		$this->leads->add( $lead );
	}

	/**
	 * @param Lead $lead
	 */
	public function removeLead(Lead $lead) {
		$lead->removeList( $this );
		$this->leads->removeElement( $lead );
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
		if (($o->getName() == $this->getName())
			&& ($o->getPurpose() == $this->getPurpose())
			&& ($o->getLastUsed() != null && $this->getLastUsed() != null && $o->getLastUsed()->getTimestamp() == $this->getLastUsed()->getTimestamp())) {
			return true;
		}
		return false;
	}

	/**
	 * @return string
	 * @see \Application\Stdlib\Object::getClass()
	 */
	public function getClass() {
		return get_class( $this );
	}
	
	/**
	 * Returns the list members given the member type
	 *
	 * @return array:
	 */
	public function getListMembers() {
		$result = array();
		switch ($this->getMemberType()) {
			case MemberType::ACCOUNT:
				$result = $this->getAccounts()->toArray();
				break;
			case MemberType::CONTACT:
				$result = $this->getContacts()->toArray();
				break;
			case MemberType::LEAD;
				$result = $this->getLeads()->toArray();
				break;
		}
		return $result;
	}
	
	public function getNameAsLink() {
		$result = '';
		if($this->getId()) {
			$result ='<a href="/marketingList/edit/' . $this->getId() . '" target="_blank">' . $this->getName() . '</a>';
		}
		return $result;
	}
	
	/**
	 * Removes any matching list members from the given array
	 *
	 * @param array $candidates
	 * @return array
	 */
	public function removeMatchedCandidates(array $candidates) {
		/* @var $account Account */
		/* @var $contact Contact */
		/* @var $lead Lead */
		
		$result = array();
		switch ($this->getMemberType()) {
			case MemberType::ACCOUNT:
				foreach ($candidates as $account) {
					if(!$this->accounts->contains($account)) {
						$result[] = $account;
					}
				}
				break;
			case MemberType::CONTACT:
				foreach ($candidates as $contact) {
					if(!$this->contacts->contains($contact)) {
						$result[] = $contact;
					}
				}
				break;
			case MemberType::LEAD;
				foreach ($candidates as $lead) {
					if(!$this->leads->contains($lead)) {
						$result[] = $lead;
					}
				}
				break;
		}
		return $result;
	}
	
	/**
	 * @param string $format
	 * @return string
	 */
	public function getFormattedLastUsedDate($format = null) {
		if($format == null) {
			$format = 'm/d/Y';
		}
		$result = '';
		if($this->getLastUsed() != null) {
			$result = $this->getLastUsed()->format($format);
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'MarketingList[id=' . $this->getId()
		. ',businessUnit=' . ($this->getBusinessUnit() != null ? $this->getBusinessUnit()->getId() : '')
		. ',description=' . $this->getDescription()
		. ',lastUsed=' . ($this->getLastUsed() != null ? $this->getLastUsed()->format( 'Y-m-d' ) : '')
		. ',lockStatus=' . ($this->getLockStatus() ? 'true' : 'false')
		. ',memberCount=' . $this->getMemberCount()
		. ',memberType=' . $this->getMemberType()
		. ',name=' . $this->getName()
		. ',owner=' . $this->getOwner()->getId()
		. ',purpose=' . $this->getPurpose()
		. ',source=' . $this->getSource()
		. ',state=' . $this->getState()
		. ',status=' . $this->getStatus()
		. ',creationDate=' . $this->getCreationDate()->format( 'Y-m-d H:i:s' )
		. ',lastUpdateDate=' . $this->getLastUpdateDate()->format( 'Y-m-d H:i:s' )
		. ']';
	}
}
?>