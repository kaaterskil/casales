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
 * @package     Application\Model\Activity\Task
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: AbstractTask.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\TrackedActivity;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Application\Model\TaskStatus;
use Doctrine\ORM\Mapping as ORM;
use Zend\Filter\Int;

/**
 * Represents an abstract activity that represents work needed to be done.
 *
 * @package     Application\Model\Activity\Task
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AbstractTask.php 13 2013-08-05 22:53:55Z  $
 * @abstract
 *
 * @ORM\Entity
 */
abstract class AbstractTask extends TrackedActivity {

	/**
	 * @ORM\Column(type="integer", name="percent_complete")
	 * @var int
	 */
	protected $percentComplete;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return int
	 */
	public function getPercentComplete() {
		return $this->percentComplete;
	}

	public function setPercentComplete($percentComplete) {
		$percentComplete = (int) $percentComplete;
		if ($percentComplete < 0 || $percentComplete > 100) {
			throw new \InvalidArgumentException( 'Percent Complete must be a value between 0 and 100.' );
		}
		$this->percentComplete = $percentComplete;
	}

	/**
	 * @return \Application\Model\TaskStatus
	 * @see \Application\Model\StatefulActivity::getStatus()
	 */
	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status = null) {
		if ($status instanceof TaskStatus) {
			$this->status = $status;
		} elseif ($status != null) {
			$this->status = TaskStatus::instance( (string) $status );
		} else {
			$this->status = null;
		}
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return string
	 * @see \Application\Model\TrackedActivity::__toString()
	 */
	public function __toString() {
		return parent::__toString() .
			 ',percentComplete=' .
			 $this->getPercentComplete() .
			 ']';
	}
}