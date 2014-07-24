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
 * @version     SVN $Id: BulkOperationLog.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\Account;
use Application\Model\BulkOperation;
use Application\Model\BusinessUnit;
use Application\Model\Contact;
use Application\Model\Lead;
use Application\Model\Opportunity;
use Application\Model\User;
use Application\Stdlib\Entity;
use Doctrine\ORM\Mapping as ORM;
use Application\Stdlib\Object;

/**
 * Represents the log used to track bulk operation execution, successes, and failures.
 *
 * @package     Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_bulk_operation_log")
 */
class BulkOperationLog implements Entity {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Account")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
	 * @var Account
	 */
	private $account;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BulkOperation")
	 * @ORM\JoinColumn(name="bulk_operation_id", referencedColumnName="id")
	 * @var BulkOperation
	 */
	private $bulkOperation;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BusinessUnit")
	 * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
	 * @var BusinessUnit
	 */
	private $businessUnit;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Contact")
	 * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
	 * @var Contact
	 */
	private $contact;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Lead")
	 * @ORM\JoinColumn(name="lead_id", referencedColumnName="id")
	 * @var Lead
	 */
	private $lead;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $message;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Opportunity")
	 * @ORM\JoinColumn(name="opportunity_id", referencedColumnName="id")
	 * @var Opportunity
	 */
	private $opportunity;

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
	 * @return \Application\Model\BulkOperation
	 */
	public function getBulkOperation() {
		return $this->bulkOperation;
	}

	public function setBulkOperation(BulkOperation $bulkOperation = null) {
		$this->bulkOperation = $bulkOperation;
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
	 * @return \Application\Model\Contact
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
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	public function setMessage($message) {
		$this->message = (string) $message;
	}

	/**
	 * @return \Application\Model\Opportunity
	 */
	public function getOpportunity() {
		return $this->opportunity;
	}

	public function setOpportunity(Opportunity $opportunity) {
		$this->opportunity = $opportunity;
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
		if (($o->getBusinessUnit()->equals( $this->getBusinessUnit() ) &&
			 ($o->getAccount() == $this->getAccount()) &&
			 ($o->getContact() == $this->getContact()) &&
			 ($o->getLead() == $this->getLead()) &&
			 ($o->getOpportunity() == $this->getOpportunity()) &&
			 ($o->getMessage() == $this->getMessage()))) {
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
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'BulkOperationLog[id=' .
			 $this->getId() .
			 ',account=' .
			 ($this->getAccount() !=
			 null ? $this->getAccount()->getId() : '') .
			 ',bulkOperation=' .
			 ($this->getBulkOperation() !=
			 null ? $this->getBulkOperation()->getId() : '') .
			 ',businessUnit=' .
			 ($this->getBusinessUnit() !=
			 null ? $this->getBusinessUnit()->getId() : '') .
			 ',contact=' .
			 ($this->getContact() !=
			 null ? $this->getContact()->getId() : '') .
			 ',lead=' .
			 ($this->getLead() !=
			 null ? $this->getLead()->getId() : '') .
			 ',message=' .
			 $this->getMessage() .
			 ',opportunity=' .
			 ($this->getOpportunity() !=
			 null ? $this->getOpportunity()->getId() : '') .
			 ',owner=' .
			 ($this->getOwner() !=
			 null ? $this->getOwner()->getId() : '') .
			 ',creationDate=' .
			 $this->getCreationDate()->format( 'Y-m-d H:i:s' ) .
			 ',lastUpdateDate=' .
			 $this->getLastUpdateDate()->format( 'Y-m-d H:i:s' ) .
			 ']';
	}
}
?>