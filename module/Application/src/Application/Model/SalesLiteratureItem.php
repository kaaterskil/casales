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
 * @version     SVN $Id: SalesLiteratureItem.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\Organization;
use Application\Model\SalesLiterature;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an item in the sales literature collection
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: SalesLiteratureItem.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_sales_literature_item")
 */
class SalesLiteratureItem implements Entity {
	
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
	private $abstract;
	
	/**
	 * @ORM\Column(type="string", name="author_name")
	 * @var string
	 */
	private $author;
	
	/**
	 * @ORM\Column(type="string", name="document_body")
	 * @var string
	 */
	private $documentBody;
	
	/**
	 * @ORM\Column(type="string", name="attached_document_url")
	 * @var string
	 */
	private $documentUrl;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $filename;
	
	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $filesize;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $filetype;
	
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
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $mimetype;
	
	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="Organization")
	 * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
	 * @var Organization
	 */
	private $organization;
	
	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="SalesLiterature", inversedBy="salesLiteratureItems")
	 * @ORM\JoinColumn(name="sales_literature_id", referencedColumnName="id")
	 * @var SalesLiterature
	 */
	private $salesLiterature;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $title;
	
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
	 * @return string
	 */
	public function getAbstract() {
		return $this->abstract;
	}
	
	public function setAbstract($abstract) {
		$this->abstract = (string) $abstract;
	}
	
	/**
	 * @return string
	 */
	public function getAuthor() {
		return $this->author;
	}
	
	public function setAuthor($author) {
		$this->author = (string) $author;
	}
	
	/**
	 * @return string
	 */
	public function getDocumentBody() {
		return $this->documentBody;
	}
	
	public function setDocumentBody($documentBody) {
		$this->documentBody = (string) $documentBody;
	}
	
	/**
	 * @return string
	 */
	public function getDocumentUrl() {
		return $this->documentUrl;
	}
	
	public function setDocumentUrl($documentUrl) {
		$this->documentUrl = (string) $documentUrl;
	}
	
	/**
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}
	
	public function setFilename($filename) {
		$this->filename = (string) $filename;
	}
	
	/**
	 * @return int
	 */
	public function getFilesize() {
		return $this->filesize;
	}
	
	public function setFilesize($filesize) {
		$this->filesize = (int) $filesize;
	}
	
	/**
	 * @return string
	 */
	public function getFiletype() {
		return $this->filetype;
	}
	
	public function setFiletype($filetype) {
		$this->filetype = (string) $filetype;
	}
	
	/**
	 * @return boolean
	 */
	public function getIsCustomerViewable() {
		return $this->isCustomerViewable;
	}
	
	public function setIsCustomerViewable($isCustomerViewable) {
		if(is_bool($isCustomerViewable) || is_numeric($isCustomerViewable)) {
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
	 * @return string
	 */
	public function getMimetype() {
		return $this->mimetype;
	}
	
	public function setMimetype($mimetype) {
		$this->mimetype = (string) $mimetype;
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
	 * @return \Application\Model\SalesLiterature
	 */
	public function getSalesLiterature() {
		return $this->salesLiterature;
	}
	
	public function setSalesLiterature(SalesLiterature $salesLiterature = null) {
		$this->salesLiterature = $salesLiterature;
	}
	
	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = (string) $title;
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
		if(!$o instanceof $this) {
			return false;
		}
		if($o->getId() == $this->getId()) {
			return true;
		}
		if($o->getSalesLiterature()->equals($this->getSalesLiterature())
				&& $o->getTitle() == $this->getTitle()
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
		return get_class($this);
	}
	
	/**
	 * Removes an uploaded file
	 */
	public function removeFile() {
		unlink($this->getDocumentUrl());
		$this->setDocumentUrl(null);
		$this->setFilename(null);
		$this->setFilesize(0);
		$this->setFiletype(null);
	}
	
	/**
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'SalesLiteratureItem[id=' . $this->getId()
		. ',abstract=' . $this->getAbstract()
		. ',author=' . $this->getAuthor()
		. ',documentBody=' . $this->getDocumentBody()
		. ',documentUrl=' . $this->getDocumentUrl()
		. ',filename=' . $this->getFilename()
		. ',filesize=' . $this->getFilesize()
		. ',filetype=' . $this->getFiletype()
		. ',isCutomerViewable=' . ($this->isCustomerViewable ? 'true' : 'false')
		. ',keywords=' . $this->getKeywords()
		. ',mimetype=' . $this->getMimetype()
		. ',organization=' . ($this->getOrganization() != null ? $this->getOrganization()->getId() : '')
		. ',salesLiterature=' . ($this->getSalesLiterature() != null ? $this->getSalesLiterature()->getId() : '')
		. ',title=' . $this->getTitle()
		. ',creationDate=' . $this->getCreationDate()
		. ',lastUpdateDate=' . $this->getLastUpdateDate()
		. ']';
	}
}
?>