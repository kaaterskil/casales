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
 * @package     Application\Model\Activity\Note
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: AbstractNote.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractActivity;
use Application\Model\ActivityPriority;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an abstract mechanism for attaching notes to records.
 *
 * @package     Application\Model\Activity\Note
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AbstractNote.php 13 2013-08-05 22:53:55Z  $
 * @abstract
 *
 * @ORM\Entity
 */
abstract class AbstractNote extends AbstractActivity {
	
	/**
	 * @ORM\Column(type="string")
	 * @var ActivityPriority
	 */
	protected $priority;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\ActivityPriority
	 */
	public function getPriority() {
		return $this->priority;
	}
	
	public function setPriority($priority = null) {
		if($priority instanceof ActivityPriority) {
			$this->priority = $priority;
		} elseif ($priority != null) {
			$this->priority = ActivityPriority::instance($priority);
		} else {
			$this->priority = null;
		}
	}
	
	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::__toString()
	 */
	public function __toString() {
		return parent::__toString()
			. ',priority=' . $this->getPriority()
			. ']';
	}
}
?>