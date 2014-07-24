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
 * @package Application\Stdlib
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN $Id: Comparable.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Stdlib;

use Application\Stdlib\Object;

/**
 * This interface imposes a total ordering on the objects of each class that implements
 * it. This ordering is referred to as the class's natural ordering, and the class's
 * compareTo method is referred to as its natural comparison method.
 *
 * Lists (and arrays) of objects that implement this interface can be sorted automatically
 * by Collections.sort (and Arrays.sort). Objects that implement this interface can be
 * used as keys in a sorted map or as elements in a sorted set, without the need to
 * specify a comparator.
 *
 * The natural ordering for a class C is said to be consistent with equals if and only if
 * e1.compareTo(e2) == 0 has the same boolean value as e1.equals(e2) for every e1 and e2
 * of class C. Note that null is not an instance of any class, and e.compareTo(null)
 * should throw a NullPointerException even though e.equals(null) returns false.
 *
 * It is strongly recommended (though not required) that natural orderings be consistent
 * with equals. This is so because sorted sets (and sorted maps) without explicit
 * comparators behave "strangely" when they are used with elements (or keys) whose natural
 * ordering is inconsistent with equals. In particular, such a sorted set (or sorted map)
 * violates the general contract for set (or map), which is defined in terms of the equals
 * method.
 *
 * For example, if one adds two keys a and b such that (!a.equals(b) && a.compareTo(b) ==
 * 0) to a sorted set that does not use an explicit comparator, the second add operation
 * returns false (and the size of the sorted set does not increase) because a and b are
 * equivalent from the sorted set's perspective.
 *
 * Virtually all Java core classes that implement Comparable have natural orderings that
 * are consistent with equals. One exception is java.math.BigDecimal, whose natural
 * ordering equates BigDecimal objects with equal values and different precisions (such as
 * 4.0 and 4.00).
 *
 * For the mathematically inclined, the relation that defines the natural ordering on a
 * given class C is:
 * {(x, y) such that x.compareTo(y) <= 0}.
 *
 * The quotient for this total order is:
 * {(x, y) such that x.compareTo(y) == 0}.
 *
 * It follows immediately from the contract for compareTo that the quotient is an
 * equivalence relation on C, and that the natural ordering is a total order on C. When we
 * say that a class's natural ordering is consistent with equals, we mean that the
 * quotient for the natural ordering is the equivalence relation defined by the class's
 * equals(Object) method:
 * {(x, y) such that x.equals(y)}.
 *
 * @package		Application\Stdlib
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Comparable.php 13 2013-08-05 22:53:55Z  $
 */
interface Comparable {

	/**
	 * Compare this object with the specified object for order
	 * Returns a negative integer, zero, or a positive integer as this object is less
	 * than, equal to, or greater than the specified object.
	 *
	 * @param Object $value
	 * @throws NullPointerException
	 * 		If an argument is null and this comparator does not permit null arguments
	 * @throws ClassCastException
	 * 		If the arguments' types prevent them from being compared by this comparator.
	 * @return int
	 */
	public function compareTo(Object $value = null);
}
?>