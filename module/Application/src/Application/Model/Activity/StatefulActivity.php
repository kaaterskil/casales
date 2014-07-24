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
 * @package     Application\Model\Activity
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: StatefulActivity.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractActivity;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Doctrine\ORM\Mapping as ORM;
use Application\Stdlib\Object;

/**
 * Represents an abstract activity whose state can change and be managed.
 *
 * @package     Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 * @abstract
 *
 * @ORM\Entity
 */
abstract class StatefulActivity extends AbstractActivity {
	
	/**
	 * Prioritization of the activity
	 * @ORM\Column(type="string")
	 * @var ActivityPriority
	 */
	private $priority;
	
	/**
	 * @ORM\Column(type="string")
	 * @var ActivityState
	 */
	private $state;
	
	/**
	 * @ORM\Column(type="string")
	 * @var ActivityStatus
	 */
	protected $status;
	
	/* ---------- Constructor ----------*/
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/* ---------- Getter/Setters ----------*/

	/**
	 * @return ActivityPriority
	 */
	public function getPriority() {
		return $this->priority;
	}

	public function setPriority($priority = null) {
		if ($priority instanceof ActivityPriority) {
			$this->priority = $priority;
		} elseif ($priority != null) {
			$this->priority = ActivityPriority::instance( $priority );
		} else {
			$this->priority = null;
		}
	}

	/**
	 * @return ActivityState
	 */
	public function getState() {
		return $this->state;
	}

	public function setState($state = null) {
		if ($state instanceof ActivityState) {
			$this->state = $state;
		} elseif ($state != null) {
			$this->state = ActivityState::instance( $state );
		} else {
			$this->state = null;
		}
	}

	/**
	 * @return ActivityStatus
	 */
	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status = null) {
		if ($status instanceof ActivityStatus) {
			$this->status = $status;
		} elseif ($status != null) {
			$this->status = ActivityStatus::instance( $status );
		} else {
			$this->status = null;
		}
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Model\AbstractActivity::equals()
	 */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			return false;
		}
		
		$result = parent::equals($o);
		if($result && ($o->getPriority() == $this->getPriority()) && ($o->getStatus() == $this->getStatus())) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::__toString()
	 */
	public function __toString() {
		return  parent::__toString()
		. ',priority' . $this->getPriority()
		. ',state=' . $this->getState()
		. ',status=' . $this->getStatus();
	}
}
?>