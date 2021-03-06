<?php

namespace DoctrineORMModule\Proxy\__CG__\Application\Model;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Campaign extends \Application\Model\Campaign implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function setId($id)
    {
        $this->__load();
        return parent::setId($id);
    }

    public function getActualEnd()
    {
        $this->__load();
        return parent::getActualEnd();
    }

    public function setActualEnd($actualEnd = NULL)
    {
        $this->__load();
        return parent::setActualEnd($actualEnd);
    }

    public function getActualStart()
    {
        $this->__load();
        return parent::getActualStart();
    }

    public function setActualStart($actualStart)
    {
        $this->__load();
        return parent::setActualStart($actualStart);
    }

    public function getBusinessUnit()
    {
        $this->__load();
        return parent::getBusinessUnit();
    }

    public function setBusinessUnit($businessUnit = NULL)
    {
        $this->__load();
        return parent::setBusinessUnit($businessUnit);
    }

    public function getCodeName()
    {
        $this->__load();
        return parent::getCodeName();
    }

    public function setCodeName($codeName)
    {
        $this->__load();
        return parent::setCodeName($codeName);
    }

    public function getDescsription()
    {
        $this->__load();
        return parent::getDescsription();
    }

    public function setDescription($description)
    {
        $this->__load();
        return parent::setDescription($description);
    }

    public function getExpectedResponse()
    {
        $this->__load();
        return parent::getExpectedResponse();
    }

    public function setExpectedResponse($expectedResponse)
    {
        $this->__load();
        return parent::setExpectedResponse($expectedResponse);
    }

    public function getExpectedRevenue()
    {
        $this->__load();
        return parent::getExpectedRevenue();
    }

    public function setExpectedRevenue($expectedRevenue)
    {
        $this->__load();
        return parent::setExpectedRevenue($expectedRevenue);
    }

    public function getMessage()
    {
        $this->__load();
        return parent::getMessage();
    }

    public function setMessage($message)
    {
        $this->__load();
        return parent::setMessage($message);
    }

    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    public function setName($name)
    {
        $this->__load();
        return parent::setName($name);
    }

    public function getObjective()
    {
        $this->__load();
        return parent::getObjective();
    }

    public function setObjective($objective)
    {
        $this->__load();
        return parent::setObjective($objective);
    }

    public function getOwner()
    {
        $this->__load();
        return parent::getOwner();
    }

    public function setOwner(\Application\Model\User $owner = NULL)
    {
        $this->__load();
        return parent::setOwner($owner);
    }

    public function getProposedEnd()
    {
        $this->__load();
        return parent::getProposedEnd();
    }

    public function setProposedEnd($proposedEnd)
    {
        $this->__load();
        return parent::setProposedEnd($proposedEnd);
    }

    public function getProposedStart()
    {
        $this->__load();
        return parent::getProposedStart();
    }

    public function setProposedStart($proposedStart)
    {
        $this->__load();
        return parent::setProposedStart($proposedStart);
    }

    public function getState()
    {
        $this->__load();
        return parent::getState();
    }

    public function setState($state = NULL)
    {
        $this->__load();
        return parent::setState($state);
    }

    public function getStatus()
    {
        $this->__load();
        return parent::getStatus();
    }

    public function setStatus($status = NULL)
    {
        $this->__load();
        return parent::setStatus($status);
    }

    public function getType()
    {
        $this->__load();
        return parent::getType();
    }

    public function setType($type = NULL)
    {
        $this->__load();
        return parent::setType($type);
    }

    public function getCreationDate()
    {
        $this->__load();
        return parent::getCreationDate();
    }

    public function setCreationDate($creationDate)
    {
        $this->__load();
        return parent::setCreationDate($creationDate);
    }

    public function getLastUpdateDate()
    {
        $this->__load();
        return parent::getLastUpdateDate();
    }

    public function setLastUpdateDate($lastUpdateDate)
    {
        $this->__load();
        return parent::setLastUpdateDate($lastUpdateDate);
    }

    public function getAnnotations()
    {
        $this->__load();
        return parent::getAnnotations();
    }

    public function setAnnotations(\Doctrine\Common\Collections\ArrayCollection $annotations)
    {
        $this->__load();
        return parent::setAnnotations($annotations);
    }

    public function addAnnotation(\Application\Model\AbstractNote $annotation)
    {
        $this->__load();
        return parent::addAnnotation($annotation);
    }

    public function removeAnnotation(\Application\Model\AbstractNote $annotation)
    {
        $this->__load();
        return parent::removeAnnotation($annotation);
    }

    public function getAppointments()
    {
        $this->__load();
        return parent::getAppointments();
    }

    public function setAppointments(\Doctrine\Common\Collections\ArrayCollection $appointments)
    {
        $this->__load();
        return parent::setAppointments($appointments);
    }

    public function addAppointment(\Application\Model\AbstractAppointment $appointment)
    {
        $this->__load();
        return parent::addAppointment($appointment);
    }

    public function removeAppointment(\Application\Model\AbstractAppointment $appointment)
    {
        $this->__load();
        return parent::removeAppointment($appointment);
    }

    public function getAuditItems()
    {
        $this->__load();
        return parent::getAuditItems();
    }

    public function setAuditItems(\Doctrine\Common\Collections\ArrayCollection $auditItems)
    {
        $this->__load();
        return parent::setAuditItems($auditItems);
    }

    public function addAuditItem(\Application\Model\Audit $auditItem)
    {
        $this->__load();
        return parent::addAuditItem($auditItem);
    }

    public function removeAuditItem(\Application\Model\Audit $auditItem)
    {
        $this->__load();
        return parent::removeAuditItem($auditItem);
    }

    public function getCampaignActivities()
    {
        $this->__load();
        return parent::getCampaignActivities();
    }

    public function setCampaignActivities(\Doctrine\Common\Collections\ArrayCollection $campaignActivities)
    {
        $this->__load();
        return parent::setCampaignActivities($campaignActivities);
    }

    public function addCampaignActivity(\Application\Model\CampaignActivity $campaignActivity)
    {
        $this->__load();
        return parent::addCampaignActivity($campaignActivity);
    }

    public function removeCampaignActivity(\Application\Model\CampaignActivity $campaignActivity)
    {
        $this->__load();
        return parent::removeCampaignActivity($campaignActivity);
    }

    public function getCampaignResponses()
    {
        $this->__load();
        return parent::getCampaignResponses();
    }

    public function setCampaignResponses(\Doctrine\Common\Collections\ArrayCollection $campaignResponses)
    {
        $this->__load();
        return parent::setCampaignResponses($campaignResponses);
    }

    public function addCampaignResponse(\Application\Model\CampaignResponse $campaignResponse)
    {
        $this->__load();
        return parent::addCampaignResponse($campaignResponse);
    }

    public function removeCampaignResponse(\Application\Model\CampaignResponse $campaignResponse)
    {
        $this->__load();
        return parent::removeCampaignResponse($campaignResponse);
    }

    public function getInteractions()
    {
        $this->__load();
        return parent::getInteractions();
    }

    public function setInteractions(\Doctrine\Common\Collections\ArrayCollection $interactions)
    {
        $this->__load();
        return parent::setInteractions($interactions);
    }

    public function addInteraction(\Application\Model\AbstractInteraction $interaction)
    {
        $this->__load();
        return parent::addInteraction($interaction);
    }

    public function removeInteraction(\Application\Model\AbstractInteraction $interaction)
    {
        $this->__load();
        return parent::removeInteraction($interaction);
    }

    public function getLeads()
    {
        $this->__load();
        return parent::getLeads();
    }

    public function setLeads(\Doctrine\Common\Collections\ArrayCollection $leads)
    {
        $this->__load();
        return parent::setLeads($leads);
    }

    public function addLead(\Application\Model\Lead $lead)
    {
        $this->__load();
        return parent::addLead($lead);
    }

    public function removeLead(\Application\Model\Lead $lead)
    {
        $this->__load();
        return parent::removeLead($lead);
    }

    public function getOpportunities()
    {
        $this->__load();
        return parent::getOpportunities();
    }

    public function setOpportunities(\Doctrine\Common\Collections\ArrayCollection $opportunities)
    {
        $this->__load();
        return parent::setOpportunities($opportunities);
    }

    public function addOpportunity(\Application\Model\Opportunity $opportunity)
    {
        $this->__load();
        return parent::addOpportunity($opportunity);
    }

    public function removeOpportunity(\Application\Model\Opportunity $opportunity)
    {
        $this->__load();
        return parent::removeOpportunity($opportunity);
    }

    public function getTasks()
    {
        $this->__load();
        return parent::getTasks();
    }

    public function setTasks(\Doctrine\Common\Collections\ArrayCollection $tasks)
    {
        $this->__load();
        return parent::setTasks($tasks);
    }

    public function addTask(\Application\Model\AbstractTask $task)
    {
        $this->__load();
        return parent::addTask($task);
    }

    public function removeTask(\Application\Model\AbstractTask $task)
    {
        $this->__load();
        return parent::removeTask($task);
    }

    public function getLists()
    {
        $this->__load();
        return parent::getLists();
    }

    public function setLists(\Doctrine\Common\Collections\ArrayCollection $lists)
    {
        $this->__load();
        return parent::setLists($lists);
    }

    public function addList(\Application\Model\MarketingList $list)
    {
        $this->__load();
        return parent::addList($list);
    }

    public function removeList(\Application\Model\MarketingList $list)
    {
        $this->__load();
        return parent::removeList($list);
    }

    public function getListsAsArray()
    {
        $this->__load();
        return parent::getListsAsArray();
    }

    public function getSalesLiterature()
    {
        $this->__load();
        return parent::getSalesLiterature();
    }

    public function setSalesLiterature(\Doctrine\Common\Collections\ArrayCollection $salesLiterature)
    {
        $this->__load();
        return parent::setSalesLiterature($salesLiterature);
    }

    public function addSalesLiterature(\Application\Model\SalesLiterature $literature)
    {
        $this->__load();
        return parent::addSalesLiterature($literature);
    }

    public function removeSalesLiterature(\Application\Model\SalesLiterature $literature)
    {
        $this->__load();
        return parent::removeSalesLiterature($literature);
    }

    public function equals(\Application\Stdlib\Object $o)
    {
        $this->__load();
        return parent::equals($o);
    }

    public function getAuditableProperties()
    {
        $this->__load();
        return parent::getAuditableProperties();
    }

    public function getClass()
    {
        $this->__load();
        return parent::getClass();
    }

    public function getFormattedActualEnd($format = NULL)
    {
        $this->__load();
        return parent::getFormattedActualEnd($format);
    }

    public function getFormattedActualStart($format = NULL)
    {
        $this->__load();
        return parent::getFormattedActualStart($format);
    }

    public function getFormattedProposedEnd($format = NULL)
    {
        $this->__load();
        return parent::getFormattedProposedEnd($format);
    }

    public function getFormattedProposedStart($format = NULL)
    {
        $this->__load();
        return parent::getFormattedProposedStart($format);
    }

    public function getClosedActivities()
    {
        $this->__load();
        return parent::getClosedActivities();
    }

    public function getClosedCampaignActivities()
    {
        $this->__load();
        return parent::getClosedCampaignActivities();
    }

    public function getDisplayName($includeLink = false)
    {
        $this->__load();
        return parent::getDisplayName($includeLink);
    }

    public function getOpenActivities()
    {
        $this->__load();
        return parent::getOpenActivities();
    }

    public function getOpenCampaignActivities()
    {
        $this->__load();
        return parent::getOpenCampaignActivities();
    }

    public function removeMatchedCandidates(array $candidates)
    {
        $this->__load();
        return parent::removeMatchedCandidates($candidates);
    }

    public function __toString()
    {
        $this->__load();
        return parent::__toString();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'actualEnd', 'actualStart', 'codeName', 'description', 'expectedResponse', 'expectedRevenue', 'message', 'name', 'objective', 'proposedEnd', 'proposedStart', 'state', 'status', 'type', 'creationDate', 'lastUpdateDate', 'businessUnit', 'owner', 'annotations', 'appointments', 'auditItems', 'campaignActivities', 'campaignResponses', 'interactions', 'leads', 'opportunities', 'tasks', 'lists', 'salesLiterature');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}