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
 * @version     SVN $Id: FindByCriteria.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

/**
 * FindByCriteria Class
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: FindByCriteria.php 13 2013-08-05 22:53:55Z  $
 */
class FindByCriteria {
	/**
	 * @var array
	 */
	private $criteria = array();

	/**
	 * @var array
	 */
	private $orderBy = null;

	/**
	 * @var int
	 */
	private $limit = null;

	/**
	 * @var int
	 */
	private $offset = null;
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return array
	 */
	public function getCriteria() {
		return $this->criteria;
	}
	
	/**
	 * @param array $criteria
	 */
	public function setCriteria(array $criteria) {
		$this->criteria = $criteria;
	}
	
	/**
	 * @return array|null
	 */
	public function getOrderBy() {
		return $this->orderBy;
	}
	
	/**
	 * @param array|null $orderBy
	 */
	public function setOrderBy(array $orderBy = null) {
		$this->orderBy = $orderBy;
	}
	
	/**
	 * @return int|null
	 */
	public function getLimit() {
		return $this->limit;
	}
	
	/**
	 * @param int|null $limit
	 */
	public function setLimit($limit = null) {
		if($limit != null) {
			$limit = (int) $limit;
		}
		$this->limit = $limit;
	}
	
	/**
	 * @return int|null
	 */
	public function getOffset() {
		return $this->offset;
	}
	
	/**
	 * @param int|null $offset
	 */
	public function setOffset($offset = null) {
		if($offset != null) {
			$offset = (int) $offset;
		}
		$this->offset = $offset;
	}
}
?>