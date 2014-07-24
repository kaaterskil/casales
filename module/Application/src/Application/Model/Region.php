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
 * @version     SVN $Id: Region.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Region class
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Region.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_region")
 */
class Region implements Entity {

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
	private $name;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $abbreviation;

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
	
	/* ----- Getter/Setters ----- */
	
	/**
	 * @return int
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
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getAbbreviation() {
		return $this->abbreviation;
	}

	public function setAbbreviation($abbreviation) {
		$this->abbreviation = $abbreviation;
	}

	/**
	 * @return DateTime
	 * @see \Application\Stdlib\Entity::getCreationDate()
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	public function setCreationDate($creation_date) {
		$this->creationDate = $creation_date;
	}

	/**
	 * @return DateTime
	 * @see \Application\Stdlib\Entity::getLastUpdateDate()
	 */
	public function getLastUpdateDate() {
		return $this->lastUpdateDate;
	}

	public function setLastUpdateDate($last_update_date) {
		$this->lastUpdateDate = $last_update_date;
	}
	
	/* ----- Methods ----- */
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
		if ($o->getName() == $this->getName()) {
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
		return 'Region[id=' . $this->getId()
		. ',name=' . $this->getName()
		. ',abbreviation=' . $this->getAbbreviation()
		. ',creationDate=' . $this->getCreationDate()->format( 'Y-m-d H:i:s' )
		. ',lastUpdateDate=' . $this->getLastUpdateDate()->format( 'Y-m-d H:i:s' )
		. ']';
	}
}
?>