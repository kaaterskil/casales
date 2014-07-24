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
 * @package     Application\Model\Activity\Interaction
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: FaxInteraction.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractInteraction;
use Application\Model\FaxStatus;
use Application\Model\Telephone;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an activity that tracks call outcome and number of pages.
 *
 * @package     Application\Model\Activity\Interaction
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management,
 *
 * @ORM\Entity
 */
class FaxInteraction extends AbstractInteraction {
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $telephone;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::getDiscriminator()
	 */
	public function getDiscriminator() {
		if($this->discriminator == null) {
			$this->discriminator = 'FaxInteraction';
		}
		return $this->discriminator;
	}
	
	/**
	 * @return string
	 */
	public function getTelephone() {
		return $this->telephone;
	}
	
	/**
	 * @param string $telephone
	 */
	public function setTelephone($telephone) {
		if ($telephone != null && $telephone != '' && $telephone != '() -') {
			$this->telephone = Telephone::formatPhoneNumber( $telephone );
		} else {
			$this->telephone = null;
		}
	}
	
	/**
	 * @return \Application\Model\FacStatus
	 * @see \Application\Model\StatefulActivity::getStatus()
	 */
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus($status = null) {
		if($status instanceof FaxStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = FaxStatus::instance((string) $status);
		} else {
			$this->status = null;
		}
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Model\AbstractInteraction::equals()
	 */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			return false;
		}
		if(parent::equals($o) && $o->getTelephone() == $this->getTelephone()) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return string
	 * @see \Application\Model\AbstractInteraction::__toString()
	 */
	public function __toString() {
		return parent::__toString()
		. ',telephone=' . $this->getTelephone()
		. ']';
	}
}
?>