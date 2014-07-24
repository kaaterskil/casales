<?php

/**
 * Casales Library
 * PHP version 5.4
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
 * @category Casales
 * @package Application\Model
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN $Id: User.php 22 2013-08-06 20:29:19Z  $
 */
namespace Application\Model;

use Application\Model\AccessMode;
use Application\Model\Audit;
use Application\Model\Auditable;
use Application\Model\BusinessUnit;
use Application\Model\LicenseType;
use Application\Model\Salutation;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a person with access to the CRM system and who owns objects in the CRM
 * database.
 *
 * @package Application\Model
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version $Id: User.php 22 2013-08-06 20:29:19Z  $ @ORM\Entity
 *          @ORM\Table(name="crm_user")
 */
class User implements Entity, Auditable {

	/**
	 * @ORM\Id @ORM\Column(type="integer", name="id") @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", name="access_mode")
	 * @var AccessMode
	 */
	private $accessMode;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE @ORM\ManyToOne(targetEntity="BusinessUnit",
	 * inversedBy="users") @ORM\JoinColumn(name="business_unit_id",
	 * referencedColumnName="id")
	 * @var BusinessUnit
	 */
	private $businessUnit;

	/**
	 * @ORM\Column(type="string", name="disabled_reason")
	 * @var string
	 */
	private $disabledReason;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $email;

	/**
	 * @ORM\Column(type="string", name="email_signature")
	 * @var string
	 */
	private $emailSignature;

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
	 * @ORM\Column(type="integer", name="is_disabled")
	 * @var boolean
	 */
	private $isDisabled;

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
	 * @ORM\Column(type="string", name="cal_type")
	 * @var LicenseType
	 */
	private $licenseType;

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
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $password;

	/**
	 * @ORM\Column(type="string")
	 * @var Salutation
	 */
	private $salutation;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $username;

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
	
	/* ---------- One-to-MAny Associations ---------- */
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE @ORM\OneToMany(targetEntity="Audit",
	 * mappedBy="user", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $auditItems;
	
	/* ---------- Constructor ---------- */
	
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
	 * @return \Application\Model\AccessMode
	 */
	public function getAccessMode() {
		return $this->accessMode;
	}

	public function setAccessMode($accessMode = null) {
		if ($accessMode instanceof AccessMode) {
			$this->accessMode = $accessMode;
		} elseif ($accessMode != null) {
			$this->accessMode = AccessMode::instance( $accessMode );
		} else {
			$this->accessMode = null;
		}
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
	public function getDisabledReason() {
		return $this->disabledReason;
	}

	public function setDisabledReason($disabledReason) {
		$this->disabledReason = (string) $disabledReason;
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
	 * @return string
	 */
	public function getEmailSignature() {
		return $this->emailSignature;
	}

	public function setEmailSignature($emailSignature) {
		$this->emailSignature = (string) $emailSignature;
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
		if ($this->fullName == null) {
			$this->fullName = $this->computeFullName();
		}
		return $this->fullName;
	}

	public function setFullName($fullName) {
		$this->fullName = (string) $fullName;
	}

	/**
	 * @return boolean
	 */
	public function getIsDisabled() {
		return $this->isDisabled;
	}

	public function setIsDisabled($isDisabled) {
		if (is_bool( $isDisabled ) || is_numeric( $isDisabled )) {
			$this->isDisabled = (bool) $isDisabled;
		} else {
			$this->isDisabled = ($isDisabled == 'true' ? true : false);
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
	 * @return \Application\Model\LicenseType
	 */
	public function getLicenseType() {
		return $this->licenseType;
	}

	public function setLicenseType($licenseType = null) {
		if ($licenseType instanceof LicenseType) {
			$this->licenseType = $licenseType;
		} elseif ($licenseType != null) {
			$this->licenseType = LicenseType::instance( $licenseType );
		} else {
			$this->licenseType = null;
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
	 * @return string
	 */
	public function getNickname() {
		return $this->nickname;
	}

	public function setNickname($nickname) {
		$this->nickname = (string) $nickname;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = (string) $password;
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
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = (string) $username;
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
	 * Returns a collection of audit items
	 *
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
		$auditItem->setUser( $this );
		$this->auditItems->add( $auditItem );
	}

	/**
	 * @param Audit $auditItem
	 * @see \Application\Model\Auditable::removeAuditItem()
	 */
	public function removeAuditItem(Audit $auditItem) {
		$auditItem->setUser( null );
		$this->auditItems->removeElement( $auditItem );
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return string
	 */
	public function computeFullName() {
		$value = '';
		$has_string = false;
		if ($this->firstName != '') {
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
		return $value;
	}

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
		if ($o->getFullName() == $this->getFullName()) {
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
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'User[id=' .
			 $this->getId() .
			 ',accessMode=' .
			 $this->getAccessMode() .
			 ',BusinessUnit=' .
			 ($this->getBusinessUnit() ? $this->getBusinessUnit()->getId() : '') .
			 ',disabledReason=' .
			 $this->getDisabledReason() .
			 ',email=' .
			 $this->getEmail() .
			 ',firstName=' .
			 $this->getFirstName() .
			 ',fullName=' .
			 $this->getFullName() .
			 ',isDisabled=' .
			 ($this->getIsDisabled() ? 'true' : 'false') .
			 ',jobTitle=' .
			 $this->getJobTitle() .
			 ',lastName=' .
			 $this->getLastName() .
			 ',middleName=' .
			 $this->getMiddleName() .
			 ',nickname=' .
			 $this->getNickname() .
			 ',salutation=' .
			 $this->getSalutation() .
			 ',creationDate=' .
			 $this->getCreationDate()->format( 'Y-m-d H:i:s' ) .
			 ',lastUpdateDate=' .
			 $this->getLastUpdateDate()->format( 'Y-m-d H:i:s' ) .
			 ']';
	}
}
?>