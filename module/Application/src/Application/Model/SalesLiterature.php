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
 * @version     SVN $Id: SalesLiterature.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\Campaign;
use Application\Model\CampaignItem;
use Application\Model\Organization;
use Application\Model\SalesLiteratureItem;
use Application\Model\User;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents sales literature. One sales literature entity may contain multiple documents.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: SalesLiterature.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_sales_literature")
 */
class SalesLiterature implements Entity, CampaignItem {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description;

	/**
	 * @ORM\Column(type="date", name="expiration_date")
	 * @var \DateTime
	 */
	private $expirationDate;

	/**
	 * @ORM\Column(type="integer", name="has_attachments")
	 * @var boolean
	 */
	private $hasAttachments = false;

	/**
	 * @ORM\Column(type="integer", name="is_customer_viewable")
	 * @var boolean
	 */
	private $isCustomerViewable = false;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $keywords;

	/**
	 * @ORM\Column(type="string", name="literature_type_code")
	 * @var LiteratureType
	 */
	private $literatureType;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $name;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Organization")
	 * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
	 * @var Organization
	 */
	private $organization;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 * @var User
	 */
	private $owner;

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
	
	/* ---------- One-To-Many Associations ---------- */
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="SalesLiteratureItem", mappedBy="salesLiterature", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $salesLiteratureItems;
	
	/* ---------- Many-To-Many Associations ---------- */
	
	/**
	 * INVERSE SIDE
	 * @ORM\ManyToMany(targetEntity="Campaign", mappedBy="salesLiterature")
	 * @var ArrayCollection
	 */
	private $campaigns;
	
	/**
	 * INVERSE SIDE
	 * @ORM\ManyToMany(targetEntity="CampaignActivity", mappedBy="salesLiterature")
	 * @var ArrayCollection
	 */
	private $campaignActivities;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->salesLiteratureItems = new ArrayCollection();
		$this->campaigns = new ArrayCollection();
		$this->campaignActivities = new ArrayCollection();
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
	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = (string) $description;
	}

	/**
	 * @return DateTime
	 */
	public function getExpirationDate() {
		return $this->expirationDate;
	}

	public function setExpirationDate($expirationDate) {
		$this->expirationDate = $this->filterDate( $expirationDate );
	}

	/**
	 * @return boolean
	 */
	public function getHasAttachments() {
		return $this->hasAttachments;
	}

	public function setHasAttachments($hasAttachments) {
		if (is_bool( $hasAttachments ) || is_numeric( $hasAttachments )) {
			$this->hasAttachments = (bool) $hasAttachments;
		} else {
			$this->hasAttachments = ($hasAttachments == 'true' ? true : false);
		}
	}

	/**
	 * @return boolean
	 */
	public function getIsCustomerViewable() {
		return $this->isCustomerViewable;
	}

	public function setIsCustomerViewable($isCustomerViewable) {
		if (is_bool( $isCustomerViewable ) || is_numeric( $isCustomerViewable )) {
			$this->isCustomerViewable = (bool) $isCustomerViewable;
		} else {
			$this->isCustomerViewable = ($isCustomerViewable == 'true' ? true : false);
		}
	}

	/**
	 * @return string
	 */
	public function getKeywords() {
		return $this->keywords;
	}

	public function setKeywords($keywords) {
		$this->keywords = (string) $keywords;
	}

	/**
	 * @return \Application\Model\LiteratureType
	 */
	public function getLiteratureType() {
		return $this->literatureType;
	}

	public function setLiteratureType($literatureType = null) {
		if ($literatureType instanceof LiteratureType) {
			$this->literatureType = $literatureType;
		} elseif ($literatureType != null) {
			$this->literatureType = LiteratureType::instance( $literatureType );
		} else {
			$this->literatureType = null;
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
	 * @return \Application\Model\User
	 */
	public function getOwner() {
		return $this->owner;
	}

	public function setOwner(User $owner = null) {
		$this->owner = $owner;
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
	
	/* ---------- One-To-Many Association Getter/Setters ---------- */
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getSalesLiteratureItems() {
		return $this->salesLiteratureItems;
	}

	public function setSalesLiteratureItems(ArrayCollection $salesLiteratureItems) {
		$this->salesLiteratureItems = $salesLiteratureItems;
	}

	public function addSalesLiteratureitem(SalesLiteratureItem $item) {
		$item->setSalesLiterature( $this );
		$this->salesLiteratureItems->add( $item );
	}

	public function removeSalesLiteratureItem(SalesLiteratureItem $item) {
		$item->setSalesLiterature( null );
		$this->salesLiteratureItems->removeElement( $item );
	}
	
	/* ---------- Many-to-Many Association Getter/Setters ---------- */
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCampaigns() {
		return $this->campaigns;
	}
	
	/**
	 * @param ArrayCollection $campaigns
	 */
	public function setCampaigns(ArrayCollection $campaigns) {
		$this->campaigns = $campaigns;
	}
	
	/**
	 * @param Campaign $campaign
	 */
	public function addCampaign(Campaign $campaign) {
		$this->campaigns->add($campaign);
	}
	
	/**
	 * @param Campaign $campaign
	 */
	public function removeCampaign(Campaign $campaign) {
		$this->campaigns->removeElement($campaign);
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCampaignActivities() {
		return $this->campaignActivities;
	}
	
	/**
	 * @param ArrayCollection $campaignActivities
	 */
	public function setCampaignActivities(ArrayCollection $campaignActivities) {
		$this->campaignActivities = $campaignActivities;
	}
	
	/**
	 * @param CampaignActivity $campaignActivity
	 */
	public function addCampaignActivity(CampaignActivity $campaignActivity) {
		$this->campaignActivities->add($campaignActivity);
	}
	
	/**
	 * @param CampaignActivity $campaignActivity
	 */
	public function removeCampaignActivity(CampaignActivity $campaignActivity) {
		$this->campaignActivities->removeElement($campaignActivity);
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
		if ($o->getOrganization()->equals( $this->getOrganization() )
				&& $o->getName() == $this->getName()
				&& $o->getOwner()->equals( $this->getOwner() )
				&& $o->getCreationDate() == $this->getCreationDate()) {
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
	 * Returns a formatted string
	 *
	 * @return string
	 */
	public function getFormattedExpirationDate() {
		$result = '';
		if($this->getExpirationDate() != null) {
			$expirationDate = $this->getExpirationDate();
			if(is_scalar($expirationDate)) {
				$expirationDate = new \DateTime($expirationDate);
			}
			$result = $expirationDate->format('m/d/Y');
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'SalesLiterature[id=' . $this->getId()
		. ',description=' . $this->getDescription()
		. ',expirationDate=' . ($this->getExpirationDate() != null ? $this->getExpirationDate()->format( 'Y-m-d' ) : '')
		. ',hasAttachments=' . ($this->getHasAttachments() ? 'true' : 'false')
		. ',isCustomerViewable=' . ($this->getIsCustomerViewable() ? 'true' : 'false')
		. ',keywords=' . $this->getKeywords()
		. ',literatureType=' . $this->getLiteratureType()
		. ',name=' . $this->getName()
		. ',organization=' . ($this->getOrganization() != null ? $this->getOrganization()->getId() : '')
		. ',owner=' . ($this->getOwner() != null ? $this->getOwner()->getId() : '')
		. ',creationDate=' . $this->getCreationDate()->format( 'Y-m-d H:i:s' )
		. ',lastUpdateDate=' . $this->getLastUpdateDate()->format( 'Y-m-d H:i:s' )
		. ']';
	}

	/**
	 * Tests if Doctrine has created a DateTime object from a null value.
	 * If it has, its value is the current datetime, not null, and we must strip it.
	 *
	 * @param mixed $date
	 * @return NULL|\DateTime
	 */
	private function filterDate($date) {
		$now = new \DateTime();
		if ($date instanceof \DateTime) {
			if ($date->format( 'Y-m-d H:i' ) == $now->format( 'Y-m-d H:i' )) {
				return null;
			}
		}
		return $date;
	}
}
?>