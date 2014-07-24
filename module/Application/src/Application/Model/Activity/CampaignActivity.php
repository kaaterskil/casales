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
 * @version     SVN $Id: CampaignActivity.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;
use Application\Model\CampaignActivityStatus;
use Application\Model\CampaignActivityType;
use Application\Model\ChannelType;
use Application\Model\MarketingList;
use Application\Model\Regarding;
use Application\Model\SalesLiterature;
use Application\Model\TrackedActivity;
use Application\Service\OrderClosedActivitiesRequest;
use Application\Service\OrderOpenActivitiesRequest;
use Application\Service\OrderResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A campaign may include different marketing activities. Some activities, such as
 * tasks, are assigned to team members to perform specific tasks, for example,
 * preparing a marketing list or printing sales literature. Other activities, such
 * as emails, faxes, or letters are distributed through multiple campaign
 * activities represented by the campaign activity entity (CampaignActivity). Each
 * campaign activity is used to distribute one type of activity that specifies a
 * recipient: phone call, appointment, letter, letter vial mail merge, fax, fax
 * via mail merge, email, email via mail merge. The activities can be distributed
 * over a period of time and at different times.
 *
 * @package     Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 *
 * @ORM\Entity
 */
class CampaignActivity extends TrackedActivity implements Regarding {

	/**
	 * The communication method for the campaign activity
	 * @ORM\Column(type="string", name="channel_type")
	 * @var ChannelType
	 */
	private $channelType;

	/**
	 * A campaign activity category
	 * @ORM\Column(type="string")
	 * @var CampaignActivityType
	 */
	private $type;
	
	/* ---------- One-to-Many Associations ---------- */

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractNote", mappedBy="campaignActivity", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $annotations;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractAppointment", mappedBy="campaignActivity", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $appointments;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="BulkOperation", mappedBy="campaignActivity", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $bulkOperations;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractInteraction", mappedBy="campaignActivity", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $interactions;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractTask", mappedBy="campaignActivity", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $tasks;
	
	/* ---------- Many-to-Many Associations ---------- */
	
	/**
	 * OWNING SIDE
	 * @ORM\ManyToMany(targetEntity="MarketingList")
	 * @ORM\JoinTable(name="crm_activity_list_association",
	 * 		joinColumns={@ORM\JoinColumn(name="campaign_activity_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="list_id", referencedColumnName="id")})
	 * @var ArrayCollection
	 */
	private $lists;
	
	/**
	 * OWNING SIDE
	 * @ORM\ManyToMany(targetEntity="SalesLiterature")
	 * @ORM\JoinTable(name="crm_activity_literature_association",
	 * 		joinColumns={@ORM\JoinColumn(name="campaign_activity_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="sales_literature_id", referencedColumnName="id")})
	 * @var ArrayCollection
	 */
	private $salesLiterature;
	
	/* ---------- Constructor ---------- */
	public function __construct() {
		parent::__construct();
		$this->annotations = new ArrayCollection();
		$this->appointments = new ArrayCollection();
		$this->bulkOperations = new ArrayCollection();
		$this->interactions = new ArrayCollection();
		$this->tasks = new ArrayCollection();
		$this->lists = new ArrayCollection();
		$this->salesLiterature = new ArrayCollection();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\ChannelType
	 */
	public function getChannelType() {
		return $this->channelType;
	}

	public function setChannelType($channelType = null) {
		if ($channelType instanceof ChannelType) {
			$this->channelType = $channelType;
		} elseif ($channelType != null) {
			$this->channelType = ChannelType::instance( $channelType );
		} else {
			$this->channelType = null;
		}
	}
	
	/**
	 * @return string
	 */
	public function getDiscriminator() {
		if($this->discriminator == null) {
			$this->discriminator = 'CampaignActivity';
		}
		return $this->discriminator;
	}
	
	/**
	 * @return CampaignActivityStatus
	 * @see \Application\Model\StatefulActivity::getStatus()
	 */
	public function getStatus() {
		if(($this->status != null) && (!$this->status instanceof CampaignActivityStatus)) {
			$this->status = CampaignActivityStatus::instance($this->status);
		}
		return $this->status;
	}
	
	public function setStatus($status = null) {
		if($status instanceof CampaignActivityStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = CampaignActivityStatus::instance($status);
		} else {
			$this->status = null;
		}
	}

	/**
	 * @return \Application\Model\CampaignActivityType
	 */
	public function getType() {
		return $this->type;
	}

	public function setType($type = null) {
		if ($type instanceof CampaignActivityType) {
			$this->type = $type;
		} elseif ($type != null) {
			$this->type = CampaignActivityType::instance( $type );
		} else {
			$this->type = null;
		}
	}
	
	/* ---------- One-to-Many Association Getter/Setters ---------- */

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAnnotations() {
		return $this->annotations;
	}

	public function setAnnotations(ArrayCollection $annotations) {
		$this->annotations = $annotations;
	}

	/**
	 * @param AbstractNote $note
	 */
	public function addAnnotation(AbstractNote $annotation) {
		$annotation->setCampaignActivity( $this );
		$this->annotations->add( $annotation );
	}

	/**
	 * @param AbstractNote $note
	 */
	public function removeAnnotation(AbstractNote $annotation) {
		$annotation->setCampaignActivity( null );
		$this->annotations->removeElement( $annotation );
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAppointments() {
		return $this->appointments;
	}

	public function setAppointments(ArrayCollection $appointments) {
		$this->appointments = $appointments;
	}
	
	/**
	 * @param AbstractAppointment $appointment
	 */
	public function addAppointment(AbstractAppointment $appointment) {
		$appointment->setCampaignActivity( $this );
		$this->appointments->add( $appointment );
	}

	/**
	 * @param AbstractAppointment $appointment
	 */
	public function removeAppointment(AbstractAppointment $appointment) {
		$appointment->setCampaignActivity( null );
		$this->appointments->removeElement( $appointment );
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getBulkOperations() {
		return $this->bulkOperations;
	}
	
	/**
	 * @param ArrayCollection $bulkOperations
	 */
	public function setBulkOperations(ArrayCollection $bulkOperations) {
		$this->bulkOperations = $bulkOperations;
	}
	
	/**
	 * @param BulkOperation $bulkOperation
	 */
	public function addBulkOperation(BulkOperation $bulkOperation) {
		$bulkOperation->setCampaignActivity($this);
		$this->bulkOperations->add($bulkOperation);
	}
	
	/**
	 * @param BulkOperation $bulkOperation
	 */
	public function removeBulkOperation(BulkOperation $bulkOperation) {
		$bulkOperation->setCampaignActivity(null);
		$this->bulkOperations->removeElement($bulkOperation);
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getInteractions() {
		return $this->interactions;
	}

	public function setInteractions(ArrayCollection $interactions) {
		$this->interactions = $interactions;
	}

	/**
	 * @param AbstractInteraction $interaction
	 */
	public function addInteraction(AbstractInteraction $interaction) {
		$interaction->setCampaignActivity( $this );
		$this->interactions->add( $interaction );
	}

	/**
	 * @param AbstractInteraction $interaction
	 */
	public function removeInteraction(AbstractInteraction $interaction) {
		$interaction->setCampaignActivity( null );
		$this->interactions->removeElement( $interaction );
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTasks() {
		return $this->tasks;
	}

	public function setTasks(ArrayCollection $tasks) {
		$this->tasks = $tasks;
	}

	/**
	 * @param AbstractTask $task
	 */
	public function addTask(AbstractTask $task) {
		$task->setCampaignActivity( $this );
		$this->tasks->add( $task );
	}

	/**
	 * @param AbstractTask $task
	 */
	public function removeTask(AbstractTask $task) {
		$task->setCampaignActivity( null );
		$this->tasks->removeElement( $task );
	}
	
	/* ---------- Many-to-Many Association Getter/Setters ---------- */
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getLists() {
		return $this->lists;
	}
	
	/**
	 * @return array
	 */
	public function getListsAsArray() {
		return $this->lists->toArray();
	}
	
	public function setLists(ArrayCollection $lists) {
		$this->lists = $lists;
	}
	
	/**
	 * @param MarketingList $list
	 */
	public function addList(MarketingList $list) {
		$list->addCampaignActivity($this);
		$this->lists->add($list);
	}
	
	/**
	 * @param MarketingList $list
	 */
	public function removeList(MarketingList $list) {
		$list->removeCampaignActivity($this);
		$this->lists->removeElement($list);
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getSalesLiterature() {
		return $this->salesLiterature;
	}
	
	public function setSalesLiterature(ArrayCollection $salesLiterature) {
		$this->salesLiterature = $salesLiterature;
	}
	
	/**
	 * @param SalesLiterature $salesLiterature
	 */
	public function addSalesLiterature(SalesLiterature $salesLiterature) {
		$salesLiterature->addCampaignActivity($this);
		$this->salesLiterature->add($salesLiterature);
	}
	
	/**
	 * @param SalesLiterature $salesLiterature
	 */
	public function removeSalesLiterature(SalesLiterature $salesLiterature) {
		$salesLiterature->removeCampaignActivity($this);
		$this->salesLiterature->removeElement($salesLiterature);
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * Returns an array of closed activities
	 *
	 * @return array
	 */
	public function getClosedActivities() {
		$result = array();
		foreach ($this->getAppointments()->getValues() as $activity) {
			if(!$this->isOpenActivity($activity)) {
				$result[] = $activity;
			}
		}
		foreach ($this->getInteractions()->getValues() as $activity) {
			if(!$this->isOpenActivity($activity)) {
				$result[] = $activity;
			}
		}
		foreach ($this->getAnnotations()->getValues() as $activity) {
			$result[] = $activity;
		}
		foreach ($this->getTasks()->getValues() as $activity) {
			if(!$this->isOpenActivity($activity)) {
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
	 * @param boolean $includeLink
	 * @return string
	 * @see \Application\Model\Regarding::getDisplayName()
	 */
	public function getDisplayName($includeLink = false) {
		$result = $this->getSubject();
		if(strlen($result > 25)) {
			$result = substr($result, 0, 25) . '...';
		}
		if($includeLink) {
			$campaignId = $this->getCampaign()->getId();
			$result = '<a href="/activity/edit/' . $this->getId() . '/campaignActivity/' . $campaignId . '/campaign">' . $result . '</a>';
		}
		return $result;
	}
	
	/**
	 * Returns an array of open activities
	 *
	 * @return array
	 */
	public function getOpenActivities() {
		$result = array();
		foreach ($this->getAppointments()->getValues() as $activity) {
			if($this->isOpenActivity($activity)) {
				$result[] = $activity;
			}
		}
		foreach ($this->getInteractions()->getValues() as $activity) {
			if($this->isOpenActivity($activity)) {
				$result[] = $activity;
			}
		}
		foreach ($this->getTasks()->getValues() as $activity) {
			if($this->isOpenActivity($activity)) {
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
	 * Removes any matching marketing list from the given array
	 *
	 * @param array $candidates
	 * @return array
	 */
	public function removeMatchedCandidates(array $candidates) {
		$result = array();
		foreach ($candidates as $list) {
			if(!$this->lists->contains($list)) {
				$result[] = $list;
			}
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::__toString()
	 */
	public function __toString() {
		$result = parent::__toString();
		return $result
		. ',actualStart=' . $this->getFormattedActualStart( 'Y-m-d h:i:s' )
		. ',actualEnd=' . $this->getFormattedActualEnd( 'Y-m-d h:i:s' )
		. ',channelType=' . $this->getChannelType()
		. ',priority=' . $this->getPriority()
		. ',scheduledStart=' . $this->getFormattedScheduledStart( 'Y-m-d h:i:s' )
		. ',scheduledEnd=' . $this->getFormattedScheduledEnd( 'Y-m-d h:i:s' )
		. ',state=' . $this->getState()
		. ',status=' . $this->getStatus()
		. ',type=' . $this->getType()
		. ']';
	}
	
	/**
	 * Tests if the given activity is OPEN
	 *
	 * @param AbstractActivity $activity
	 * @return boolean
	 */
	private function isOpenActivity(AbstractActivity $activity) {
		if($activity instanceof StatefulActivity) {
			if(is_object($activity->getState())) {
				return ($activity->getState()->getName() == ActivityState::OPEN ? true : false);
			}
			return ($activity->getState() == ActivityState::OPEN ? true : false);
		}
		return false;
	}
}
?>