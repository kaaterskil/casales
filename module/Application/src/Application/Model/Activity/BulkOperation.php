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
 * @version     SVN $Id: BulkOperation.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\BulkOperationStatus;
use Application\Model\BulkOperationState;
use Application\Model\BulkOperationType;
use Application\Model\CreatedRecordType;
use Application\Model\MarketingList;
use Application\Model\TargetedRecordType;
use Application\Model\TrackedActivity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents a system operation used to perform lengthy and asynchronous operations
 * on large data sets such as distributing a campaign activity or quick campaign.
 *
 * @package     Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 *
 * @ORM\Entity
 */
class BulkOperation extends TrackedActivity {
	
	/**
	 * The type code of the objects created in the bulk operation.
	 * @ORM\Column(type="string", name="created_record_type")
	 * @var CreatedRecordType
	 */
	private $createdRecordType;
	
	/**
	 * @ORM\Column(type="integer", name="failure_count")
	 * @var int
	 */
	private $failureCount = 0;

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="MarketingList", inversedBy="bulkOperations")
	 * @ORM\JoinColumn(name="marketing_list_id", referencedColumnName="id")
	 * @var MarketingList
	 */
	private $marketingList;
	
	/**
	 * @ORM\Column(type="string", name="operation_type")
	 * @var BulkOperationType
	 */
	private $operationType;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $parameters;
	
	/**
	 * @ORM\Column(type="integer", name="success_count")
	 * @var int
	 */
	private $successCount = 0;
	
	/**
	 * @ORM\Column(type="integer", name="target_members_count")
	 * @var int
	 */
	private $targetMembersCount = 0;
	
	/**
	 * The type code of the objects targeted in the bulk operation, i.e. Account, Contact, Lead.
	 * @ORM\Column(type="string", name="targeted_record_type")
	 * @var TargetedRecordType
	 */
	private $targetedRecordType;
	
	/* ---------- One-to-Many Associations ---------- */
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractInteraction", mappedBy="bulkOperation", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $bulkInteractions;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->bulkInteractions = new ArrayCollection();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * Overrides the default implementation so that $now can be used
	 *
	 * @param \DateTime $actualEnd
	 * @see \Application\Model\TrackedActivity::setActualEnd()
	 */
	public function setActualEnd($actualEnd = null, $bypassFilter = false) {
		$this->actualEnd = $actualEnd;
	}
	
	/**
	 * Overrides the default implementation so that $now can be used
	 *
	 * @param \DateTime $actualStart
	 * @see \Application\Model\TrackedActivity::setActualStart()
	 */
	public function setActualStart($actualStart = null, $bypassFilter = false) {
		$this->actualStart = $actualStart;
	}
	
	/**
	 * Gets the type code of the objects created in the bulk operation.
	 * @return \Application\Model\CreatedRecordType
	 */
	public function getCreatedRecordType() {
		return $this->createdRecordType;
	}
	
	/**
	 * Sets the type code of the objects created in the bulk operation.
	 * @param string $createdRecordType
	 */
	public function setCreatedRecordType($createdRecordType = null) {
		if($createdRecordType instanceof CreatedRecordType) {
			$this->createdRecordType = $createdRecordType;
		} elseif($createdRecordType != null) {
			$this->createdRecordType = CreatedRecordType::instance($createdRecordType);
		} else {
			$this->createdRecordType = null;
		}
	}
	
	/**
	 * @return string
	 */
	public function getDiscriminator() {
		if($this->discriminator == null) {
			$this->discriminator = 'BulkOperation';
		}
		return $this->discriminator;
	}
	
	/**
	 * @return int
	 */
	public function getFailureCount() {
		return $this->failureCount;
	}
	
	public function setFailureCount($failureCount) {
		$this->failureCount = (int) $failureCount;
	}
	
	/**
	 * @return \Application\Model\MarketingList
	 */
	public function getMarketingList() {
		return $this->marketingList;
	}
	
	public function setMarketingList(MarketingList $list = null) {
		$this->marketingList = $list;
	}
	
	/**
	 * @return \Application\Model\BulkOperationType
	 */
	public function getOperationType() {
		return $this->operationType;
	}
	
	public function setOperationType($operationType = null) {
		if($operationType instanceof BulkOperationType) {
			$this->operationType = $operationType;
		} elseif($operationType != null) {
			$this->operationType = BulkOperationType::instance($operationType);
		} else {
			$this->operationType = null;
		}
	}
	
	/**
	 * @return string
	 */
	public function getParameters() {
		return  $this->parameters;
	}
	
	public function setParameters($parameters) {
		$this->parameters = (string) $parameters;
	}
	
	/**
	 * @return \Application\Model\BulkOperationStatus
	 * @see \Application\Model\StatefulActivity::getStatus()
	 */
	public function getStatus() {
		if(($this->status != null) && (!$this->status instanceof BulkOperationStatus)) {
			$this->status = BulkOperationStatus::instance($this->status);
		}
		return $this->status;
	}
	
	/**
	 * @param string|BulkOperationStatus $status
	 * @see \Application\Model\StatefulActivity::setStatus()
	 */
	public function setStatus($status = null) {
		if($status instanceof BulkOperationStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = BulkOperationStatus::instance($status);
		} else {
			$this->status = null;
		}
	}
	
	
	/**
	 * @return int
	 */
	public function getSuccessCount() {
		return $this->successCount;
	}
	
	public function setSuccessCount($successCount) {
		$this->successCount = (int) $successCount;
	}
	
	/**
	 * Gets the type code of the objects targeted in the bulk operation.
	 * @return \Application\Model\TargetedRecordType
	 */
	public function getTargetedRecordType() {
		return $this->targetedRecordType;
	}
	
	/**
	 * Sets the type code of the objects targeted in the bulk operation.
	 * @param string $targetedRecordType
	 */
	public function setTargetedRecordType($targetedRecordType = null) {
		if($targetedRecordType instanceof TargetedRecordType) {
			$this->targetedRecordType = $targetedRecordType;
		} elseif($targetedRecordType != null) {
			$this->targetedRecordType = TargetedRecordType::instance($targetedRecordType);
		} else {
			$this->targetedRecordType = null;
		}
	}
	
	/**
	 * @return number
	 */
	public function getTargetMembersCount() {
		return $this->targetMembersCount;
	}
	
	public function setTargetMembersCount($targetMembersCount) {
		$this->targetMembersCount = $targetMembersCount;
	}
	
	/* ---------- One-to-Many Association Getter/Setters ---------- */
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getBulkInteractions() {
		return $this->bulkInteractions;
	}
	
	public function setBulkInteractions(ArrayCollection $bulkInteractions) {
		$this->bulkInteractions = $bulkInteractions;
	}
	
	public function addBulkInteraction(AbstractInteraction $interaction) {
		$interaction->setBulkOperation($this);
		$this->bulkInteractions->add($interaction);
	}
	
	public function removeBulkInteraction(AbstractInteraction $interaction) {
		$interaction->setBulkOperation( null );
		$this->bulkInteractions->removeElement($interaction);
	}
	
	/* ---------- Methods ---------- */
}
?>