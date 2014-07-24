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
 * @version     SVN $Id: Telephone.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\Account;
use Application\Model\Contact;
use Application\Model\Lead;
use Application\Stdlib\Entity;
use Application\Stdlib\Enum;
use Application\Stdlib\Object;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a telephone number.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Telephone.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_telephone")
 */
class Telephone implements Entity {

	/**
	 * Formats the given telephone number into (xxx) xxx-xxxx
	 *
	 * @param string $unformatted
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	static public function formatPhoneNumber($unformatted) {
		$unformatted = self::unformatPhoneNumber( $unformatted );
		
		$result = '';
		if (!empty( $unformatted )) {
			switch (strlen( $unformatted )) {
				case 10 :
					$result = '(' .
						 substr( $unformatted, 0, 3 ) .
						 ') ' .
						 substr( $unformatted, 3, 3 ) .
						 '-' .
						 substr( $unformatted, 6 );
					break;
				case 7 :
					$result = substr( $unformatted, 0, 3 ) .
						 '-' .
						 substr( $unformatted, 3 );
					break;
				default :
					throw new \InvalidArgumentException( "Telephone number must have either 7 or 10 digits. Given: '" .
						 $unformatted .
						 "'" );
			}
		}
		return $result;
	}

	/**
	 * Reformats the given telephone number into a string of digits
	 *
	 * @param unknown $formatted
	 * @return mixed
	 */
	static public function unformatPhoneNumber($formatted) {
		return preg_replace( '#[^0-9]#', '', $formatted );
	}

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Account", inversedBy="telephones")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
	 * @var Account
	 */
	private $account;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BusinessUnit", inversedBy="telephones")
	 * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
	 * @var BusinessUnit
	 */
	private $businessUnit;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Contact", inversedBy="telephones")
	 * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
	 * @var Contact
	 */
	private $contact;

	/**
	 * @ORM\Column(type="integer", name="is_primary")
	 * @var boolean
	 */
	private $isPrimary;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Lead", inversedBy="telephones")
	 * @ORM\JoinColumn(name="lead_id", referencedColumnName="id")
	 * @var Lead
	 */
	private $lead;

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
	private $phone;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $type;

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
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 * @param string $phone
	 */
	public function __construct($phone = null) {
		if ($phone != null) {
			$this->phone = self::formatPhoneNumber( $phone );
		}
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
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
	 * @return BusinessUnit
	 */
	public function getBusinessUnit() {
		return $this->businessUnit;
	}

	public function setBusinessUnit(BusinessUnit $businessUnit = null) {
		$this->businessUnit = $businessUnit;
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
	 * @return boolean
	 */
	public function getIsPrimary() {
		return $this->isPrimary;
	}

	public function setIsPrimary($isPrimary = null) {
		if (is_bool( $isPrimary ) || is_numeric( $isPrimary )) {
			$this->isPrimary = $isPrimary;
		} else {
			$this->isPrimary = ($isPrimary == 'true' ? true : false);
		}
	}

	/**
	 * @return Lead
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
	 * @return string
	 */
	public function getPhone() {
		return $this->phone;
	}

	public function setPhone($phone = null) {
		if ($phone != null && $phone != '' && $phone != '() -') {
			$this->phone = self::formatPhoneNumber( $phone );
		} else {
			$this->phone = null;
		}
	}

	/**
	 * @return TelephoneType
	 */
	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		if ($type instanceof TelephoneType) {
			$this->type = $type;
		} else {
			$this->type = TelephoneType::instance( $type );
		}
	}

	/**
	 * @return \DateTime|string
	 */
	public function getCreationDate() {
		if ($this->creationDate instanceof \DateTime) {
			return $this->creationDate->format( 'Y-m-d h:i:s' );
		}
		return $this->creationDate;
	}

	public function setCreationDate($creation_date) {
		$this->creationDate = $creation_date;
	}

	/**
	 * @return \DateTime|string
	 */
	public function getLastUpdateDate() {
		if ($this->lastUpdateDate instanceof \DateTime) {
			return $this->lastUpdateDate->format( 'Y-m-d h:i:s' );
		}
		return $this->lastUpdateDate;
	}

	public function setLastUpdateDate($last_update_date) {
		$this->lastUpdateDate = $last_update_date;
	}
	
	/* ---------- Methods ---------- */
	
	/**
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
		if ($o->getPhone() == $this->getPhone()) {
			return true;
		}
		return false;
	}

	/**
	 * @return string
	 * @see \Application\Model\Object::getClass()
	 */
	public function getClass() {
		return get_class( $this );
	}

	/**
	 * @return string
	 * @see \Application\Model\Object::__toString()
	 */
	public function __toString() {
		return 'Telephone[id=' .
			 $this->getId() .
			 ',phone=' .
			 $this->getPhone() .
			 ',isPrimary=' .
			 $this->getIsPrimary() .
			 ',creationDate=' .
			 $this->formatDate( $this->getCreationDate() ) .
			 ',lastUpdateDate=' .
			 $this->formatDate( $this->getLastUpdateDate() ) .
			 ']';
	}

	private function formatDate($date, $format = '') {
		if (empty( $format )) {
			$format = 'Y-m-d H:i:s';
		}
		
		$result = '';
		if ($date instanceof \DateTime) {
			$result = $date->format( $format );
		} elseif (!empty( $date )) {
			$result = $date;
		}
		return $result;
	}
}
?>