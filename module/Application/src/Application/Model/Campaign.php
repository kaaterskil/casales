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
 * @version     SVN $Id: Campaign.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;
use Application\Model\Audit;
use Application\Model\Auditable;
use Application\Model\BusinessUnit;
use Application\Model\Lead;
use Application\Model\CampaignActivity;
use Application\Model\CampaignItem;
use Application\Model\CampaignResponse;
use Application\Model\CampaignState;
use Application\Model\CampaignStatus;
use Application\Model\CampaignType;
use Application\Model\MarketingList;
use Application\Model\Opportunity;
use Application\Model\Regarding;
use Application\Model\SalesLiterature;
use Application\Model\StatefulActivity;
use Application\Model\Trackable;
use Application\Model\User;

use Application\Service\OrderOpenActivitiesRequest;
use Application\Service\OrderClosedActivitiesRequest;

use Application\Stdlib\Entity;
use Application\Stdlib\Object;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A campaign represents a container for campaign activities and responses, sales
 * literature, products, and lists to create, plan, execute, and track the results
 * of a specific marketing campaign through its life.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Campaign.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_campaign")
 */
class Campaign implements Entity, Regarding, Auditable, Trackable {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="date", name="actual_end")
	 * @var \DateTime
	 */
	private $actualEnd;
	
	/**
	 * @ORM\Column(type="date", name="actual_start")
	 * @var \DateTime
	 */
	private $actualStart;
	
	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BusinessUnit")
	 * @ORM\JoinColumn(name="business_unit_id", referencedColumnName="id")
	 * @var BusinessUnit
	 */
	private $businessUnit;
	
	/**
	 * @ORM\Column(type="string", name="code_name")
	 * @var string
	 */
	private $codeName;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description;
	
	/**
	 * @ORM\Column(type="integer", name="expected_response")
	 * @var int
	 */
	private $expectedResponse;
	
	/**
	 * @ORM\Column(type="integer", name="expected_revenue")
	 * @var int
	 */
	private $expectedRevenue;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $message;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $name;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $objective;
	
	/**
	 * Unidirectional Many-to-One
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 * @var User
	 */
	private $owner;
	
	/**
	 * @ORM\Column(type="date", name="proposed_end")
	 * @var \DateTime
	 */
	private $proposedEnd;
	
	/**
	 * @ORM\Column(type="date", name="proposed_start")
	 * @var \DateTime
	 */
	private $proposedStart;
	
	/**
	 * @ORM\Column(type="string")
	 * @var CampaignState
	 */
	private $state;
	
	/**
	 * @ORM\Column(type="string")
	 * @var CampaignStatus
	 */
	private $status;
	
	/**
	 * @ORM\Column(type="string")
	 * @var CampaignType
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
	
	/* ---------- One-to-Many Associations ---------- */
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractNote", mappedBy="campaign", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $annotations;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractAppointment", mappedBy="campaign", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $appointments;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Audit", mappedBy="campaign", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $auditItems;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="CampaignActivity", mappedBy="campaign", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $campaignActivities;

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="CampaignResponse", mappedBy="campaign", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $campaignResponses;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractInteraction", mappedBy="campaign", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $interactions;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Lead", mappedBy="campaign", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $leads;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Opportunity", mappedBy="campaign", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $opportunities;
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AbstractTask", mappedBy="campaign", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $tasks;
	
	/* ---------- Many-to-Many Associations ---------- */
	
	/**
	 * OWNING SIDE
	 * @ORM\ManyToMany(targetEntity="MarketingList")
	 * @ORM\JoinTable(name="crm_campaign_list_association",
	 * 		joinColumns={@ORM\JoinColumn(name="campaign_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="list_id", referencedColumnName="id")})
	 * @var ArrayCollection
	 */
	private $lists;
	
	/**
	 * OWNING SIDE
	 * @ORM\ManyToMany(targetEntity="SalesLiterature")
	 * @ORM\JoinTable(name="crm_campaign_literature_association",
	 * 		joinColumns={@ORM\JoinColumn(name="campaign_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="sales_literature_id", referencedColumnName="id")})
	 * @var ArrayCollection
	 */
	private $salesLiterature;
	
	/* ---------- Constructor ---------- */
	
	public function __construct() {
		$this->annotations = new ArrayCollection();
		$this->appointments = new ArrayCollection();
		$this->auditItems = new ArrayCollection();
		$this->campaignActivities = new ArrayCollection();
		$this->campaignResponses = new ArrayCollection();
		$this->interactions = new ArrayCollection();
		$this->leads = new ArrayCollection();
		$this->opportunities = new ArrayCollection();
		$this->tasks = new ArrayCollection();
		$this->lists = new ArrayCollection();
		$this->salesLiterature = new ArrayCollection();
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
	 * @return DateTime
	 * @see \Application\Model\Trackable::getActualEnd()
	 */
	public function getActualEnd() {
		return $this->actualEnd;
	}
	
	public function setActualEnd($actualEnd = null) {
		$this->actualEnd = $this->filterDate($actualEnd);
	}
	
	/**
	 * @return DateTime
	 * @see \Application\Model\Trackable::getActualStart()
	 */
	public function getActualStart() {
		return $this->actualStart;
	}
	
	public function setActualStart($actualStart) {
		$this->actualStart = $this->filterDate($actualStart);
	}
	
	/**
	 * @return \Application\Model\BusinessUnit
	 */
	public function getBusinessUnit() {
		return $this->businessUnit;
	}
	
	public function setBusinessUnit($businessUnit = null) {
		$this->businessUnit = $businessUnit;
	}
	
	/**
	 * @return string
	 */
	public function getCodeName() {
		return $this->codeName;
	}
	
	public function setCodeName($codeName) {
		$this->codeName = (string) $codeName;
	}
	
	/**
	 * @return string
	 */
	public function getDescsription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = (string) $description;
	}
	
	/**
	 * @return int
	 */
	public function getExpectedResponse() {
		return $this->expectedResponse;
	}
	
	public function setExpectedResponse($expectedResponse) {
		$expectedResponse = (int) $expectedResponse;
		if($expectedResponse < 0 || $expectedResponse > 100) {
			throw new \InvalidArgumentException("Expected Response must be a value between 0 and 100");
		}
		$this->expectedResponse = $expectedResponse;
	}
	
	/**
	 * @return int
	 */
	public function getExpectedRevenue() {
		return $this->expectedRevenue;
	}
	
	public function setExpectedRevenue($expectedRevenue) {
		$this->expectedRevenue = (int) $expectedRevenue;
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
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = (string) $name;
	}
	
	/**
	 * @return string
	 */
	public function getObjective() {
		return $this->objective;
	}
	
	public function setObjective($objective) {
		$this->objective = (string) $objective;
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
	 */
	public function getProposedEnd() {
		return $this->proposedEnd;
	}
	
	public function setProposedEnd($proposedEnd) {
		$this->proposedEnd = $this->filterDate($proposedEnd);
	}
	
	/**
	 * @return DateTime
	 */
	public function getProposedStart() {
		return $this->proposedStart;
	}
	
	public function setProposedStart($proposedStart) {
		$this->proposedStart = $this->filterDate($proposedStart);
	}
	
	/**
	 * @return \Application\Model\CampaignState
	 */
	public function getState() {
		return $this->state;
	}
	
	public function setState($state = null) {
		if($state instanceof CampaignState) {
			$this->state = $state;
		} elseif($state != null) {
			$this->state = CampaignState::instance($state);
		} else {
			$this->state = null;
		}
	}
	
	/**
	 * @return \Application\Model\CampaignStatus
	 */
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus($status = null) {
		if($status instanceof CampaignStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = CampaignStatus::instance($status);
		} else {
			$this->status = null;
		}
	}
	
	/**
	 * @return \Application\Model\CampaignType
	 */
	public function getType() {
		return $this->type;
	}
	
	public function setType($type = null) {
		if($type instanceof CampaignType) {
			$this->type = $type;
		} elseif($type != null) {
			$this->type = CampaignType::instance($type);
		} else {
			$this->type = null;
		}
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
		$annotation->setCampaign($this);
		$this->annotations->add($annotation);
	}
	
	/**
	 * @param AbstractNote $note
	 */
	public function removeAnnotation(AbstractNote $annotation) {
		$annotation->setCampaign(null);
		$this->annotations->removeElement($annotation);
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
		$appointment->setCampaign($this);
		$this->appointments->add($appointment);
	}
	
	/**
	 * @param AbstractAppointment $appointment
	 */
	public function removeAppointment(AbstractAppointment $appointment) {
		$appointment->setCampaign(null);
		$this->appointments->removeElement($appointment);
	}
	
	/**
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
		$auditItem->setCampaign( $this );
		$this->auditItems->add( $auditItem );
	}
	
	/**
	 * @param Audit $auditItem
	 * @see \Application\Model\Auditable::removeAuditItem()
	 */
	public function removeAuditItem(Audit $auditItem) {
		$auditItem->setCampaign( null );
		$this->auditItems->removeElement( $auditItem );
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCampaignActivities() {
		return $this->campaignActivities;
	}
	
	public function setCampaignActivities(ArrayCollection $campaignActivities) {
		$this->campaignActivities = $campaignActivities;
	}
	
	/**
	 * @param CampaignActivity $campaignActivity
	 */
	public function addCampaignActivity(CampaignActivity $campaignActivity) {
		$campaignActivity->setCampaign($this);
		$this->campaignActivities->add($campaignActivity);
	}
	
	/**
	 * @param CampaignActivity $campaignActivity
	 */
	public function removeCampaignActivity(CampaignActivity $campaignActivity) {
		$campaignActivity->setCampaign(null);
		$this->campaignActivities->removeElement($campaignActivity);
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCampaignResponses() {
		return $this->campaignResponses;
	}
	
	public function setCampaignResponses(ArrayCollection $campaignResponses) {
		$this->campaignResponses = $campaignResponses;
	}
	
	/**
	 * @param CampaignResponse $campaignResponse
	 */
	public function addCampaignResponse(CampaignResponse $campaignResponse) {
		$campaignResponse->setCampaign($this);
		$this->campaignResponses->add($campaignResponse);
	}
	
	/**
	 * @param CampaignResponse $campaignResponse
	 */
	public function removeCampaignResponse(CampaignResponse $campaignResponse) {
		$campaignResponse->setCampaign(null);
		$this->campaignResponses->removeElement($campaignResponse);
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
		$interaction->setCampaign($this);
		$this->interactions->add($interaction);
	}
	
	/**
	 * @param AbstractInteraction $interaction
	 */
	public function removeInteraction(AbstractInteraction $interaction) {
		$interaction->setCampaign(null);
		$this->interactions->removeElement($interaction);
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getLeads() {
		return $this->leads;
	}
	
	public function setLeads(ArrayCollection $leads) {
		$this->leads = $leads;
	}
	
	/**
	 * @param Lead $lead
	 */
	public function addLead(Lead $lead) {
		$lead->setCampaign($this);
		$this->leads->add($lead);
	}
	
	/**
	 * @param Lead $lead
	 */
	public function removeLead(Lead $lead) {
		$lead->setCampaign(null);
		$this->leads->removeElement($lead);
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getOpportunities() {
		return $this->opportunities;
	}
	
	public function setOpportunities(ArrayCollection $opportunities) {
		$this->opportunities = $opportunities;
	}
	
	/**
	 * @param Opportunity $opportunity
	 */
	public function addOpportunity(Opportunity $opportunity) {
		$opportunity->setCampaign($this);
		$this->opportunities->add($opportunity);
	}
	
	/**
	 * @param Opportunity $opportunity
	 */
	public function removeOpportunity(Opportunity $opportunity) {
		$opportunity->setCampaign(null);
		$this->opportunities->removeElement($opportunity);
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
		$task->setCampaign($this);
		$this->tasks->add($task);
	}
	
	/**
	 * @param AbstractTask $task
	 */
	public function removeTask(AbstractTask $task) {
		$task->setCampaign(null);
		$this->tasks->removeElement($task);
	}
	
	/* ---------- Many-to-Many Association Getter/Setters ---------- */
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getLists() {
		return new ArrayCollection($this->lists->toArray());
	}
	
	public function setLists(ArrayCollection $lists) {
		$this->lists = $lists;
	}
	
	/**
	 * @param MarketingList $list
	 */
	public function addList(MarketingList $list) {
		$list->addCampaign($this);
		$this->lists->add($list);
	}
	
	/**
	 * @param MarketingList $list
	 */
	public function removeList(MarketingList $list) {
		$list->removeCampaign($this);
		$this->lists->removeElement($list);
	}
	
	/**
	 * @return array
	 */
	public function getListsAsArray() {
		return $this->lists->toArray();
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getSalesLiterature() {
		return $this->salesLiterature;
	}
	
	/**
	 * @param ArrayCollection $salesLiterature
	 */
	public function setSalesLiterature(ArrayCollection $salesLiterature) {
		$this->salesLiterature = $salesLiterature;
	}
	
	/**
	 *
	 *
	 * @param SalesLiterature $literature
	 */
	public function addSalesLiterature(SalesLiterature $literature) {
		$literature->addCampaign( $this );
		$this->salesLiterature->add($literature);
	}
	
	/**
	 * @param SalesLiterature $literature
	 */
	public function removeSalesLiterature(SalesLiterature $literature) {
		$literature->removeCampaign( $this );
		$this->salesLiterature->removeElement( $literature );
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
				&& ($o->getCodeName() == $this->getCodeName())
				&& ($o->getProposedStart()->getTimestamp() == $this->getProposedStart()->getTimestamp())) {
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
			'annotations',
			'appointments',
			'auditItems',
			'campaignActivities',
			'campaignResponses',
			'interactions',
			'leads',
			'lists',
			'opportunities',
			'salesLiterature',
			'tasks',
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
	 * @see \Application\Model\Trackable::getFormattedActualEnd()
	 */
	public function getFormattedActualEnd($format = null) {
		if ($format == null) {
			$format = 'm/d/Y';
		}
		
		$result = '';
		if ($this->actualEnd) {
			$result = $this->actualEnd->format( $format );
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Model\Trackable::getFormattedActualStart()
	 */
	public function getFormattedActualStart($format = null) {
		if ($format == null) {
			$format = 'm/d/Y';
		}
		
		$result = '';
		if ($this->actualStart) {
			$result = $this->actualStart->format( $format );
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Model\Trackable::getFormattedActualEnd()
	 */
	public function getFormattedProposedEnd($format = null) {
		if ($format == null) {
			$format = 'm/d/Y';
		}
		
		$result = '';
		if ($this->proposedEnd) {
			$result = $this->proposedEnd->format( $format );
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Model\Trackable::getFormattedActualStart()
	 */
	public function getFormattedProposedStart($format = null) {
		if ($format == null) {
			$format = 'm/d/Y';
		}
		
		$result = '';
		if ($this->proposedStart) {
			$result = $this->proposedStart->format( $format );
		}
		return $result;
	}
	
	/**
	 * Returns a collection of closed planning activities
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
	 * Returns a collection of closed campaign activities
	 * @return array
	 */
	public function getClosedCampaignActivities() {
		$result = array();
		foreach ($this->getCampaignActivities() as $ca) {
			if(!$this->isOpenActivity($ca)) {
				$result[] = $ca;
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
		$result = $this->getName();
		if(strlen($result > 25)) {
			$result = substr($result, 0, 25) . '...';
		}
		if($includeLink) {
			$result = '<a href="/campaign/edit/' . $this->getId() . '">' . $result . '</a>';
		}
		return $result;
	}
	
	/**
	 * Returns an array of open planning activities
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
	 * Returns a collection of open campaign activities
	 * @return array
	 */
	public function getOpenCampaignActivities() {
		$result = array();
		foreach ($this->getCampaignActivities() as $ca) {
			if($this->isOpenActivity($ca)) {
				$result[] = $ca;
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
		/* @var $list MarketingList */
		
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
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'Campaign[id=' . $this->getId()
		. ',actualEnd=' . $this->getFormattedActualEnd()
		. ',actualStart=' . $this->getFormattedActualStart()
		. ',businessUnit=' . ($this->getBusinessUnit() ? $this->getBusinessUnit()->getId() : '')
		. ',codeName=' . $this->getCodeName()
		. ',description=' . $this->getDescsription()
		. ',expectedResponse=' . $this->getExpectedResponse()
		. ',expectedRevenue=' . $this->getExpectedRevenue()
		. ',message=' . $this->getMessage()
		. ',name=' . $this->getName()
		. ',objective=' . $this->getObjective()
		. ',owner=' . ($this->getOwner() ? $this->getOwner()->getId() : '')
		. ',proposedEnd=' . $this->getFormattedProposedEnd()
		. ',proposedStart=' . $this->getFormattedProposedStart()
		. ',state=' . $this->getState()
		. ',status=' . $this->getStatus()
		. ',type=' . $this->getType()
		. ',creationDate=' . $this->getCreationDate()
		. ',lastUpdateDate=' . $this->getLastUpdateDate()
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
	
	/**
	 * Tests if the given activity is OPEN
	 *
	 * @param AbstractActivity $activity
	 * @return boolean
	 */
	private function isOpenActivity(AbstractActivity $activity) {
		if ($activity instanceof StatefulActivity) {
			$open = ActivityState::instance( ActivityState::OPEN );
			$scheduled = AppointmentState::instance(AppointmentState::SCHEDULED);
			if (($activity->getState() == $open) || ($activity->getState() == $scheduled)) {
				return true;
			}
		}
		return false;
	}
}
?>