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
 * @version     SVN $Id: AbstractInteraction.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\BulkOperation;
use Application\Model\TrackedActivity;
use Application\Model\Direction;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an abstract interaction between two or more parties.
 *
 * @package     Application\Model\Activity\Interaction
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AbstractInteraction.php 13 2013-08-05 22:53:55Z  $
 * @abstract
 *
 * @ORM\Entity
 */
abstract class AbstractInteraction extends TrackedActivity {

	/**
	 * Bidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="BulkOperation", inversedBy="bulkInteractions")
	 * @ORM\JoinColumn(name="bulk_operation_id", referencedColumnName="id")
	 * @var BulkOperation
	 */
	protected $bulkOperation;
	
	/**
	 * @ORM\Column(type="string")
	 * @var Direction
	 */
	protected $direction;
	
	/**
	 * @ORM\Column(type="string", name="from_sender")
	 * @var string
	 */
	
	protected $from;
	
	/**
	 * @ORM\Column(type="string", name="to_recipients")
	 * @var string
	 */
	protected $to;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\BulkOperation
	 */
	public function getBulkOperation() {
		return $this->bulkOperation;
	}
	
	public function setBulkOperation(BulkOperation $bulkOperation = null) {
		$this->bulkOperation = $bulkOperation;
	}

	/**
	 * @return \Application\Model\Direction
	 */
	public function getDirection() {
		return $this->direction;
	}

	public function setDirection($direction = null) {
		if ($direction instanceof Direction) {
			$this->direction = $direction;
		} elseif ($direction != null) {
			$this->direction = Direction::instance( $direction );
		} else {
			$this->direction = null;
		}
	}
	
	/**
	 * @return string
	 */
	public function getFrom() {
		return $this->from;
	}
	
	public function setFrom($from) {
		$this->from = (string) $from;
	}
	
	/**
	 * @return string
	 */
	public function getTo() {
		return $this->to;
	}
	
	public function setTo($to) {
		$this->to = (string) $to;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Model\TrackedActivity::equals()
	 */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			return false;
		}
		$result = parent::equals($o);
		if($result && ($o->getFrom() == $this->getFrom())
				&& ($o->getTo() == $this->getTo())
				&& ($o->getDirection() == $this->getDirection())
				&& ($o->getBulkOperation() == $this->getBulkOperation())) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::__toString()
	 */
	public function __toString() {
		return parent::__toString()
		. ',bulkOperation=' . ($this->getBulkOperation() != null ? $this->getBulkOperation()->getId() : '')
		. ',direction=' . $this->getDirection()
		. ',from=' . $this->getFrom()
		. ',to=' . $this->getTo();
	}
}
?>