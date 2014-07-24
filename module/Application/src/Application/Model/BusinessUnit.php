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
 * @version     SVN $Id: BusinessUnit.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\Audit;
use Application\Model\Auditable;
use Application\Model\Organization;
use Application\Model\User;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a business, division, or department in the Casales CRM database.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: BusinessUnit.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_business_unit")
 */
class BusinessUnit implements Entity, Auditable {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", name="cost_center")
	 * @var string
	 */
	private $costCenter;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description;

	/**
	 * @ORM\Column(type="string", name="disabled_reason")
	 * @var string
	 */
	private $disabledReason;

	/**
	 * @ORM\Column(type="string", name="division_name")
	 * @var string
	 */
	private $divisionName;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $email;

	/**
	 * @ORM\Column(type="integer", name="is_disabled")
	 * @var boolean
	 */
	private $isDisabled = false;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $name;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Organization", inversedBy="businessUnits")
	 * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
	 * @var Organization
	 */
	private $organization;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BusinessUnit", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_business_unit_id", referencedColumnName="id")
	 * @var BusinessUnit
	 */
	private $parentBusinessUnit;

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
	 * @ORM\OneToMany(targetEntity="Address", mappedBy="businessUnit", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $addresses;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Audit", mappedBy="businessUnit", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $auditItems;

	/**
	 * Bidirectional Many-to-One: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="BusinessUnit", mappedBy="parentBusinessUnit", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $children;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Telephone", mappedBy="businessUnit", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $telephones;

	/**
	 * Bidirectional Many-to-One: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="User", mappedBy="businessUnit", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $users;
	
	/* ---------- Constructor ---------- */

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->addresses = new ArrayCollection();
		$this->auditItems = new ArrayCollection();
		$this->children = new ArrayCollection();
		$this->telephones = new ArrayCollection();
		$this->users = new ArrayCollection();
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
	public function getCostCenter() {
		return $this->costCenter;
	}

	public function setCostCenter($costCenter) {
		$this->costCenter = (string) $costCenter;
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
	 * @return string
	 */
	public function getDisabledReason() {
		return $this->disabledReason;
	}

	public function setDisabledReason($disabledReason) {
		$this->disabledReason = (string) $disabledReason;
	}

	/**
	 * @return string
	 */
	public function getDivisionName() {
		return $this->divisionName;
	}

	public function setDivisionName($divisionName) {
		$this->divisionName = (string) $divisionName;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = (string) $email;
	}

	/**
	 * @return boolean
	 */
	public function getIsDisabled() {
		return $this->isDisabled;
	}

	public function setIsDiabled($isDiabled) {
		if (is_bool( $isDiabled ) || is_numeric( $isDiabled )) {
			$this->isDisabled = (bool) $isDiabled;
		} else {
			$this->isDisabled = ($isDiabled == 'true' ? true : false);
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
	 * @return \Application\Model\Organization
	 */
	public function getOrganization() {
		return $this->organization;
	}
	
	public function setOrganization(Organization $organization = null) {
		$this->organization = $organization;
	}

	/**
	 * @return \Application\Model\BusinessUnit
	 */
	public function getParentBusinessUnit() {
		return $this->parentBusinessUnit;
	}

	public function setParentBusinessUnit(BusinessUnit $parentBusinessUnit = null) {
		$this->parentBusinessUnit = $parentBusinessUnit;
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
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAddresses() {
		return $this->addresses;
	}
	
	public function setAddresses(ArrayCollection $addresses) {
		$this->addresses = $addresses;
	}
	
	/**
	 * @param Address $address
	 */
	public function addAddress(Address $address) {
		$address->setBusinessUnit( $this );
		$this->addresses->add( $address );
	}
	
	/**
	 * @param Address $address
	 */
	public function removeAddress(Address $address) {
		$address->setBusinessUnit( null );
		$this->addresses->removeElement( $address );
	}
	
	public function addAddresses(ArrayCollection $addresses) {
		foreach ($addresses->getValues() as $address) {
			$this->addAddress($address);
		}
	}
	
	public function removeAddresses(ArrayCollection $addresses) {
		foreach ($addresses->getValues() as $address) {
			$this->removeAddress($address);
		}
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAuditItems() {
		return $this->auditItems;
	}
	
	/**
	 * @param ArrayCollection $auditItems
	 */
	public function setAuditItems(ArrayCollection $auditItems) {
		$this->auditItems = $auditItems;
	}
	
	/**
	 * @param Audit $auditItem
	 */
	public function addAuditItem(Audit $auditItem) {
		$auditItem->setBusinessUnit( $this );
		$this->auditItems->add( $auditItem );
	}
	
	/**
	 * @param Audit $auditItem
	 */
	public function removeAuditItem(Audit $auditItem) {
		$auditItem->setBusinessUnit( null );
		$this->auditItems->removeElement( $auditItem );
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getChildren() {
		return $this->children;
	}
	
	public function setChildren(ArrayCollection $children) {
		$this->children = $children;
	}
	
	/**
	 * Adds a child BusinessUnit to this collection
	 * @param BusinessUnit $child
	 */
	public function addChild(BusinessUnit $child) {
		$child->setParentBusinessUnit($this);
		$this->children->add($child);
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTelephones() {
		return $this->telephones;
	}
	
	/**
	 * @param ArrayCollection $telephones
	 */
	public function setTelephones(ArrayCollection $telephones) {
		$this->telephones = $telephones;
	}
	
	/**
	 * @param Telephone $telephone
	 */
	public function addTelephone(Telephone $telephone) {
		$telephone->setBusinessUnit( $this );
		$this->telephones->add( $telephone );
	}
	
	/**
	 * @param Telephone $telephone
	 */
	public function removeTelephone(Telephone $telephone) {
		$telephone->setBusinessUnit( null );
		$this->telephones->removeElement( $telephone );
	}
	
	/**
	 * @param ArrayCollection $telephones
	 */
	public function addTelephones(ArrayCollection $telephones) {
		foreach ($telephones->getValues() as $telephone) {
			$this->addTelephone( $telephone );
		}
	}
	
	/**
	 * @param ArrayCollection $telephones
	 */
	public function removeTelephones(ArrayCollection $telephones) {
		foreach ($telephones->getValues() as $telephone) {
			$this->removeTelephone( $telephone );
		}
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getUsers() {
		return $this->users;
	}
	
	public function setUsers(ArrayCollection $users) {
		$this->users = $users;
	}
	
	/**
	 * Adds a User to the BusinessUnit collection
	 * @param User $user
	 */
	public function addUser(User $user) {
		$user->setBusinessUnit($this);
		$this->users->add($user);
	}
	
	/* ---------- Methods ---------- */

	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Stdlib\Object::equals()
	 */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			return false;
		}
		if($o->getId() == $this->getId()) {
			return true;
		}
		if(($o->getName() == $this->getName())
				&& ($o->getDescription() == $this->getDescription())) {
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
			'auditItems',
			'children',
			'telephones',
			'users'
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
		return get_class($this);
	}
	
	/**
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'BusinessUnit[id=' . $this->getId()
		. ',costCenter=' . $this->getCostCenter()
		. ',description=' . $this->getDescription()
		. ',disabledReason=' . $this->getDisabledReason()
		. ',divisionName=' . $this->getDivisionName()
		. ',email=' . $this->getEmail()
		. ',isDisabled=' . ($this->getIsDisabled() ? 'true' : 'false')
		. ',name=' . $this->getName()
		. ',parentBusinessUnit=' . ($this->getParentBusinessUnit() ? $this->getParentBusinessUnit()->getId() : '')
		. ',website=' . $this->getWebsite()
		. ',creationDate=' . $this->getCreationDate()->format('Y-m-d H:i:s')
		. ',lastUpdateDate=' . $this->getLastUpdateDate()->format('Y-m-d H:i:s')
		. ']';
	}
}
?>