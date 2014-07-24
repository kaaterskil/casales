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
 * @package     Application\Model
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: ContactSortField.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\StdLib\Enum;

/**
 * Specifies the sorting methods for a contact collection.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 */
class ContactSortField extends Enum {
	const FIRSTNAME = 'firstName,First Name';
	const LASTNAME = 'lastName,Last Name';
	const SUFFIX = 'suffix,Suffix';
	const DISPLAYNAME = 'displayName,Display Name';
	const NICKNAME = 'nickname,Nickname';
	const JOBTITLE = 'jobTitle,Job Title';

	private $displayName;

	public static function instance($name) {
		$clazz = get_called_class();
		$rc = new \ReflectionClass( $clazz );
		$constants = $rc->getConstants();
		
		$i = 0;
		foreach ( $constants as $constant ) {
			$split = preg_split( '/[,]+/', $constant );
			$constantName = trim($split[0]);
			$displayName = trim($split[1]);
			if (!isset( self::$singletons[$constantName] )) {
				if (!in_array( $constantName, self::$enumConstantDirectory )) {
					self::$enumConstantDirectory[$i] = $constantName;
				}
				self::$singletons[$constantName] = new $clazz( $constantName, $i++ );
				self::$singletons[$constantName]->setDisplayName( $displayName );
			}
		}
		return self::$singletons[$name];
	}

	public static function toArray() {
		$rc = new \ReflectionClass( get_called_class() );
		$values = $rc->getConstants();
		
		$result = array ();
		foreach ( $values as $value ) {
			$split = preg_split( '/[,]+/', $value );
			$constantName = $split[0];
			$displayName = $split[1];
			$result[$constantName] = $displayName;
		}
		return $result;
	}

	public function getDisplayName() {
		return $this->displayName;
	}

	protected function setDisplayName($displayName) {
		$this->displayName = $displayName;
	}
}
?>