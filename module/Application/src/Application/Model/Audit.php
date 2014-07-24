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
 * @version     SVN $Id: Audit.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractActivity;
use Application\Model\Account;
use Application\Model\AuditAction;
use Application\Model\AuditOperation;
use Application\Model\Campaign;
use Application\Model\Contact;
use Application\Model\Lead;
use Application\Model\Opportunity;
use Application\Model\User;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The audit entity is used by the Casales platform to record the history of data
 * changes to an entity or attribute.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Audit.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_audit")
 */
class Audit {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Account", inversedBy="auditItems")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
	 * @var Account
	 */
	private $account;

	/**
	 * @ORM\Column(type="string")
	 * @var AuditAction
	 */
	private $action;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="AbstractActivity", inversedBy="auditItems")
	 * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
	 * @var AbstractActivity
	 */
	private $activity;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BusinessUnit", inversedBy="auditItems")
	 * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
	 * @var Account
	 */
	private $businessUnit;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Campaign", inversedBy="auditItems")
	 * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
	 * @var Campaign
	 */
	private $campaign;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Contact", inversedBy="auditItems")
	 * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
	 * @var Contact
	 */
	private $contact;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Lead", inversedBy="auditItems")
	 * @ORM\JoinColumn(name="lead_id", referencedColumnName="id")
	 * @var Lead
	 */
	private $lead;

	/**
	 * @ORM\Column(type="string", name="new_data")
	 * @var string
	 */
	private $newData;

	/**
	 * @ORM\Column(type="string", name="old_data")
	 * @var string
	 */
	private $oldData;

	/**
	 * @ORM\Column(type="string")
	 * @var AuditOperation
	 */
	private $operation;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Opportunity", inversedBy="auditItems")
	 * @ORM\JoinColumn(name="opportunity_id", referencedColumnName="id")
	 * @var Opportunity
	 */
	private $opportunity;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $property;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @var User
	 */
	private $user;

	/**
	 * @ORM\Column(type="datetime", name="creation_date")
	 * @var \DateTime
	 */
	private $creationDate;
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return int
	 * @see \Application\Stdlib\Entity::getId()
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = (int) $id;
	}

	/**
	 * @return \Application\Model\Account
	 */
	public function getAccount() {
		return $this->account;
	}

	/**
	 * @param Account $account
	 */
	public function setAccount(Account $account = null) {
		$this->account = $account;
	}

	/**
	 * @return \Application\Model\AuditAction
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @param string|AuditAction $action
	 */
	public function setAction($action = null) {
		if ($action instanceof AuditAction) {
			$this->action = $action;
		} elseif ($action != null) {
			$this->action = AuditAction::instance( $action );
		} else {
			$this->action = null;
		}
	}
	
	/**
	 * @return \Application\Model\AbstractActivity
	 */
	public function getActivity() {
		return $this->activity;
	}
	
	/**
	 * @param AbstractActivity $activity
	 */
	public function setActivity(AbstractActivity $activity = null) {
		$this->activity = $activity;
	}
	
	/**
	 * @return \Application\Model\Account
	 */
	public function getBusinessUnit() {
		return $this->businessUnit;
	}
	
	/**
	 * @param BusinessUnit $businessUnit
	 */
	public function setBusinessUnit(BusinessUnit $businessUnit = null) {
		$this->businessUnit = $businessUnit;
	}

	/**
	 * @return \Application\Model\Campaign
	 */
	public function getCampaign() {
		return $this->campaign;
	}

	/**
	 * @param Campaign $campaign
	 */
	public function setCampaign(Campaign $campaign = null) {
		$this->campaign = $campaign;
	}

	/**
	 * @return \Application\Model\Contact
	 */
	public function getContact() {
		return $this->contact;
	}

	/**
	 * @param Contact $contact
	 */
	public function setContact(Contact $contact = null) {
		$this->contact = $contact;
	}

	/**
	 * @return \Application\Model\Lead
	 */
	public function getLead() {
		return $this->lead;
	}

	/**
	 * @param Lead $lead
	 */
	public function setLead(Lead $lead = null) {
		$this->lead = $lead;
	}

	/**
	 * @return string
	 */
	public function getNewData() {
		return $this->newData;
	}

	/**
	 * @param mixed $newData
	 */
	public function setNewData($newData) {
		$this->newData = $newData;
	}

	/**
	 * @return string
	 */
	public function getOldData() {
		return $this->oldData;
	}

	/**
	 * @param mixed $oldData
	 */
	public function setOldData($oldData) {
		$this->oldData = $oldData;
	}

	/**
	 * @return \Application\Model\AuditOperation
	 */
	public function getOperation() {
		return $this->operation;
	}

	/**
	 * @param string|AuditOperation $operation
	 */
	public function setOperation($operation = null) {
		if ($operation instanceof AuditOperation) {
			$this->operation = $operation;
		} elseif ($operation != null) {
			$this->operation = AuditOperation::instance( $operation );
		} else {
			$this->operation = null;
		}
	}

	/**
	 * @return \Application\Model\Opportunity
	 */
	public function getOpportunity() {
		return $this->opportunity;
	}

	/**
	 * @param Opportunity $opportunity
	 */
	public function setOpportunity(Opportunity $opportunity = null) {
		$this->opportunity = $opportunity;
	}

	/**
	 * @return string
	 */
	public function getProperty() {
		return $this->property;
	}

	/**
	 * @param string $property
	 */
	public function setProperty($property) {
		$this->property = (string) $property;
	}

	/**
	 * @return \Application\Model\User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user = null) {
		$this->user = $user;
	}

	/**
	 * @return DateTime
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	/**
	 * @param string|\DateTime $creationDate
	 */
	public function setCreationDate($creationDate = null) {
		if(!is_null($creationDate) && is_string($creationDate)) {
			$this->creationDate = new \DateTime($creationDate);
		} else {
			$this->creationDate = $creationDate;
		}
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
		if (($o->getAccount() != null
				&& $o->getAccount()->equals( $this->getAccount() ))
				&& ($o->getCampaign() != null && $o->getCampaign()->equals( $this->getCampaign() ))
				&& ($o->getContact() != null && $o->getContact()->equals( $this->getContact() ))
				&& ($o->getLead() != null && $o->getLead()->equals( $this->getLead() ))
				&& ($o->getOpportunity() != null && $o->getOpportunity()->equals( $this->getOpportunity() ))
				&& ($o->getAction() == $this->getAction())
				&& ($o->getOperation() == $this->getOperation())
				&& ($o->getProperty() == $this->getProperty())
				&& ($o->getChangeData() == $this->getChangeData())
				&& ($o->getCreationDate() == $this->getCreationDate())) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the camel case property name as a formatted string
	 * @return string
	 */
	public function getPrettyProperty() {
		$result = preg_replace('/([a-z0-9])([A-Z])/', "$1 $2", $this->getProperty());
		return ucwords($result);
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
		return 'Audit[id=' . $this->getId()
		. ',account=' . ($this->getAccount() != null ? $this->getAccount()->getId() : '')
		. ',action=' . $this->getAction()
		. ',campaign=' . ($this->getCampaign() != null ? $this->getCampaign()->getId() : '')
		. ',contact=' . ($this->getContact() != null ? $this->getContact()->getId() : '')
		. ',lead=' . ($this->getLead() != null ? $this->getLead()->getId() : '')
		. ',newData=' . $this->getNewData()
		. ',oldData=' . $this->getOldData()
		. ',operation=' . $this->getOperation()
		. ',opportunity=' . ($this->getOpportunity() != null ? $this->getOpportunity()->getId() : '')
		. ',property=' . $this->getProperty()
		. ',user=' . ($this->getUser() != null ? $this->getUser()->getId() : '')
		. ',creationDate=' . ($this->getCreationDate() != null ? $this->getCreationDate()->format( 'U' ) : '')
		. ']';
	}
}
?>