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
 * @version     SVN $Id: VisitInteraction.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractInteraction;
use Application\Model\Address;
use Application\Model\AppointmentStatus;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an activity to record an unscheduled meeting.
 *
 * @package     Application\Model\Activity\Interaction
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: VisitInteraction.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 */
class VisitInteraction extends AbstractInteraction {
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $address;
	
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
	 */
	public function getAddress() {
		return $this->address;
	}
	
	/**
	 * @param string $address
	 */
	public function setAddress($address) {
		$this->address = (string) $address;
	}
	
	/**
	 * @return \Application\Model\AppointmentStatus
	 * @see \Application\Model\StatefulActivity::getStatus()
	 */
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus($status = null) {
		if($status instanceof AppointmentStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = AppointmentStatus::instance((string) $status);
		} else {
			$this->status = null;
		}
	}
	
	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::getDiscriminator()
	 */
	public function getDiscriminator() {
		if($this->discriminator == null) {
			$this->discriminator = 'VisitInteraction';
		}
		return $this->discriminator;
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
		if(parent::equals($o) && $o->getAddress() == $this->getAddress()) {
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
		. ',address=' . $this->getAddress()
		. ']';
	}
}
?>