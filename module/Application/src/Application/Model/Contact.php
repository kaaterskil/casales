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
 * @version     SVN $Id: Contact.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;
use Application\Model\Account;
use Application\Model\ActivityState;
use Application\Model\Address;
use Application\Model\AddressDTO;
use Application\Model\Audit;
use Application\Model\Auditable;
use Application\Model\BusinessUnit;
use Application\Model\Contactable;
use Application\Model\ContactSortField;
use Application\Model\ContactState;
use Application\Model\ContactStatus;
use Application\Model\Gender;
use Application\Model\Lead;
use Application\Model\MarketingList;
use Application\Model\Salutation;
use Application\Model\StatefulActivity;
use Application\Model\Telephone;
use Application\Model\User;

use Application\Service\OrderOpenActivitiesRequest;
use Application\Service\OrderClosedActivitiesRequest;

use Application\Stdlib\Entity;
use Application\Stdlib\Object;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Zend\Crypt\PublicKey\Rsa\PublicKey;
use Zend\View\Helper\Url;
use Zend\View\Model\ViewModel;

/**
 * Represents a person with whom a business unit has a relationship, for example, a customer,
 * a supplier, or a colleague.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Contact.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_contact")
 */
class Contact implements Entity, Contactable, Auditable {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Account", inversedBy="contacts")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
	 * @var Account
	 */
	private $account;

	/**
	 * @ORM\Column(type="string", name="assistant_email")
	 * @var string
	 */
	private $assistantEmail;

	/**
	 * @ORM\Column(type="string", name="assistant_name")
	 * @var string
	 */
	private $assistantName;

	/**
	 * @ORM\Column(type="string", name="assistant_phone")
	 * @var string
	 */
	private $assistantTelephone;

	/**
	 * @ORM\Column(type="date", name="birth_date")
	 * @var \DateTime
	 */
	private $birthDate;
	
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
	 * @ORM\Column(type="integer", name="do_not_call")
	 * @var boolean
	 */
	private $doNotCall = false;

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
	 * @ORM\Column(type="string", name="first_name")
	 * @var string
	 */
	private $firstName;

	/**
	 * @ORM\Column(type="string", name="full_name")
	 * @var string
	 */
	private $displayName;

	/**
	 * @ORM\Column(type="string")
	 * @var Gender
	 */
	private $gender;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $interests;

	/**
	 * @ORM\Column(type="integer", name="is_primary_contact")
	 * @var boolean
	 */
	private $isPrimaryContact = false;

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
	 * @ORM\Column(type="string", name="middle_name")
	 * @var string
	 */
	private $middleName;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $nickname;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
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
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $prefix;

	/**
	 * @ORM\Column(type="string")
	 * @var Salutation
	 */
	private $salutation;

	/**
	 * @ORM\Column(type="string", name="sort_name")
	 * @var ContactSortField
	 */
	private $sortName;

	/**
	 * @ORM\Column(type="string")
	 * @var ContactState
	 */
	private $state;

	/**
	 * @ORM\Column(type="string")
	 * @var ContactStatus
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
	 * @ORM\OneToMany(targetEntity="AbstractAppointment", mappedBy="contact", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $appointments;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Audit", mappedBy="contact", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $auditItems;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractInteraction", mappedBy="contact", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $interactions;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractNote", mappedBy="contact", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $notes;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractTask", mappedBy="contact", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $tasks;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Address", mappedBy="contact", cascade={"persist", "remove"}, fetch="EAGER")
	 * @var ArrayCollection
	 */
	private $addresses;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Telephone", mappedBy="contact", cascade={"persist", "remove"}, fetch="EAGER")
	 * @var ArrayCollection
	 */
	private $telephones;
	
	/**
	 * INVERSE SIDE
	 * @ORM\ManyToMany(targetEntity="MarketingList", mappedBy="contacts")
	 * @var ArrayCollection
	 */
	private $lists;
	
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
		$this->addresses = new ArrayCollection();
		$this->telephones = new ArrayCollection();
		$this->lists = new ArrayCollection();
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
	 * @return string
	 */
	public function getAssistantEmail() {
		return $this->assistantEmail;
	}
	
	public function setAssistantEmail($assistantEmail) {
		$this->assistantEmail = (string) $assistantEmail;
	}

	/**
	 * @return string
	 */
	public function getAssistantName() {
		return $this->assistantName;
	}

	public function setAssistantName($assistantName) {
		$this->assistantName = $assistantName;
	}

	/**
	 * @return string
	 */
	public function getAssistantTelephone() {
		return $this->assistantTelephone;
	}

	public function setAssistantTelephone($assistantTelephone) {
		$unformatted = Telephone::unformatPhoneNumber( $assistantTelephone );
		$this->assistantTelephone = Telephone::formatPhoneNumber( $unformatted );
	}

	/**
	 * @return DateTime
	 */
	public function getBirthDate() {
		return $this->birthDate;
	}

	public function setBirthDate($birthDate = null) {
		if ($birthDate instanceof \DateTime) {
			$now = new \DateTime();
			if ($birthDate->format( 'Y-m-d' ) == $now->format( 'Y-m-d' )) {
				$this->birthDate = null;
				return;
			}
		}
		$this->birthDate = $birthDate;
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
	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return boolean
	 */
	public function getDoNotCall() {
		return $this->doNotCall;
	}

	public function setDoNotCall($doNotCall) {
		if (is_bool( $doNotCall ) || is_numeric( $doNotCall )) {
			$this->doNotCall = (bool) $doNotCall;
		} else {
			$this->doNotCall = $doNotCall == 'true' ? true : false;
		}
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
	 * @return string
	 * @see \Application\Model\Regarding::getEmail1()
	 */
	public function getEmail1() {
		return $this->email1;
	}

	public function setEmail1($email1) {
		$this->email1 = $email1;
	}

	/**
	 * @return string
	 */
	public function getEmail2() {
		return $this->email2;
	}

	public function setEmail2($email2) {
		$this->email2 = $email2;
	}

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	/**
	 * @param boolean $includeLink
	 * @return string
	 * @see \Application\Model\Regarding::getDisplayName()
	 */
	public function getDisplayName($includeLink = null) {
		$result = $this->displayName;
		if($includeLink) {
			$result = '<a href="/contact/edit/' . $this->getId() . '" target="_blank">' . $result . '</a>';
		}
		return $result;
	}

	public function setDisplayName($displayName) {
		$this->displayName = $displayName;
	}

	/**
	 * @return \Application\Model\Gender
	 */
	public function getGender() {
		return $this->gender;
	}

	public function setGender($gender = null) {
		if ($gender instanceof Gender) {
			$this->gender = $gender;
		} elseif ($gender != null) {
			$this->gender = Gender::instance( $gender );
		} else {
			$this->gender = null;
		}
	}

	/**
	 * @return string
	 */
	public function getInterests() {
		return $this->interests;
	}

	public function setInterests($interests) {
		$this->interests = $interests;
	}

	/**
	 * @return boolean
	 */
	public function getIsPrimaryContact() {
		return $this->isPrimaryContact;
	}

	public function setIsPrimaryContact($isPrimaryContact) {
		if (is_bool( $isPrimaryContact ) || is_numeric( $isPrimaryContact )) {
			$this->isPrimaryContact = (bool) $isPrimaryContact;
		} else {
			$this->isPrimaryContact = $isPrimaryContact == 'true' ? true : false;
		}
	}

	/**
	 * @return string
	 */
	public function getJobTitle() {
		return $this->jobTitle;
	}

	public function setJobTitle($jobTitle) {
		$this->jobTitle = $jobTitle;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/**
	 * @return string
	 */
	public function getMiddleName() {
		return $this->middleName;
	}

	public function setMiddleName($middleName) {
		$this->middleName = $middleName;
	}

	/**
	 * @return string
	 */
	public function getNickname() {
		return $this->nickname;
	}

	public function setNickname($nickname) {
		$this->nickname = $nickname;
	}

	/**
	 * @return Lead
	 */
	public function getOriginatingLead() {
		return $this->originatingLead;
	}

	public function setOriginatingLead(Lead $originatingLead = null) {
		$this->originatingLead = $originatingLead;
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
		$this->prefix = $prefix;
	}

	/**
	 * @return \Application\Model\Salutation
	 */
	public function getSalutation() {
		return $this->salutation;
	}

	public function setSalutation($salutation) {
		if ($salutation instanceof Salutation) {
			$this->salutation = $salutation;
		} elseif ($salutation != null) {
			$this->salutation = Salutation::instance( $salutation );
		} else {
			$this->salutation = null;
		}
	}

	/**
	 * @return \Application\Model\ContactSortField
	 */
	public function getSortName() {
		return $this->sortName;
	}

	public function setSortName($sortName = null) {
		if ($sortName instanceof ContactSortField) {
			$this->sortName = $sortName;
		} elseif ($sortName != null) {
			$this->sortName = ContactSortField::instance( $sortName );
		} else {
			$this->sortName = null;
		}
	}

	/**
	 * @return ContactState
	 */
	public function getState() {
		return $this->state;
	}

	public function setState($state = null) {
		if ($state instanceof ContactState) {
			$this->state = $state;
		} elseif ($state != null) {
			$this->state = ContactState::instance( $state );
		} else {
			$this->state = null;
		}
	}

	/**
	 * @return ContactStatus
	 */
	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status = null) {
		if ($status instanceof ContactStatus) {
			$this->status = $status;
		} elseif ($status != null) {
			$this->status = ContactStatus::instance( $status );
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
		$this->suffix = $suffix;
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
	
	/* ----- Collections ----- */
	
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
		$auditItem->setContact($this);
		$this->auditItems->add($auditItem);
	}
	
	/**
	 * @param Audit $auditItem
	 * @see \Application\Model\Auditable::removeAuditItem()
	 */
	public function removeAuditItem(Audit $auditItem) {
		$auditItem->setContact( null );
		$this->auditItems->removeElement($auditItem);
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
	 * Returns a collection of note activities
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getNotes() {
		return $this->notes;
	}

	public function setNotes(ArrayCollection $notes) {
		$this->notes = $notes;
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
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getLists() {
		return $this->lists;
	}
	
	public function setLists(ArrayCollection $lists) {
		$this->lists = $lists;
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
		$activity->setContact( $this );
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
		$activity->setContact( null );
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

	/**
	 * Returns a collection of Addresses
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

	public function addAddress(Address $address) {
		$address->setContact( $this );
		$this->addresses->add( $address );
	}

	public function removeAddress(Address $address) {
		$this->addresses->removeElement( $address );
		$address->setContact( null );
	}

	/**
	 * Returns a collection of Telephones
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

	public function addTelephone(Telephone $telephone) {
		$telephone->setContact( $this );
		$this->telephones->add( $telephone );
	}

	public function removeTelephone(Telephone $telephone) {
		$this->telephones->removeElement( $telephone );
		$telephone->setContact( null );
	}
	
	/**
	 * @param MarketingList $list
	 */
	public function addList(MarketingList $list) {
		$this->lists->add($list);
	}
	
	/**
	 * @param MarketingList $list
	 */
	public function removeList(MarketingList $list) {
		$this->lists->removeElement($list);
	}
	
	/* ----- Methods ----- */
	
	/**
	 * Returns a formatted string of the Contact's name
	 * @return string
	 */
	public function computeDisplayName() {
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
	 * @see \Application\Stdlib\Object::equals()
	 */
	public function equals(Object $o) {
		if (!$o instanceof $this) {
			return false;
		}
		if ($o->getId() == $this->getId()) {
			return true;
		}
		if (($o->computeDisplayName() == $this->computeDisplayName())
				&& ($o->getAccount()->equals( $this->getAccount() ))) {
			return true;
		}
		return false;
	}

	/**
	 * Returns an anchor link for the Contact's parent Account listing, if any
	 * @return string
	 */
	public function getAccountLink() {
		$result = '';
		if ($this->getAccount()) {
			$link = '/account/edit/' . $this->getAccount()->getId();
			$result = '<a href="' . $link . '">' . $this->getShortAccountName() . '</a>';
		}
		return $result;
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
			'lists',
			'notes',
			'telephones',
			'tasks'
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
		
		$comparator = new OrderClosedActivitiesRequest($result);
		$response = $comparator->execute();
		if($response->getResult()) {
			$result = $response->getCollection();
		}
		return $result;
	}

	/**
	 * Returns an anchor link for the Contact's email listing, if exists
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
		
		$comparator = new OrderOpenActivitiesRequest($result);
		$response = $comparator->execute();
		if($response->getResult()) {
			$result = $response->getCollection();
		}
		return $result;
	}

	/**
	 * Returns primary address (or first address if applicable) information for listing
	 * @return AddressDTO
	 * @see \Application\Model\Contactable::getPrimaryAddress()
	 */
	public function getPrimaryAddress() {
		/* @var $address Address */
		$result = new AddressDTO();
		if ($this->getAddresses()->count()) {
			$address = $this->getAddresses()->first();
			$street = $address->getAddress1();
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
	 * @see \Application\Model\Contactable::getPrimaryTelephone()
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
	 * Returns the account name for listing
	 * @return string
	 */
	public function getShortAccountName() {
		$result = '';
		if ($this->getAccount()) {
			$result = $this->getAccount()->getName();
			if(strlen($result) > 32) {
				$result = substr($result, 0, 32) . '...';
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
			$displayName = $this->getDisplayName();
			$addrSpec = $this->getEmail1();
			
			// Test length
			$result = '"' . $displayName . '" <' . $addrSpec . '>';
			if(strlen($result) > 255) {
				$result = $addrSpec;
			}
		}
		return $result;
	}

	public function __toString() {
		return 'Contact[id=' . $this->getId()
		. ',salutation=' . $this->getSalutation()
		. ',prefix=' . $this->getPrefix()
		. ',firstName=' . $this->getFirstName()
		. ',middleName=' . $this->getMiddleName()
		. ',lastName=' . $this->getLastName()
		. ',suffix=' . $this->getSuffix()
		. ',displayName=' . $this->getDisplayName()
		. ',nickname=' . $this->getNickname()
		. ',sortName=' . $this->getSortName()
		. ',jobTitle=' . $this->getJobTitle()
		. ',gender=' . $this->getGender()
		. ',email1=' . $this->getEmail1()
		. ',email2=' . $this->getEmail2()
		. ',doNotCall=' . ($this->getDoNotCall() ? 'true' : 'false')
		. ',doNotMail=' . ($this->getDoNotMail() ? 'true' : 'false')
		. ',doNotEmail=' . ($this->doNotEmail ? 'true' : 'false')
		. ',description=' . $this->getDescription()
		. ',isPrimaryContact=' . ($this->getIsPrimaryContact() ? 'true' : 'false')
		. ',birthDate=' . $this->formatDate($this->getBirthDate(), 'm/d/Y')
		. ',assistantName=' . $this->getAssistantName()
		. ',interests=' . $this->getInterests()
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