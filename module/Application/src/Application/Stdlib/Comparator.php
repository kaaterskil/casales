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
 * @version SVN $Id: Comparator.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Stdlib;

use Application\Stdlib\Comparable;
use Application\Stdlib\Object;

/**
 * A comparison function, which imposes a total ordering on some collection of objects.
 * Comparators can be passed to a sort method (such as Collections.sort or Arrays.sort) to
 * allow precise control over the sort order. Comparators can also be used to control the
 * order of certain data structures (such as sorted sets or sorted maps), or to provide an
 * ordering for collections of objects that don't have a natural ordering.
 *
 * The ordering imposed by a comparator c on a set of elements S is said to be consistent
 * with equals if and only if c.compare(e1, e2)==0 has the same boolean value as
 * e1.equals(e2) for every e1 and e2 in S.
 *
 * Caution should be exercised when using a comparator capable of imposing an ordering
 * inconsistent with equals to order a sorted set (or sorted map). Suppose a sorted set
 * (or sorted map) with an explicit comparator c is used with elements (or keys) drawn
 * from a set S. If the ordering imposed by c on S is inconsistent with equals, the sorted
 * set (or sorted map) will behave "strangely." In particular the sorted set (or sorted
 * map) will violate the general contract for set (or map), which is defined in terms of
 * equals.
 *
 * For example, suppose one adds two elements a and b such that (a.equals(b) &&
 * c.compare(a, b) != 0) to an empty TreeSet with comparator c. The second add operation
 * will return true (and the size of the tree set will increase) because a and b are not
 * equivalent from the tree set's perspective, even though this is contrary to the
 * specification of the Set.add method.
 *
 * Note: It is generally a good idea for comparators to also implement
 * java.io.Serializable, as they may be used as ordering methods in serializable data
 * structures (like TreeSet, TreeMap). In order for the data structure to serialize
 * successfully, the comparator (if provided) must implement Serializable.
 *
 * For the mathematically inclined, the relation that defines the imposed ordering that a
 * given comparator c imposes on a given set of objects S is:
 * {(x, y) such that c.compare(x, y) <= 0}.
 *
 * The quotient for this total order is:
 * {(x, y) such that c.compare(x, y) == 0}.
 *
 * It follows immediately from the contract for compare that the quotient is an
 * equivalence relation on S, and that the imposed ordering is a total order on S. When we
 * say that the ordering imposed by c on S is consistent with equals, we mean that the
 * quotient for the ordering is the equivalence relation defined by the objects'
 * equals(Object) method(s):
 * {(x, y) such that x.equals(y)}.
 *
 * Unlike Comparable, a comparator may optionally permit comparison of null arguments,
 * while maintaining the requirements for an equivalence relation.
 * @author Java
 */
interface Comparator {

	/**
	 * Compares its two arguments for order
	 * Returns a negative integer, zero, or a positive integer as the first argument is
	 * less than, equal to, or greater than the second.
	 *
	 * @param Object $object1
	 * @param Object $object2
	 * @throws NullPointerException
	 * 		If an argument is null and this comparator does not permit null arguments
	 * @throws ClassCastException
	 * 		If the arguments' types prevent them from being compared by this comparator.
	 * @return int
	 */
	public function compare(Object $object1 = null, Object $object2 = null);
}
?>