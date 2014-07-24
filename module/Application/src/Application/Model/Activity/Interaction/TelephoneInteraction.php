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
 * @version     SVN $Id: TelephoneInteraction.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractInteraction;
use Application\Model\Account;
use Application\Model\Contact;
use Application\Model\Lead;
use Application\Model\Telephone;
use Application\Model\TelephoneStatus;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an activity to track a telephone call.
 *
 * @package     Application\Model\Activity\Interaction
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TelephoneInteraction.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 */
class TelephoneInteraction extends AbstractInteraction {
	
	/**
	 * @ORM\Column(type="integer", name="left_voice_mail")
	 * @var boolean
	 */
	private $leftVoiceMail;
	
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
			$this->discriminator = 'TelephoneInteraction';
		}
		return $this->discriminator;
	}
	
	/**
	 * @return boolean
	 */
	public function getLeftVoiceMail() {
		return $this->leftVoiceMail;
	}
	
	public function setLeftVoiceMail($leftVoiceMail) {
		if (is_bool( $leftVoiceMail ) || is_int( $leftVoiceMail )) {
			$this->leftVoiceMail = $leftVoiceMail;
		} else {
			$this->leftVoiceMail = $leftVoiceMail == 'true' ? true : false;
		}
	}
	
	/**
	 * @return \Application\Model\TelephoneStatus
	 * @see \Application\Model\StatefulActivity::getStatus()
	 */
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus($status = null) {
		if($status instanceof TelephoneStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = TelephoneStatus::instance((string) $status);
		} else {
			$this->status = null;
		}
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
			$telephone = Telephone::unformatPhoneNumber($telephone);
			$this->telephone = Telephone::formatPhoneNumber( $telephone );
		} else {
			$this->telephone = null;
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
	
	public function __toString() {
		return parent::__toString()
		. ',leftVoiceMail=' . ($this->getLeftVoiceMail() ? 'true' : 'false')
		. ',telephone=' . $this->getTelephone()
		. ']';
	}
}
?>