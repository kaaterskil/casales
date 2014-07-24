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
 * @version     SVN $Id: Organization.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\BusinessUnit;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the top level of the Casales CRM business hierarchy. The
 * organization can be a specific business, holding company, or corporation.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Organization.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_organization")
 */
class Organization implements Entity {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string", name="date_format")
	 * @var string
	 */
	private $dateFormat;
	
	/**
	 * @ORM\Column(type="string", name="disabled_reason")
	 * @var string
	 */
	private $disabledReason;
	
	/**
	 * @ORM\Column(type="integer", name="is_disabled")
	 * @var boolean
	 */
	private $isDisabled = false;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $locale;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $name;
	
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

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="BusinessUnit", mappedBy="organization", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $businessUnits;
	
	/* ---------- Constructors ---------- */
	
	public function __construct() {
		$this->businessUnits = new ArrayCollection();
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
	public function getDateFormat() {
		return $this->dateFormat;
	}
	
	public function setDateFormat($dateFormat) {
		$this->dateFormat = (string) $dateFormat;
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
	 * @return boolean
	 */
	public function getIsDisabled() {
		return $this->isDisabled;
	}
	
	public function setIsDisabled($isDisabled) {
		if(is_bool($isDisabled) || is_numeric($isDisabled)) {
			$this->isDisabled = (bool) $isDisabled;
		} else {
			$this->isDisabled = ($isDisabled == 'true' ? true : false);
		}
	}
	
	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}
	
	public function setLocale($locale) {
		$this->locale = (string) $locale;
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
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getBusinessUnits() {
		return $this->businessUnits;
	}
	
	public function setBusinessUnits(ArrayCollection $businessUnits) {
		$this->businessUnits = $businessUnits;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @param BusinessUnit $businessUnit
	 */
	public function addBusinessUnit(BusinessUnit $businessUnit) {
		$businessUnit->setOrganization($this);
		$this->businessUnits->add($businessUnit);
	}
	
	/**
	 * @param BusinessUnit $businessUnit
	 */
	public function removeBusinessUnit(BusinessUnit $businessUnit) {
		$businessUnit->setOrganization(null);
		$this->businessUnits->removeElement($businessUnit);
	}
	
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
		if($o->getName() == $this->getName()) {
			return true;
		}
		return false;
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
		return 'Organization[id=' . $this->getId()
		. ',dateFormat=' . $this->getDateFormat()
		. ',disabledReason=' . $this->getDisabledReason()
		. ',isDisabled=' . ($this->getIsDisabled() ? 'true' : 'false')
		. ',locale=' . $this->getLocale()
		. ',name=' . $this->getName()
		. ',creationDate=' . $this->getCreationDate()->format('Y-m-d H:i:s')
		. ',lastUpdateDate=' . $this->getLastUpdateDate()->format('Y-m-d H:i:s')
		. ']';
	}
}
?>