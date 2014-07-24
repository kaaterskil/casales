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
 * @version     SVN $Id: Request.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\Command;
use Application\Service\Response;
use Application\Stdlib\Object;

/**
 * Represents the abstract base class for the request parameter used in the Execute method.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Request.php 13 2013-08-05 22:53:55Z  $
 */
abstract class Request implements Object, Command {
	/**
	 * The name of the request
	 * @var string
	 */
	private $requestName;
	
	/**
	 * The collection of parameters for the request
	 * @var array
	 */
	private $parameters = array();
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null) {
		$this->requestName = $name;
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return string
	 */
	public function getRequestName() {
		return $this->requestName;
	}
	
	public function setRequestName($name) {
		$this->requestName = (string) $name;
	}
	
	/**
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}
	
	public function setParameters(array $parameters) {
		$this->parameters = $parameters;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Stdlib\Object::equals()
	 */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			return false;
		}
		if($o->getRequestName() == $this->getRequestName()
				&& $o->getParameters() == $this->getParameters()) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return Response
	 * @see \Application\Service\Command::execute()
	 */
	abstract public function execute();
	
	/**
	 * @return string
	 * @see \Application\Stdlib\Object::getClass()
	 */
	public function getClass() {
		return get_class($this);
	}
	
	/**
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		$result = 'Request[name=' . $this->requestName
		. 'parameters={';
		foreach ($this->parameters as $key => $value) {
			$result .= $key . '=' . $value . ',';
		}
		$result = substr($result, 0, -1);
		$result .= '}]';
		return $result;
	}
}
?>