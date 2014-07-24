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
 * @package     Application\Model\Activity\Appointment
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: AbstractAppointment.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\TrackedActivity;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an abstract commitment of a time interval with start and end times.
 *
 * @package     Application\Model\Activity\Appointment
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AbstractAppointment.php 13 2013-08-05 22:53:55Z  $
 * @abstract
 *
 * @ORM\Entity
 */
abstract class AbstractAppointment extends TrackedActivity {
	
	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $location;
	
	/**
	 * @ORM\Column(type="datetime", name="original_start_date")
	 * @var \DateTime
	 */
	protected $originalStartDate;
	
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
	 */
	public function getLocation() {
	return $this->location;
}
	
	public function setLocation($location) {
		$this->location = (string) $location;
	}
	
	/**
	 * @return DateTime
	 */
	public function getOriginalStartDate() {
		return $this->originalStartDate;
	}
	
	public function setOriginalStartDate($originalStartDate) {
		$this->originalStartDate = $originalStartDate;
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
	
	/* ---------- Methods ----------*/
	
	/**
	 * @param string $format
	 * @return string
	 * @see \Application\Model\TrackedActivity::getFormattedActualEnd()
	 */
	public function getFormattedActualEnd($format = null) {
		if($format == null) {
			$format = 'm/d g:ia';
		}
		
		$result = '';
		if($this->getActualEnd()) {
			$result = $this->getActualEnd()->format($format);
		}
		return $result;
	}
	
	/**
	 * @param string $format
	 * @return string
	 * @see \Application\Model\TrackedActivity::getFormattedActualStart()
	 */
	public function getFormattedActualStart($format = null) {
		if($format == null) {
			$format = 'm/d g:ia';
		}
		
		$result = '';
		if($this->getActualStart()) {
			$result = $this->getActualStart()->format($format);
		}
		return $result;
	}
	
	/**
	 * @param string $format
	 * @return string
	 * @see \Application\Model\ScheduledActivity::getFormattedScheduledEnd()
	 */
	public function getFormattedScheduledEnd($format = null) {
		if($format == null) {
			$format = 'm/d g:ia';
		}
		
		$result = '';
		if($this->getScheduledEnd()) {
			$result = $this->getScheduledEnd()->format($format);
		}
		return $result;
	}
	
	/**
	 * @param string $format
	 * @return string
	 * @see \Application\Model\ScheduledActivity::getFormattedScheduledStart()
	 */
	public function getFormattedScheduledStart($format = null) {
		if($format == null) {
			$format = 'm/d g:ia';
		}
		
		$result = '';
		if($this->getScheduledStart()) {
			$result = $this->getScheduledStart()->format($format);
		}
		return $result;
	}
	
	/**
	 * @param string $format
	 * @return string
	 */
	public function getFormattedOriginalStartDate($format = null) {
		if($format == null) {
			$format = 'm/d g:ia';
		}
		
		$result = '';
		if($this->getOriginalStartDate()) {
			$result = $this->getOriginalStartDate()->format($format);
		}
		return $result;
	}
	
	/**
	 * @return string
	 * @see \Application\Model\TrackedActivity::__toString()
	 */
	public function __toString() {
		return parent::__toString()
		. ',location=' . $this->getLocation()
		. ',originalStartDate=' . $this->getFormattedOriginalStartDate('Y-m-d h:i:s')
		. ']';
	}
}
?>