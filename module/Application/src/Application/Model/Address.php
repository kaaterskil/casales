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
 * @version     SVN $Id: Address.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\BusinessUnit;
use Application\Model\Contact;
use Application\Model\AddressType;
use Application\Model\Lead;
use Application\Model\Region;
use Application\Model\User;

use Application\Stdlib\Entity;
use Application\Stdlib\Enum;
use Application\Stdlib\Object;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an address for an organization, contact or lead
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Address.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_address")
 */
class Address implements Entity {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $type;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManytoOne(targetEntity="Account", inversedBy="addresses")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
	 * @var Account
	 */
	private $account;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $address1;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $address2;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $address3;
	
	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BusinessUnit", inversedBy="addresses")
	 * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
	 * @var BusinessUnit
	 */
	private $businessUnit;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManytoOne(targetEntity="Contact", inversedBy="addresses")
	 * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
	 * @var Contact
	 */
	private $contact;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManytoOne(targetEntity="Lead", inversedBy="addresses")
	 * @ORM\JoinColumn(name="lead_id", referencedColumnName="id")
	 * @var Lead
	 */
	private $lead;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $city;
	
	/**
	 * Unidirectional Many-to-One
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 * @var User
	 */
	private $owner;
	
	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Region")
	 * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
	 * @var Region
	 */
	private $region;

	/**
	 * @ORM\Column(type="string", name="postal_code")
	 * @var string
	 */
	private $postalCode;

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

	/*---------- Getter/Setters ----------*/
	
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
	 * @return Account
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
	public function getAddress1() {
		return $this->address1;
	}
	
	public function setAddress1($address1) {
		$this->address1 = $address1;
	}

	/**
	 * @return string
	 */
	public function getAddress2() {
		return $this->address2;
	}
	
	public function setAddress2($address2) {
		$this->address2 = $address2;
	}

	/**
	 * @return string
	 */
	public function getAddress3() {
		return $this->address3;
	}
	
	public function setAddress3($address3) {
		$this->address3 = $address3;
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
	public function getCity() {
		return $this->city;
	}
	
	public function setCity($city) {
		$this->city = $city;
	}
	
	/**
	 * @return Contact
	 */
	public function getContact() {
		return $this->contact;
	}
	
	public function setContact(Contact $contact = null) {
		$this->contact = $contact;
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
	 * @return User
	 */
	public function getOwner() {
		return $this->owner;
	}
	
	public function setOwner(User $user = null) {
		$this->owner = $user;
	}
	
	/**
	 * @return Region
	 */
	public function getRegion() {
		return $this->region;
	}
	
	public function setRegion(Region $region = null) {
		$this->region = $region;
	}

	/**
	 * @return string
	 */
	public function getPostalCode() {
		return $this->postalCode;
	}
	
	public function setPostalCode($postal_code) {
		$this->postalCode = $postal_code;
	}
	
	/**
	 * @return AddressType
	 */
	public function getType() {
		if($this->type != null) {
			return $this->type;
		}
		return null;
	}
	
	public function setType($type) {
		if($type instanceof AddressType) {
			$this->type = $type;
		} else {
			$this->type = AddressType::instance($type);
		}
	}
	
	/**
	 * @return DateTime
	 * @see \Application\Stdlib\Entity::getCreationDate()
	 */
	public function getCreationDate() {
		if($this->creationDate instanceof \DateTime) {
			return $this->creationDate->format('Y-m-d h:i:s');
		}
		return $this->creationDate;
	}
	
	public function setCreationDate($creation_date) {
		$this->creationDate = $creation_date;
	}

	/**
	 * @return DateTime
	 * @see \Application\Stdlib\Entity::getLastUpdateDate()
	 */
	public function getLastUpdateDate() {
		if($this->lastUpdateDate instanceof \DateTime) {
			return $this->lastUpdateDate->format('Y-m-d h:i:s');
		}
		return $this->lastUpdateDate;
	}
	
	public function setLastUpdateDate($last_update_date) {
		$this->lastUpdateDate = $last_update_date;
	}

	/*---------- Methods ----------*/
	
	/** @return string */
	public function getFormattedAddress() {
		$value = '';
		$has_value = false;
		if($this->address1 != '') {
			$value = $this->address1;
			$has_value = true;
		}
		if($this->address2 != '') {
			$value = $has_value ? $value . ', ' : $value;
			$value .= $this->address2;
			$has_value = true;
		}
		if($this->address3 != '') {
			$value = $has_value ? $value . ', ' : $value;
			$value .= $this->address3;
			$has_value = true;
		}
		if($this->city != '') {
			$value = $has_value ? $value . ', ' : $value;
			$value .= $this->city;
			$has_value = true;
		}
		if($this->region != null) {
			$value = $has_value ? $value . ', ' : $value;
			$value .= $this->region->getAbbreviation();
			$has_value = true;
		}
		if($this->postalCode != '') {
			$value = $has_value ? $value . ' ' : $value;
			$value .= $this->postalCode;
		}
		return $value;
	}
	
	public function isEmpty() {
		return strlen($this->address1
			. $this->address2
			. $this->address3
			. $this->city
			. $this->postalCode) == 0;
	}
	
	/** @see \Application\Stdlib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			return false;
		}
		if($o->getId() == $this->getId()) {
			return true;
		}
		if($o->getFormattedAddress() == $this->getFormattedAddress()) {
			return true;
		}
		return false;
	}
	
	/** @see \Application\Model\Object::getClass() */
	public function getClass() {
		return get_class($this);
	}
	
	/** @see \Application\Model\Object::__toString() */
	public function __toString() {
		return 'Address[id=' . $this->getId()
		. ',address1=' . $this->getAddress1()
		. ',address2=' . $this->getAddress2()
		. ',address3=' . $this->getAddress3()
		. ',city=' . $this->getCity()
		. ',state=' . $this->getRegion()->getAbbreviation()
		. ',postalCode=' . $this->getPostalCode()
		. ',creationDate=' . $this->getCreationDate()->format('Y-m-d H:i:s')
		. ',lastUpdateDate=' . $this->getLastUpdateDate()->format('Y-m-d H:i:s')
		. ']';
	}
}
?>