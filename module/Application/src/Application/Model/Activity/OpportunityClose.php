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
 * @version     SVN $Id: OpportunityClose.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\OpportunityCloseState;
use Application\Model\OpportunityCloseStatus;
use Application\Model\Schedulable;
use Application\Model\StatefulActivity;
use Application\Model\Trackable;

use Application\Stdlib\Entity;
use Application\Stdlib\Object;

use Doctrine\ORM\Mapping as ORM;
use Application\Stdlib\Exception\NullPointerException;
use Application\Stdlib\Exception\ClassCastException;
use Application\Stdlib\Comparable;

/**
 * Represents a special activity that is created when an opportunity is closed.
 *
 * @package     Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 *
 * @ORM\Entity
 */
class OpportunityClose extends StatefulActivity implements Schedulable, Trackable, Comparable {
	/**
	 * @ORM\Column(type="datetime", name="actual_end")
	 * @var \DateTime
	 */
	private $actualEnd;

	/**
	 * @ORM\Column(type="datetime", name="actual_start")
	 * @var \DateTime
	 */
	private $actualStart;
	
	/**
	 * @ORM\Column(type="integer", name="actual_revenue")
	 * @var int
	 */
	private $actualRevenue;
	
	/**
	 * @ORM\Column(type="datetime", name="scheduled_end")
	 * @var \DateTime
	 */
	private $scheduledEnd;
	
	/**
	 * @ORM\Column(type="datetime", name="scheduled_start")
	 * @var \DateTime
	 */
	private $scheduledStart;
	
	/* ---------- Constructor ----------*/
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/* ---------- Getter/Setters ----------*/
	
	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::getDiscriminator()
	 */
	public function getDiscriminator() {
		if($this->discriminator == null) {
			$this->discriminator = 'OpportunityClose';
		}
		return $this->discriminator;
	}

	/**
	 * @return DateTime
	 * @see \Application\Model\Trackable::getActualEnd()
	 */
	public function getActualEnd() {
		return $this->actualEnd;
	}

	public function setActualEnd($actualEnd = null) {
		$this->actualEnd = $this->filterDate( $actualEnd );
	}

	/**
	 * @return DateTime
	 * @see \Application\Model\Trackable::getActualStart()
	 */
	public function getActualStart() {
		return $this->actualStart;
	}

	public function setActualStart($actualStart = null) {
		$this->actualStart = $this->filterDate( $actualStart );
	}
	
	/**
	 * @return int
	 */
	public function getActualRevenue() {
		return $this->actualRevenue;
	}
	
	public function setActualRevenue($actualRevenue) {
		$this->actualRevenue = (int) $actualRevenue;
	}
	
	/**
	 * @return DateTime
	 * @see \Application\Model\Schedulable::getScheduledEnd()
	 */
	public function getScheduledEnd() {
		return $this->scheduledEnd;
	}
	
	public function setScheduledEnd($scheduledEnd = null) {
		$this->scheduledEnd = $this->filterDate($scheduledEnd);
	}
	
	/**
	 * @return DateTime
	 * @see \Application\Model\Schedulable::getScheduledStart()
	 */
	public function getScheduledStart() {
		return $this->scheduledStart;
	}
	
	public function setScheduledStart($scheduledStart = null) {
		$this->scheduledStart = $$this->filterDate($scheduledStart);
	}
	
	/**
	 * @return \Application\Model\OpportunityCloseStatus
	 * @see \Application\Model\StatefulActivity::getStatus()
	 */
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus($status = null) {
		if($status instanceof OpportunityCloseStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = OpportunityCloseStatus::instance($status);
		} else {
			$this->status = null;
		}
	}
	/* ---------- Method ----------*/
	
	public function getDiscriminatorTitle() {
		return 'Close';
	}
	
	/**
	 * @return string
	 * @see \Application\Model\Trackable::getFormattedActualEnd()
	 */
	public function getFormattedActualEnd($format = null) {
		if($format == null) {
			$format = 'm/d';
		}
		return $this->formatDate($this->getActualEnd(), $format);
	}
	
	/**
	 * @return string
	 * @see \Application\Model\Trackable::getFormattedActualStart()
	 */
	public function getFormattedActualStart($format = null) {
		if($format == null) {
			$format = 'm/d';
		}
		return $this->formatDate($this->getActualStart(), $format);
	}
	
	/**
	 * @param string $format
	 * @return string
	 * @see \Application\Model\Schedulable::getFormattedScheduledEnd()
	 */
	public function getFormattedScheduledEnd($format = null) {
		if($format == null) {
			$format = 'm/d';
		}
		return $this->formatDate($this->getScheduledEnd(), $format);
	}
	
	/**
	 * @param string $format
	 * @return string
	 * @see \Application\Model\Schedulable::getFormattedScheduledStart()
	 */
	public function getFormattedScheduledStart($format = null) {
		if($format == null) {
			$format = 'm/d';
		}
		return $this->formatDate($this->getScheduledStart(), $format);
	}
	
	/**
	 * Tests if Doctrine has created a DateTime object from a null value.
	 * If it has, its value is the current datetime, not null, and we must strip it.
	 *
	 * @param mixed $date
	 * @return NULL|\DateTime
	 */
	private function filterDate($date = null) {
		if(($date != null) && (!$date instanceof \DateTime)) {
			$date = new \DateTime($date);
		}
		if ($date instanceof \DateTime) {
			$now = new \DateTime();
			if ($date->format( 'Y-m-d H:i' ) == $now->format( 'Y-m-d H:i' )) {
				return null;
			}
		}
		return $date;
	}
	
	/**
	 * Returns the given date as a string with the given format
	 * @param DateTime $date
	 * @param string $format
	 * @return string
	 */
	private function formatDate(\DateTime $date = null, $format) {
		$result = '';
		if($date) {
			$result = $date->format($format);
		}
		return $result;
	}
	
	/**
	 * @param Object $value
	 * @throws NullPointerException
	 * @throws ClassCastException
	 * @return int
	 * @see \Application\Stdlib\Comparable::compareTo()
	 */
	public function compareTo(Object $value = null) {
		if ($value == null) {
			throw new NullPointerException();
		}
		if (!$value instanceof $this) {
			throw new ClassCastException();
		}
		
		if ($this->getActualEnd() == null && $value->getActualEnd() == null) {
			return 0;
		} elseif ($this->getActualEnd() == null && $value->getActualEnd() != null) {
			return -1;
		} elseif ($this->getActualEnd() != null && $value->getActualEnd() == null) {
			return 1;
		} else {
			$thisTimestamp = $this->getActualEnd()->getTimestamp();
			$thatTimestamp = $value->getActualEnd()->getTimestamp();
			if ($thisTimestamp < $thatTimestamp) {
				return -1;
			} elseif ($thisTimestamp == $thatTimestamp) {
				return 0;
			}
		}
		return 1;
	}
}
?>