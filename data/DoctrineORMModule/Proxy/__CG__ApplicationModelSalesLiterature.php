<?php

namespace DoctrineORMModule\Proxy\__CG__\Application\Model;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class SalesLiterature extends \Application\Model\SalesLiterature implements \Doctrine\ORM\Proxy\Proxy
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

    public function getDescription()
    {
        $this->__load();
        return parent::getDescription();
    }

    public function setDescription($description)
    {
        $this->__load();
        return parent::setDescription($description);
    }

    public function getExpirationDate()
    {
        $this->__load();
        return parent::getExpirationDate();
    }

    public function setExpirationDate($expirationDate)
    {
        $this->__load();
        return parent::setExpirationDate($expirationDate);
    }

    public function getHasAttachments()
    {
        $this->__load();
        return parent::getHasAttachments();
    }

    public function setHasAttachments($hasAttachments)
    {
        $this->__load();
        return parent::setHasAttachments($hasAttachments);
    }

    public function getIsCustomerViewable()
    {
        $this->__load();
        return parent::getIsCustomerViewable();
    }

    public function setIsCustomerViewable($isCustomerViewable)
    {
        $this->__load();
        return parent::setIsCustomerViewable($isCustomerViewable);
    }

    public function getKeywords()
    {
        $this->__load();
        return parent::getKeywords();
    }

    public function setKeywords($keywords)
    {
        $this->__load();
        return parent::setKeywords($keywords);
    }

    public function getLiteratureType()
    {
        $this->__load();
        return parent::getLiteratureType();
    }

    public function setLiteratureType($literatureType = NULL)
    {
        $this->__load();
        return parent::setLiteratureType($literatureType);
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

    public function getOrganization()
    {
        $this->__load();
        return parent::getOrganization();
    }

    public function setOrganization(\Application\Model\Organization $organization = NULL)
    {
        $this->__load();
        return parent::setOrganization($organization);
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

    public function getSalesLiteratureItems()
    {
        $this->__load();
        return parent::getSalesLiteratureItems();
    }

    public function setSalesLiteratureItems(\Doctrine\Common\Collections\ArrayCollection $salesLiteratureItems)
    {
        $this->__load();
        return parent::setSalesLiteratureItems($salesLiteratureItems);
    }

    public function addSalesLiteratureitem(\Application\Model\SalesLiteratureItem $item)
    {
        $this->__load();
        return parent::addSalesLiteratureitem($item);
    }

    public function removeSalesLiteratureItem(\Application\Model\SalesLiteratureItem $item)
    {
        $this->__load();
        return parent::removeSalesLiteratureItem($item);
    }

    public function getCampaigns()
    {
        $this->__load();
        return parent::getCampaigns();
    }

    public function setCampaigns(\Doctrine\Common\Collections\ArrayCollection $campaigns)
    {
        $this->__load();
        return parent::setCampaigns($campaigns);
    }

    public function addCampaign(\Application\Model\Campaign $campaign)
    {
        $this->__load();
        return parent::addCampaign($campaign);
    }

    public function removeCampaign(\Application\Model\Campaign $campaign)
    {
        $this->__load();
        return parent::removeCampaign($campaign);
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

    public function equals(\Application\Stdlib\Object $o)
    {
        $this->__load();
        return parent::equals($o);
    }

    public function getClass()
    {
        $this->__load();
        return parent::getClass();
    }

    public function getFormattedExpirationDate()
    {
        $this->__load();
        return parent::getFormattedExpirationDate();
    }

    public function __toString()
    {
        $this->__load();
        return parent::__toString();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'description', 'expirationDate', 'hasAttachments', 'isCustomerViewable', 'keywords', 'literatureType', 'name', 'creationDate', 'lastUpdateDate', 'organization', 'owner', 'salesLiteratureItems', 'campaigns', 'campaignActivities');
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