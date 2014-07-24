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
 * @package     Application\Stdlib
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: Enum.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\StdLib;

use Application\Stdlib\Exception\ClassCastException;
use Application\Stdlib\Exception\NullPointerException;
use Application\Stdlib\Comparable;
use Application\Stdlib\Object;

/**
 * This is the common base class of all enumeration types.
 *
 * More information about enums, including descriptions of the implicitly declared methods
 * synthesized by the compiler, can be found in section 8.9 of <cite>The Java&trade; Language
 * Specification</cite>.
 *
 * <p> Note that when using an enumeration type as the type of a set or as the type of the keys in a
 * map, specialized and efficient {@linkplain java.util.EnumSet set} and {@linkplain
 * java.util.EnumMap map} implementations are available.
 *
 * @package		Application\Stdlib
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Enum.php 13 2013-08-05 22:53:55Z  $
 */
abstract class Enum implements Comparable {

	/**
	 * An associative array containing the textual names of each of the declared constant values
	 *
	 * @var array
	 */
	protected static $enumConstantDirectory = array();

	/**
	 * An array of singletons in which the key is the textual name and the value is the singleton
	 * instance
	 *
	 * @var array
	 */
	protected static $singletons = array();

	/**
	 * The name of this enum constant, as declared in the enum declaration.
	 * Most programmers should use the {@link #toString} method rather than accessing this field.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The ordinal of this enumeration constant (its position in the enum declaration, where the
	 * initial constant is assigned an ordinal of zero).
	 *
	 * Most programmers will have no use for this field. It is designed for use by sophisticated
	 * enum-based data structures.
	 *
	 * @var int
	 */
	private $ordinal;

	/**
	 * Returns a singleton instance of the enum with the given name
	 *
	 * @return \Application\StdLib\Enum
	 */
	public static function instance($name) {
		$rc = new \ReflectionClass( get_called_class() );
		$constants = $rc->getConstants();
		
		$i = 0;
		foreach ( $constants as $constant ) {
			if (!isset( self::$singletons[$constant] )) {
				// Set the type directory
				if (!in_array( $constant, self::$enumConstantDirectory )) {
					self::$enumConstantDirectory[$i] = $constant;
				}
				
				// Create the singleton instance
				$clazz = get_called_class();
				self::$singletons[$constant] = new $clazz( $constant, $i++ );
			}
		}
		return self::$singletons[$name];
	}

	/**
	 * Returns an associative array with the enum values as a list of key => value pairs.
	 *
	 * @return array:
	 */
	public static function toArray() {
		$rc = new \ReflectionClass( get_called_class() );
		$values = $rc->getConstants();
		
		$result = array();
		foreach ( $values as $key => $value ) {
			$result[$value] = $value;
		}
		return $result;
	}

	/**
	 * Returns the enum constant of the specified enum type with the specified name.
	 * The name must match exactly an identifier used to declare an enum constant in this type.
	 * (Extraneous whitespace characters are not permitted.
	 *
	 * @param string $enumType
	 * @param string $name
	 * @throws \InvalidArgumentException
	 * @throws \NullPointerException
	 * @return mixed
	 */
	public static function valueOf($enumType, $name) {
		$rc = new \ReflectionClass( $enumType );
		$result = in_array( $name, $rc->getStaticPropertyValue( 'enumConstantDirectory' ) );
		if ($result) {
			return self::$singletons[$name];
		}
		if ($name == null) {
			throw new NullPointerException( "Name is null." );
		}
		throw new \InvalidArgumentException( "No enum constant " . $enumType . "." . $name );
	}

	/**
	 * Sole constructor.
	 * Programmers should not invoke this constructor.
	 *
	 * @param string $name
	 * @param int $ordinal
	 */
	protected function __construct($name, $ordinal) {
		$this->name = $name;
		$this->ordinal = (int) $ordinal;
	}

	/**
	 * Returns the name of this enum constant, exactly as declared in its enum declaration.
	 *
	 * <b>Most programmers should use the {@link #toString} method in preference to this one, as the
	 * toString method may return a more user-friendly name.</b> This method is designed primarily
	 * for use in specialized situations where correctness depends on getting the exact name, which
	 * will not vary from release to release.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the ordinal of this enumeration constant (its position
	 * in its enum declaration, where the initial constant is assigned
	 * an ordinal of zero).
	 *
	 * @return int
	 */
	public function getOrdinal() {
		return $this->ordinal;
	}

	/**
	 * Returns true if the specified object is equal to this enum constant.
	 *
	 * @param Enum $other
	 * @return boolean
	 */
	public function equals(Enum $other) {
		return $this === $other;
	}

	/**
	 * Compares this enum with the specified object for order.
	 *
	 * Returns a negative integer, zero, or a positive integer as this object is less than, equal
	 * to, or greater than the specified object.
	 *
	 * Enum constants are only comparable to other enum constants of the same enum type. The natural
	 * order implemented by this method is the order in which the constants are declared.
	 *
	 * @param Enum $o
	 * @throws NullPointerException
	 * @throws ClassCastException
	 * @return int
	 */
	public function compareTo(Object $o = null) {
		$other = $o;
		$self = $this;
		if($other == null) {
			throw new NullPointerException();
		}
		if (get_class( $other ) != get_class( $self )) {
			throw new ClassCastException();
		}
		return $self->getOrdinal() - $other->getOrdinal();
	}

	/**
	 * Returns the name of this enum constant, as contained in the declaration.
	 * This method may be overridden, though it typically isn't necessary or desirable. An enum type
	 * should override this method when a more "programmer-friendly" string form exists.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}
}
?>