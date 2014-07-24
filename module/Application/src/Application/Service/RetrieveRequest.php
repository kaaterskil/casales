<?php

/**
 * Casales Library
 *
 * PHP version 5.4
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
 * KAATERSKIL MANAGEMENT, LLC BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Casales
 * @package     Application\Service
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: RetrieveRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\Request;
use Application\Service\RetrieveResponse;
use Application\Service\TargetRetrieve;

/**
 * Contains the data needed to retrieve information for the specified entity instance.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: RetrieveRequest.php 13 2013-08-05 22:53:55Z  $
 */
class RetrieveRequest extends Request {
	/**
	 * @var TargetRetrieve
	 */
	private $target;
	
	/* ---------- Constructor ---------- */
	
	public function __construct() {
		parent::__construct('RetrieveRequest');
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Service\TargetRetrieve
	 */
	public function getTarget() {
		return $this->target;
	}
	
	/**
	 * @param TargetRetrieve $target
	 */
	public function setTarget(TargetRetrieve $target) {
		$this->target = $target;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\RetrieveResponse
	 */
	public function execute() {
		return $this->target->retrieve();
	}
	
	/**
	 * @return \Application\Service\RetrieveResponse
	 */
	public function retrieve() {
		return $this->target->retrieve();
	}
}
?>