<?php
/**
 * Casales Library
 *
 * @category	Casales
 * @package		Casales_
 * @author		Blair
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: OrderResponse.php 13 2013-08-05 22:53:55Z  $
 */

namespace Application\Service;

use Application\Service\Response;

/**
 * OrderResponse Class
 *
 * @package		package
 * @author		Blair
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: OrderResponse.php 13 2013-08-05 22:53:55Z  $
 */
class OrderResponse implements Response {
	
	/**
	 * @var array
	 */
	private $collection = array();
	
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
	 * @return array:
	 */
	public function getCollection() {
		return $this->collection;
	}
	
	/**
	 * @param array $collection
	 */
	public function setCollection(array $collection = array()) {
		$this->collection = $collection;
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