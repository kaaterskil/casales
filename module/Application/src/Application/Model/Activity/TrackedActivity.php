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
 * @version     SVN $Id: TrackedActivity.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\ScheduledActivity;
use Application\Model\Trackable;
use Application\Stdlib\Comparable;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an abstract activity that can be tracked with actual start and end dates.
 *
 * @package     Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 *
 * @ORM\Entity
 */
abstract class TrackedActivity extends ScheduledActivity implements Trackable {

	/**
	 * The actual start date of the activity
	 * @ORM\Column(type="datetime", name="actual_start")
	 * @var \DateTime
	 */
	protected $actualStart;

	/**
	 * The actual end date of the activity
	 * @ORM\Column(type="datetime", name="actual_end")
	 * @var \DateTime
	 */
	protected $actualEnd;
	
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
	 * @see \Application\Model\Trackable::getActualStart()
	 */
	public function getActualStart() {
		return $this->actualStart;
	}

	public function setActualStart($actualStart = null, $bypassFilter = false) {
		if ($bypassFilter) {
			$this->actualStart = $actualStart;
		} else {
			$this->actualStart = $this->filterDate( $actualStart );
		}
	}

	/**
	 * @return DateTime
	 * @see \Application\Model\Trackable::getActualEnd()
	 */
	public function getActualEnd() {
		return $this->actualEnd;
	}

	public function setActualEnd($actualEnd = null, $bypassFilter = false) {
		if ($bypassFilter) {
			$this->actualEnd = $actualEnd;
		} else {
			$this->actualEnd = $this->filterDate( $actualEnd );
		}
	}
	
	/* ---------- Method ---------- */
	
	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Model\AbstractActivity::equals()
	 */
	public function equals(Object $o) {
		if (!$o instanceof $this) {
			return false;
		}
		
		$result = parent::equals( $o );
		if ($result && ($o->getActualEnd() == $this->getActualEnd())
				&& ($o->getActualStart() == $this->getActualStart())) {
			return true;
		}
		return false;
	}

	/**
	 * @return string
	 * @see \Application\Model\Trackable::getFormattedActualEnd()
	 */
	public function getFormattedActualEnd($format = null) {
		if ($format == null) {
			$format = 'm/d';
		}
		
		$result = '';
		if ($this->actualEnd) {
			$result = $this->actualEnd->format( $format );
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Model\Trackable::getFormattedActualStart()
	 */
	public function getFormattedActualStart($format = null) {
		if ($format == null) {
			$format = 'm/d';
		}
		
		$result = '';
		if ($this->actualStart) {
			$result = $this->actualStart->format( $format );
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Model\StatefulActivity::__toString()
	 */
	public function __toString() {
		return parent::__toString()
		. ',actualEnd=' . ($this->getActualEnd() != null ? $this->getActualEnd()->format( 'Y-m-d H:i:s' ) : '')
		. ',actualStart=' . ($this->getActualStart() != null ? $this->getActualStart()->format( 'Y-m-d H:i:s' ) : '');
	}
}
?>