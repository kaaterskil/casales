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
 * @version     SVN $Id: AccountGroup.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Stdlib\Entity;
use Application\Stdlib\Object;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a grouping of accounts.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AccountGroup.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_group")
 */
class AccountGroup implements Entity {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="AccountGroup")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	private $parent = null;
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $description;

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

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Account", mappedBy="accountGroup")
	 * @var ArrayCollection
	 */
	private $accounts = array();

	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="AccountGroup", mappedBy="parent")
	 * @var ArrayCollection
	 */
	private $children = array();

	/*----- Constructor -----*/
	
	public function __construct() {
		$this->accounts = new ArrayCollection();
		$this->children = new ArrayCollection();
	}

	/*----- Getter/Setters -----*/
	
	/** @return int */
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = (int) $id;
	}
	
	/** @return \AccountGroup */
	public function getParent() {
		return $this->parent;
	}
	
	public function setParent(AccountGroup $parent = null) {
		$this->parent = $parent;
	}
	
	/** @return string */
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	public function getCreationDate() {
		return $this->creationDate;
	}
	
	public function setCreationDate($creation_date) {
		$this->creationDate = $creation_date;
	}

	public function getLastUpdateDate() {
		return $this->lastUpdateDate;
	}
	
	public function setLastUpdateDate($last_update_date) {
		$this->lastUpdateDate = $last_update_date;
	}
	
	/** @return \ArrayCollection */
	public function getAccounts() {
		return $this->accounts;
	}
	
	public function setAccounts(ArrayCollection $accounts) {
		$this->accounts = $accounts;
	}
	
	/** @return \ArrayCollection */
	public function getChildren() {
		return $this->children;
	}
	
	public function setChildren(ArrayCollection $children) {
		$this->children = $children;
	}

	/*----- Methods -----*/
	
	public function addAccount(Account $account) {
		$account->setAccountGroup($this);
		$this->accounts->add($account);
	}
	
	public function addChild(AccountGroup $child) {
		$child->setParent($this);
		$this->children->add($child);
	}
	
	/** @see \Application\Stdlib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			return false;
		}
		if($o->getId() == $this->getId()) {
			return true;
		}
		if(($o->getDescription() == $this->getDescription())
				&& ($o->getParent()->equals($this->getParent()))) {
			return true;
		}
		return false;
	}
	
	/** @see \Application\Model\Object::getClass() */
	public function getClass() {
		return get_class($this);
	}
	
	/** @see \Application\Model\Object::__toString() */
	public function __toString() {
		return 'AccountGroup[id=' . $this->getId()
		. ',description=' . $this->getDescription()
		. ',parent=' . $this->getParent()->getDescription()
		. ',creationDate=' . $this->getCreationDate()->format('Y-m-d H:i:s')
		. ',lastUpdateDate=' . $this->getLastUpdateDate()->format('Y-m-d H:i:s')
		. ']';
	}
}
?>