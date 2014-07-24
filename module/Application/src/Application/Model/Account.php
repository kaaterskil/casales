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
 * @version     SVN $Id: Account.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractActivity;
use Application\Model\AccountSource;
use Application\Model\AccountState;
use Application\Model\AccountStatus;
use Application\Model\AccountType;
use Application\Model\AddressDTO;
use Application\Model\Audit;
use Application\Model\Auditable;
use Application\Model\BusinessUnit;
use Application\Model\Contact;
use Application\Model\Contactable;
use Application\Model\Lead;
use Application\Model\MarketingList;
use Application\Model\Opportunity;
use Application\Model\User;

use Application\Service\OrderOpenActivitiesRequest;
use Application\Service\OrderClosedActivitiesRequest;

use Application\Stdlib\Entity;
use Application\Stdlib\Object;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Ldap\Node\AbstractNode;

/**
 * A business that represents a customer or potential customer. An account is the company
 * that is billed in business transactions.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Account.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_account")
 */
class Account implements Entity, Contactable, Auditable {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;
	
	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="AccountGroup", inversedBy="accounts")
	 * @ORM\JoinColumn(name="account_group_id", referencedColumnName="id")
	 * @var AccountGroup
	 */
	private $accountGroup;
	
	/**
	 * @ORM\Column(type="string", name="account_type")
	 * @var AccountType
	 */
	private $accountType;
	
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
	private $category;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description;
	
	/**
	 * @ORM\Column(type="integer", name="do_not_call")
	 * @var boolean
	 */
	private $doNotCall = false;
	
	/**
	 * @ORM\Column(type="integer", name="do_not_mail")
	 * @var boolean
	 */
	private $doNotMail = false;
	
	/**
	 * @ORM\Column(type="integer", name="do_not_email")
	 * @var boolean
	 */
	private $doNotEmail = false;
	
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
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $name;
	
	/**
	 * @ORM\Column(type="string", name="notes")
	 * @var string
	 */
	private $note;
	
	/**
	 * Unidirectional Many-to-One
	 * @ORM\ManyToOne(targetEntity="Lead")
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
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Account", inversedBy="childAccounts")
	 * @ORM\JoinColumn(name="parent_account_id", referencedColumnName="id")
	 * @var Account
	 */
	private $parentAccount;
	
	/**
	 * Unidirectional Many-to-One
	 * @ORM\ManyToOne(targetEntity="Contact")
	 * @ORM\JoinColumn(name="primary_contact_id", referencedColumnName="id")
	 * @var Contact
	 */
	private $primaryContact;
	
	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Account", inversedBy="referrals")
	 * @ORM\JoinColumn(name="referral_id", referencedColumnName="id")
	 * @var Account
	 */
	private $referrer = null;
	
	/**
	 * @ORM\Column(type="string")
	 * @var AccountSource
	 */
	private $source;
	
	/**
	 * @ORM\Column(type="string")
	 * @var AccountState
	 */
	private $state;
	
	/**
	 * @ORM\Column(type="string")
	 * @var AccountStatus
	 */
	private $status;
	
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
	 * @ORM\OneToMany(targetEntity="Address", mappedBy="account", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $addresses;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractAppointment", mappedBy="account", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $appointments;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Audit", mappedBy="account", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $auditItems;
	
	/**
	 * Bidirectional Many-to-One: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Account", mappedBy="parentAccount", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $childAccounts;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Contact", mappedBy="account", cascade={"persist"})
	 * @ORM\OrderBy({"lastName" = "asc"})
	 * @var ArrayCollection
	 */
	private $contacts;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractInteraction", mappedBy="account", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $interactions;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Lead", mappedBy="account", cascade={"persist"})
	 * @ORM\OrderBy({"lastName" = "asc"})
	 * @var ArrayCollection
	 */
	private $leads;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractNote", mappedBy="account", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $notes;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Opportunity", mappedBy="account", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $opportunities;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Account", mappedBy="referrer")
	 * @var ArrayCollection
	 */
	private $referrals;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractTask", mappedBy="account", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $tasks;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Telephone", mappedBy="account", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $telephones;
	
	/* ---------- Many-to-Many Associations ---------- */
	
	/**
	 * INVERSE SIDE
	 * @ORM\ManyToMany(targetEntity="MarketingList", mappedBy="accounts")
	 * @var ArrayCollection
	 */
	private $lists;
	
	/* ----------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->addresses = new ArrayCollection();
		$this->appointments = new ArrayCollection();
		$this->auditItems = new ArrayCollection();
		$this->childAccounts = new ArrayCollection();
		$this->contacts = new ArrayCollection();
		$this->interactions = new ArrayCollection();
		$this->leads = new ArrayCollection();
		$this->notes = new ArrayCollection();
		$this->opportunities = new ArrayCollection();
		$this->referrals = new ArrayCollection();
		$this->tasks = new ArrayCollection();
		$this->telephones = new ArrayCollection();
		$this->lists = new ArrayCollection();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/**
	 * @return \Application\Model\AccountGroup
	 */
	public function getAccountGroup() {
		return $this->accountGroup;
	}

	public function setAccountGroup(AccountGroup $group = null) {
		$this->accountGroup = $group;
	}

	/**
	 * @return \Application\Model\AccountType
	 */
	public function getAccountType() {
		return $this->accountType;
	}

	public function setAccountType($accountType = null) {
		if ($accountType instanceof AccountType) {
			$this->accountType = $accountType;
		} elseif($accountType != null) {
			$this->accountType = AccountType::instance( $accountType );
		} else {
			$this->accountType = null;
		}
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
	 * @return string
	 */
	public function getCategory() {
		return $this->category;
	}
	
	public function setCategory($category) {
		$this->category = (string) $category;
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
	 * return boolean
	 */
	public function getDoNotCall() {
		return $this->doNotCall;
	}

	public function setDoNotCall($do_not_call) {
		if (is_bool( $do_not_call ) || is_numeric( $do_not_call )) {
			$this->doNotCall = (bool) $do_not_call;
		} else {
			$this->doNotCall = $do_not_call == 'true' ? true : false;
		}
	}

	/**
	 * return boolean
	 */
	public function getDoNotMail() {
		return $this->doNotMail;
	}

	public function setDoNotMail($do_not_mail) {
		if (is_bool( $do_not_mail ) || is_numeric( $do_not_mail )) {
			$this->doNotMail = (bool) $do_not_mail;
		} else {
			$this->doNotMail = $do_not_mail == 'true' ? true : false;
		}
	}

	/**
	 * return boolean
	 */
	public function getDoNotEmail() {
		return $this->doNotEmail;
	}

	public function setDoNotEmail($do_not_email) {
		if (is_bool( $do_not_email ) || is_numeric( $do_not_email )) {
			$this->doNotEmail = (bool) $do_not_email;
		} else {
			$this->doNotEmail = $do_not_email == 'true' ? true : false;
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
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * return string
	 */
	public function getNote() {
		return $this->note;
	}

	public function setNote($note) {
		$this->note = $note;
	}
	
	/**
	 * @return Lead
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
	 * @return Account
	 */
	public function getParentAccount() {
		return $this->parentAccount;
	}
	
	public function setParentAccount(Account $parentAccount = null) {
		$this->parentAccount = $parentAccount;
	}
	
	/**
	 * @return Contact
	 */
	public function getPrimaryContact() {
		return $this->primaryContact;
	}
	
	public function setPrimaryContact(Contact $primaryContact = null) {
		$this->primaryContact = $primaryContact;
	}

	/**
	 * @return \Application\Model\Account
	 */
	public function getReferrer() {
		return $this->referrer;
	}

	public function setReferrer(Account $referrer = null) {
		$this->referrer = $referrer;
	}

	/**
	 * @return \Application\Model\AccountSource
	 */
	public function getSource() {
		return $this->source;
	}

	public function setSource($source = null) {
		if ($source instanceof AccountSource) {
			$this->source = $source;
		} elseif ($source != null) {
			$this->source = AccountSource::instance( $source );
		} else {
			$this->source = null;
		}
	}
	
	/**
	 * @return AccountState
	 */
	public function getState() {
		return $this->state;
	}
	
	public function setState($state = null) {
		if($state instanceof AccountState) {
			$this->state = $state;
		} elseif($state != null) {
			$this->state = AccountState::instance($state);
		} else {
			$this->state = null;
		}
	}
	
	/**
	 * @return AccountStatus
	 */
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus($status = null) {
		if($status instanceof AccountStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = AccountStatus::instance($status);
		} else {
			$this->status = null;
		}
	}

	/**
	 * return string
	 */
	public function getWebsite() {
		return $this->website;
	}

	public function setWebsite($website) {
		$this->website = $website;
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
	 * Returns a collection of addresses
	 * @return \Doctrine\Common\Collections\ArrayCollection
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

	public function addAddress(Address $address) {
		$address->setAccount( $this );
		$this->addresses->add( $address );
	}

	public function removeAddress(Address $address) {
		$address->setAccount( null );
		$this->addresses->removeElement( $address );
	}

	/**
	 * Returns a collection of appointment activities
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAppointments() {
		return $this->appointments;
	}
	
	public function setAppointments(ArrayCollection $appointments) {
		$this->appointments = $appointments;
	}
	
	public function getChildAccounts() {
		return $this->childAccounts;
	}
	
	public function setChildAccounts(ArrayCollection $childAccounts) {
		$this->childAccounts = $childAccounts;
	}
	
	public function addChildAccount(Account $childAccount) {
		$childAccount->setParentAccount($this);
		$this->childAccounts->add($childAccount);
	}
	
	public function removeChildAccount(Account $childAccount) {
		$childAccount->setParentAccount(null);
		$this->childAccounts->removeElement($childAccount);
	}
	
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
		$auditItem->setAccount( $this );
		$this->auditItems->add( $auditItem );
	}
	
	/**
	 * @param Audit $auditItem
	 * @see \Application\Model\Auditable::removeAuditItem()
	 */
	public function removeAuditItem(Audit $auditItem) {
		$auditItem->setAccount( null );
		$this->auditItems->removeElement( $auditItem );
	}
	
	/**
	 * Returns a collection of contacts
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getContacts() {
		return $this->contacts;
	}

	public function setContacts(ArrayCollection $contacts) {
		$this->contacts = $contacts;
	}

	public function addContact(Contact $contact) {
		$contact->setAccount( $this );
		$this->contacts->add( $contact );
	}

	public function removeContact(Contact $contact) {
		$this->contacts->removeElement( $contact );
		$contact->setAccount( null );
	}

	/**
	 * Returns a collection of interaction activities
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getInteractions() {
		return $this->interactions;
	}
	
	public function setInteractions(ArrayCollection $interactions) {
		$this->interactions = $interactions;
	}
	
	/**
	 * Returns a collection of leads
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getLeads() {
		return $this->leads;
	}
	
	public function setLeads(ArrayCollection $leads) {
		$this->leads = $leads;
	}
	
	public function addLead(Lead $lead) {
		$lead->setAccount($this);
		$this->leads->add($lead);
	}
	
	public function removeLead(Lead $lead) {
		$lead->setAccount(null);
		$this->leads->removeElement($lead);
	}

	/**
	 * Returns a collection of note activities
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getNotes() {
		return $this->notes;
	}
	
	public function setNotes(ArrayCollection $notes) {
		$this->notes = $notes;
	}
	
	public function addNote(AbstractNote $note) {
		$note->setAccount($this);
		$this->notes->add($note);
	}
	
	public function removeNote(AbstractNote $note) {
		$note->setAccount(null);
		$this->notes->removeElement($note);
	}

	/**
	 * Returns a collection of opportunities
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getOpportunities() {
		return $this->opportunities;
	}

	public function setOpportunities(ArrayCollection $opportunities) {
		$this->opportunities = $opportunities;
	}

	public function addOpportunity(Opportunity $opportunity) {
		$opportunity->setAccount( $this );
		$this->opportunities->add( $opportunity );
	}

	public function removeOpportunity(Opportunity $opportunity) {
		$this->opportunities->removeElement( $opportunity );
		$opportunity->setAccount( null );
	}

	/**
	 * Returns a collection of referrals
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getReferrals() {
		return $this->referrals;
	}

	public function setReferrals(ArrayCollection $referrals) {
		$this->referrals = $referrals;
	}

	public function addReferral(Account $referral) {
		$referral->setReferrer( $this );
		$this->referrals->add( $referral );
	}

	public function removeReferral(Account $referral) {
		$this->referrals->removeElement( $referral );
		$referral->setReferrer( null );
	}

	/**
	 * Returns a collection of task activities
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTasks() {
		return $this->tasks;
	}
	
	public function setTasks(ArrayCollection $tasks) {
		$this->tasks = $tasks;
	}
	
	public function addTask(AbstractTask $task) {
		$task->setAccount($this);
		$this->tasks->add($task);
	}
	
	public function removeTask(AbstractTask $task) {
		$task->setAccount(null);
		$this->tasks->removeElement($task);
	}

	/**
	 * Returns a collection of telephones
	 * @return \Doctrine\Common\Collections\ArrayCollection
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

	public function addTelephone(Telephone $telephone) {
		$telephone->setAccount( $this );
		$this->telephones->add( $telephone );
	}

	public function removeTelephone(Telephone $telephone) {
		$telephone->setAccount( null );
		$this->telephones->removeElement( $telephone );
	}

	public function addActivities(ArrayCollection $activities) {
		foreach ( $activities->getValues() as $activity ) {
			$this->addActivity( $activity );
		}
	}

	public function removeActivities(ArrayCollection $activities) {
		foreach ( $activities->getValues() as $activity ) {
			$this->removeActivity( $activity );
		}
	}

	public function addActivity(AbstractActivity $activity) {
		$activity->setAccount( $this );
		if ($activity instanceof AbstractAppointment) {
			$this->appointments->add( $activity );
		} elseif ($activity instanceof AbstractInteraction) {
			$this->interactions->add( $activity );
		} elseif ($activity instanceof AbstractNote) {
			$this->notes->add( $activity );
		} elseif ($activity instanceof AbstractTask) {
			$this->tasks->add( $activity );
		} else {
			throw new \InvalidArgumentException( 'Unknown Activity class: ' . $activity->getClass() );
		}
	}

	public function removeActivity(AbstractActivity $activity) {
		$activity->setAccount( null );
		if ($activity instanceof AbstractAppointment) {
			$this->appointments->removeElement( $activity );
		} elseif ($activity instanceof AbstractInteraction) {
			$this->interactions->removeElement( $activity );
		} elseif ($activity instanceof AbstractNote) {
			$this->notes->removeElement( $activity );
		} elseif ($activity instanceof AbstractTask) {
			$this->tasks->removeElement( $activity );
		} else {
			throw new \InvalidArgumentException( 'Unknown Activity class: ' . $activity->getClass() );
		}
	}
	
	/* ---------- Many-to-Many Association Getter/Setters ---------- */
	
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
	 * Inverse side. Integrity maintained on owning side.
	 * @param MarketingList $list
	 */
	public function addList(MarketingList $list) {
		$this->lists->add($list);
	}
	
	/**
	 * Inverse side. Integrity maintained on owning side.
	 * @param MarketingList $list
	 */
	public function removeList(MarketingList $list) {
		$this->lists->removeElement($list);
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @param Object $o
	 * @see \Application\Stdlib\Object::equals()
	 */
	public function equals(Object $o) {
		if (! $o instanceof $this) {
			throw new \ClassCastException();
		}
		if ($o->getId() == $this->getId()) {
			return true;
		}
		if (($o->getName() == $this->getName())
			&& ($o->getAccountType()->equals( $this->getAccountType() ))
			&& ($o->getAccountGroup()->equals( $this->getAccountGroup() ))) {
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
			'childAccounts',
			'contacts',
			'interactions',
			'leads',
			'lists',
			'notes',
			'opportunities',
			'referrals',
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
	 * @param boolean $includeLink
	 * @return string
	 * @see \Application\Model\Regarding::getDisplayName()
	 */
	public function getDisplayName($includeLink = false) {
		$result = $this->getName();
		if(strlen($result) > 25) {
			$result = substr($result, 0, 25) . '...';
		}
		if($includeLink) {
			$result = '<a href="/account/edit/' . $this->getId() . '" target="_blank">' . $result . '</a>';
		}
		return $result;
	}

	/**
	 * Returns a collection of open activities
	 * @return array
	 */
	public function getOpenActivities() {
		/* @var $contact Contact */
		/* @var $lead Lead */
		
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
		
		foreach ($this->getContacts()->getValues() as $contact) {
			$activities = $contact->getOpenActivities();
			$result = array_merge($result, $activities);
		}
		
		foreach ($this->getLeads()->getValues() as $lead) {
			$activities = $lead->getOpenActivities();
			$result = array_merge($result, $activities);
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
		/* @var $contact Contact */
		/* @var $lead Lead */
		
		$result = array();
		foreach ( $this->getAppointments()->getValues() as $activity ) {
			if (! $this->isOpenActivity( $activity )) {
				$result[] = $activity;
			}
		}
		foreach ( $this->getInteractions()->getValues() as $activity ) {
			if (! $this->isOpenActivity( $activity )) {
				$result[] = $activity;
			}
		}
		foreach ( $this->getNotes()->getValues() as $activity ) {
			$result[] = $activity;
		}
		foreach ( $this->getTasks()->getValues() as $activity ) {
			if (! $this->isOpenActivity( $activity )) {
				$result[] = $activity;
			}
		}
		
		foreach ($this->getContacts()->getValues() as $contact) {
			$activities = $contact->getClosedActivities();
			$result = array_merge($result, $activities);
		}
		
		foreach ($this->getLeads()->getValues() as $lead) {
			$activities = $lead->getClosedActivities();
			$result = array_merge($result, $activities);
		}
		
		$comparator = new OrderClosedActivitiesRequest($result);
		$response = $comparator->execute();
		if($response->getResult()) {
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
		
		if($this->getAddresses()->count()) {
			$address = $this->getAddresses()->first();
			$street = strlen($address->getAddress1()) > 25
					? substr($address->getAddress1(), 0, 25) . '...' : $address->getAddress1();
			$region = $address->getRegion() != null ? $address->getRegion()->getAbbreviation() : '';
			
			$result->setStreet($street);
			$result->setCity($address->getCity());
			$result->setRegion($region);
			$result->setPostalCode($address->getPostalCode());
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
		if($this->getTelephones()->count()) {
			foreach ($this->getTelephones()->getValues() as $telephone) {
				if($telephone->getIsPrimary()) {
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
		if($this->getEmail1() != '') {
			$displayName = $this->getName();
			$addrSpec = $this->getEmail1();
			
			// Test for special characters
			if(preg_match('[@<>,]', $displayName)) {
				$displayName = '"' . $displayName . '"';
			}
			
			// Test length
			$result = $displayName . ' <' . $addrSpec . '>';
			if(strlen($result) > 255) {
				$result = $addrSpec;
			}
		}
		return $result;
	}
	
	public function __toString() {
		return 'Account[id=' . $this->getId()
		. ',accountGroup=' . $this->getAccountGroup()->getDescription()
		. ',accountType=' . $this->getAccountType()->getName()
		. ',category=' . $this->getCategory()
		. ',description=' . $this->getDescription()
		. ',doNotCall=' . ($this->getDoNotCall() ? 'true' : 'false')
		. ',doNotMail=' . ($this->doNotMail ? 'true' : 'false')
		. ',doNotEmail=' . ($this->getDoNotEmail() ? 'true' : 'false')
		. ',email1=' . $this->getEmail1()
		. ',email2=' . $this->getEmail2()
		. ',name=' . $this->getName()
		. ',notes=' . $this->getNotes()
		. ',originatingLead=' . ($this->getOriginatingLead() != null ? $this->getOriginatingLead()->getId() : '')
		. ',parentAccount=' . ($this->getParentAccount() != null ? $this->getParentAccount()->getId() : '')
		. ',primaryContact=' . ($this->getPrimaryContact() != null ? $this->getPrimaryContact()->getId() : '')
		. ',referrer=' . ($this->getReferrer() != null ? $this->getReferrer()->getId() : '')
		. ',source=' . $this->getSource()
		. ',state=' . $this->getState()
		. ',website=' . $this->getWebsite()
		. ',creationDate=' . $this->getCreationDate()->format( 'Y-m-d H:i:s' )
		. ',lastUpdateDate=' . $this->getLastUpdateDate()->format( 'Y-m-d H:i:s' )
		. ']';
	}
	
	/**
	 * Adds the account's primary contact address and telephones
	 *
	 * @return void
	 */
	public function importPrimaryContactInformation() {
		$contact = $this->getPrimaryContact();
		foreach ($contact->getAddresses() as $address) {
			$this->addresses->add($address);
		}
		foreach ($contact->getTelephones() as $telephone) {
			$this->telephones->add($telephone);
		}
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