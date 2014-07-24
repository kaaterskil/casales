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
 * @version     SVN $Id: CreateResponse.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\Response;

/**
 * Contains the response from the Create message.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: CreateResponse.php 13 2013-08-05 22:53:55Z  $
 */
class CreateResponse implements Response {
	/**
	 * @var int
	 */
	private $id = 0;
	
	/**
	 * @var string
	 */
	private $message;
	
	/**
	 * @var boolean
	 */
	private $result = false;
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * Gets the ID of the newly created entity instance.
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Sets the ID of the newly created entity instance.
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = (int) $id;
	}
	
	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}
	
	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = (string) $message;
	}
	
	/**
	 * @return boolean
	 */
	public function getResult() {
		return $this->result;
	}
	
	/**
	 * @param boolean $result
	 */
	public function setResult($result) {
		$this->result = (bool) $result;
	}
}
?>