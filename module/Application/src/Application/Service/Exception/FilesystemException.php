<?php
/**
 * Casales Library
 *
 * @category	Casales
 * @package		Casales_
 * @author		Blair
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: FilesystemException.php 13 2013-08-05 22:53:55Z  $
 */

namespace Application\Service\Exception;

/**
 * FilesystemException Class
 *
 * @package		package
 * @author		Blair
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: FilesystemException.php 13 2013-08-05 22:53:55Z  $
 */
class FilesystemException extends \RuntimeException {
	
	/**
	 * Constructor
	 *
	 * @param string $message
	 */
	public function __construct($message) {
		parent::__construct($message);
	}
}
?>