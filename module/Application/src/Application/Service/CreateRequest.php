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
 * @version     SVN $Id: CreateRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\CreateResponse;
use Application\Service\Request;
use Application\Service\TargetCreate;

/**
 * Contains the data needed to create an entity instance.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: CreateRequest.php 13 2013-08-05 22:53:55Z  $
 */
class CreateRequest extends Request {

	/**
	 * @var TargetCreate
	 */
	private $target;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'CreateRequest' );
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Service\TargetCreate
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * @param TargetCreate $target
	 */
	public function setTarget(TargetCreate $target) {
		$this->target = $target;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\CreateResponse
	 */
	public function create() {
		return $this->target->create();
	}

	/**
	 * @return \Application\Service\CreateResponse
	 */
	public function execute() {
		return $this->target->create();
	}
}
?>