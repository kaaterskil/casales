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
 * @version     SVN $Id: ScheduledActivity.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\StatefulActivity;
use Application\Model\Schedulable;
use Application\Stdlib\Comparable;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an abstract class of activity that can be scheduled, such as an appointment.
 *
 * @package     Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 * @abstract
 *
 * @ORM\Entity
 */
abstract class ScheduledActivity extends StatefulActivity implements Schedulable {
	
	/**
	 * The target start date for the activity
	 * @ORM\Column(type="datetime", name="scheduled_start")
	 * @var \DateTime
	 */
	private $scheduledStart;
	
	/**
	 * The target end date for the activity
	 * @ORM\Column(type="datetime", name="scheduled_end")
	 * @var \DateTime
	 */
	private $scheduledEnd;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return DateTime
	 */
	public function getScheduledStart() {
		return $this->scheduledStart;
	}

	public function setScheduledStart($scheduledStart) {
		$this->scheduledStart = $scheduledStart;
	}

	/**
	 * @return DateTime
	 */
	public function getScheduledEnd() {
		return $this->scheduledEnd;
	}

	public function setScheduledEnd($scheduledEnd) {
		$this->scheduledEnd = $this->filterDate( $scheduledEnd );
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
		if($result && ($o->getScheduledEnd() == $this->getScheduledEnd()) && ($o->getScheduledStart() == $this->getScheduledStart())) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return string
	 * @see \Application\Model\Schedulable::getFormattedScheduledEnd()
	 */
	public function getFormattedScheduledEnd($format = null) {
		if($format == null) {
			$format = 'm/d';
		}
		
		$result = '';
		if($this->scheduledEnd) {
			$result = $this->scheduledEnd->format($format);
		}
		return $result;
	}
	
	/**
	 * @return string
	 * @see \Application\Model\Schedulable::getFormattedScheduledStart()
	 */
	public function getFormattedScheduledStart($format = null) {
		if($format == null) {
			$format = 'm/d';
		}
		
		$result = '';
		if($this->scheduledStart) {
			$result = $this->scheduledStart->format($format);
		}
		return $result;
	}
	
	/**
	 * @return string
	 * @see \Application\Model\StatefulActivity::__toString()
	 */
	public function __toString() {
		return parent::__toString()
		. ',scheduledEnd=' . ($this->getScheduledEnd() != null ? $this->getScheduledEnd()->format('Y-m-d H:i:s') : '')
		. ',scheduledStart=' . ($this->getScheduledStart() != null ? $this->getScheduledStart()->format('Y-m-d H:i:s') : '');
	}
	
	/**
	 * Tests if Doctrine has created a DateTime object from a null value.
	 * If it has, its value is the current datetime, not null, and we must strip it.
	 *
	 * @param mixed $date
	 * @return NULL|\DateTime
	 */
	protected function filterDate($date) {
		$now = new \DateTime();
		if ($date instanceof \DateTime) {
			if ($date->format( 'Y-m-d H:i' ) == $now->format( 'Y-m-d H:i' )) {
				return null;
			}
		}
		return $date;
	}
}
?>