<?php

/**
 * Casales Library
 * PHP version 5.4
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
 * @category Casales
 * @package Application\Service
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN $Id: OrderRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\OrderResponse;
use Application\Service\OrderType;
use Application\Stdlib\Comparator;
use Application\Stdlib\Object;
use Application\Stdlib\Exception\NullPointerException;
use Application\Stdlib\Exception\ClassCastException;

/**
 * OrderRequest Class
 *
 * @package Application\Stdlib
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version $Id: OrderRequest.php 13 2013-08-05 22:53:55Z  $
 * @abstract
 *
 */
abstract class OrderRequest implements Comparator {

	/**
	 *
	 * @var array
	 */
	protected $collection = array();

	/**
	 *
	 * @var OrderType
	 */
	protected $orderType;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct(array $collection) {
		$this->collection = $collection;
		$this->orderType = OrderType::instance( OrderType::ASC );
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 *
	 * @return array
	 */
	public function getCollection() {
		return $this->collection;
	}

	/**
	 *
	 * @param array $attribute
	 */
	public function setCollection(array $collection) {
		$this->collection = $collection;
	}

	/**
	 *
	 * @return OrderType
	 */
	public function getOrderType() {
		return $this->orderType;
	}

	/**
	 *
	 * @param string|OrderType $orderType
	 */
	public function setOrderType($orderType = null) {
		if ($orderType instanceof OrderType) {
			$this->orderType = $orderType;
		} elseif ($orderType != null) {
			$this->orderType = OrderType::instance( $orderType );
		} else {
			$this->orderType = OrderType::instance( OrderType::ASC );
		}
	}
	
	/* ---------- Methods ---------- */
	/**
	 * Executes the sort
	 *
	 * @return OrderResponse
	 */
	public function execute() {
		$response = new OrderResponse();
		
		try {
			$result = usort( $this->collection, array(
				$this,
				'compare'
			) );
			$response->setResult( $result );
			$response->setCollection( $this->collection );
		} catch ( \RuntimeException $e ) {
			$response->setResult( 'Sort Error: ' . $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * Compares its two arguments for order. Returns a negative integer, zero, or a
	 * positive integer as the first argument is less than, equal to, or greater than the
	 * second.
	 *
	 * @param Object $o1
	 * @param Object $o2
	 * @throws NullPointerException
	 * @throws ClassCastException
	 * @return int
	 * @see \Application\Stdlib\Comparator::compare()
	 */
	public function compare(Object $o1 = null, Object $o2 = null) {
		// Test for null values
		if (($o1 == null) || ($o2 == null)) {
			throw new NullPointerException( "Cannot compare a null object." );
		}
		
		// Test for class
		if ((!$this->isExtends( $o1, $o2 ))) {
			throw new ClassCastException( "Cannot compare objects of incompatible types." );
		}
		
		$t1 = $this->getComparableValue( $o1 );
		$t2 = $this->getComparableValue( $o2 );
		if ($t1 == $t2) {
			return 0;
		} else {
			if ($this->getOrderType()->getName() == OrderType::ASC) {
				if (is_numeric( $t1 )) {
					return ($t1 - $t2 > 0 ? 1 : -1);
				} else {
					return ($t1 > $t2 ? 1 : -1);
				}
			} else {
				if (is_numeric( $t1 )) {
					return ($t1 - $t2 > 0 ? -1 : 1);
				} else {
					return ($t1 > $t2 ? -1 : 1);
				}
			}
		}
	}

	/**
	 * Test if a class extends or implements a specific class/interface
	 *
	 * @param string $search The class or interface name to test
	 * @param \ReflectionClass $rc The ReflectionClass object to test against
	 * @return boolean
	 */
	public function isExtends(Object $testObject, Object $controlObject) {
		$rc1 = new \ReflectionClass( $testObject );
		
		if ($rc1 === false) {
			return false;
		}
		
		do {
			$clazz1 = $rc1->getName();
			$rc2 = new \ReflectionClass( $controlObject );
			do {
				$clazz2 = $rc2->getName();
				if ($clazz1 instanceof $clazz2) {
					return true;
				}
				if ($rc1->isSubclassOf( $clazz2 )) {
					return true;
				}
				$interfaces = $rc2->getInterfaceNames();
				if (is_array( $interfaces )) {
					foreach ( array_keys( $interfaces, 'Application\Stdlib\Entity' ) as $key ) {
						unset( $interfaces[$key] );
					}
					foreach ( array_keys( $interfaces, 'Application\Stdlib\Object' ) as $key ) {
						unset( $interfaces[$key] );
					}
				}
				foreach ( $interfaces as $interface ) {
					if ($rc1->implementsInterface( $interface )) {
						return true;
					}
				}
				$rc2 = $rc2->getParentClass();
			} while ( $rc2 !== false );
			$rc1 = $rc1->getParentClass();
		} while ( $rc1 !== false );
		
		return false;
	}

	/**
	 * Returns the property value to be compared
	 *
	 * @param Object $o
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	protected abstract function getComparableValue(Object $o);
}
?>