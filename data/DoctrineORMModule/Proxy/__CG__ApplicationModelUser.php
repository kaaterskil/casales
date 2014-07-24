<?php

namespace DoctrineORMModule\Proxy\__CG__\Application\Model;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class User extends \Application\Model\User implements \Doctrine\ORM\Proxy\Proxy
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

    public function getAccessMode()
    {
        $this->__load();
        return parent::getAccessMode();
    }

    public function setAccessMode($accessMode = NULL)
    {
        $this->__load();
        return parent::setAccessMode($accessMode);
    }

    public function getBusinessUnit()
    {
        $this->__load();
        return parent::getBusinessUnit();
    }

    public function setBusinessUnit(\Application\Model\BusinessUnit $businessUnit = NULL)
    {
        $this->__load();
        return parent::setBusinessUnit($businessUnit);
    }

    public function getDisabledReason()
    {
        $this->__load();
        return parent::getDisabledReason();
    }

    public function setDisabledReason($disabledReason)
    {
        $this->__load();
        return parent::setDisabledReason($disabledReason);
    }

    public function getEmail()
    {
        $this->__load();
        return parent::getEmail();
    }

    public function setEmail($email)
    {
        $this->__load();
        return parent::setEmail($email);
    }

    public function getEmailSignature()
    {
        $this->__load();
        return parent::getEmailSignature();
    }

    public function setEmailSignature($emailSignature)
    {
        $this->__load();
        return parent::setEmailSignature($emailSignature);
    }

    public function getFirstName()
    {
        $this->__load();
        return parent::getFirstName();
    }

    public function setFirstName($firstName)
    {
        $this->__load();
        return parent::setFirstName($firstName);
    }

    public function getFullName()
    {
        $this->__load();
        return parent::getFullName();
    }

    public function setFullName($fullName)
    {
        $this->__load();
        return parent::setFullName($fullName);
    }

    public function getIsDisabled()
    {
        $this->__load();
        return parent::getIsDisabled();
    }

    public function setIsDisabled($isDisabled)
    {
        $this->__load();
        return parent::setIsDisabled($isDisabled);
    }

    public function getJobTitle()
    {
        $this->__load();
        return parent::getJobTitle();
    }

    public function setJobTitle($jobTitle)
    {
        $this->__load();
        return parent::setJobTitle($jobTitle);
    }

    public function getLastName()
    {
        $this->__load();
        return parent::getLastName();
    }

    public function setLastName($lastName)
    {
        $this->__load();
        return parent::setLastName($lastName);
    }

    public function getLicenseType()
    {
        $this->__load();
        return parent::getLicenseType();
    }

    public function setLicenseType($licenseType = NULL)
    {
        $this->__load();
        return parent::setLicenseType($licenseType);
    }

    public function getMiddleName()
    {
        $this->__load();
        return parent::getMiddleName();
    }

    public function setMiddleName($middleName)
    {
        $this->__load();
        return parent::setMiddleName($middleName);
    }

    public function getNickname()
    {
        $this->__load();
        return parent::getNickname();
    }

    public function setNickname($nickname)
    {
        $this->__load();
        return parent::setNickname($nickname);
    }

    public function getPassword()
    {
        $this->__load();
        return parent::getPassword();
    }

    public function setPassword($password)
    {
        $this->__load();
        return parent::setPassword($password);
    }

    public function getSalutation()
    {
        $this->__load();
        return parent::getSalutation();
    }

    public function setSalutation($salutation = NULL)
    {
        $this->__load();
        return parent::setSalutation($salutation);
    }

    public function getUsername()
    {
        $this->__load();
        return parent::getUsername();
    }

    public function setUsername($username)
    {
        $this->__load();
        return parent::setUsername($username);
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

    public function computeFullName()
    {
        $this->__load();
        return parent::computeFullName();
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

    public function __toString()
    {
        $this->__load();
        return parent::__toString();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'accessMode', 'disabledReason', 'email', 'emailSignature', 'firstName', 'fullName', 'isDisabled', 'jobTitle', 'lastName', 'licenseType', 'middleName', 'nickname', 'password', 'salutation', 'username', 'creationDate', 'lastUpdateDate', 'businessUnit', 'auditItems');
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