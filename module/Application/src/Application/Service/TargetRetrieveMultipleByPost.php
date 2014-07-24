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
 * @version     SVN $Id: TargetRetrieveMultipleByPost.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\Command;
use Application\Service\Request;
use Application\Service\RetrieveResponse;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

/**
 * TargetRetrieveMultipleByPost Class
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetRetrieveMultipleByPost.php 13 2013-08-05 22:53:55Z  $
 */
class TargetRetrieveMultipleByPost extends TargetRetrieveMultiple {

	/**
	 * @var string
	 */
	protected $alias = 'o';

	/**
	 * @var string
	 */
	protected $clazz;

	/**
	 * @var array
	 */
	protected $orderBy = array();

	/**
	 * @var array
	 */
	private $params = array();
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct( $em );
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return string
	 */
	public function getAlias() {
		return $this->alias;
	}

	/**
	 * @param string $alias
	 */
	public function setAlias($alias) {
		$this->alias = (string) $alias;
	}

	/**
	 * @return string
	 */
	public function getClazz() {
		return $this->clazz;
	}

	/**
	 * @param string $clazz
	 */
	public function setClazz($clazz) {
		$this->clazz = (string) $clazz;
	}

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager() {
		return $this->em;
	}

	/**
	 * @param EntityManager $em
	 */
	public function setEntityManager(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return array
	 */
	public function getOrderBy() {
		return $this->orderBy;
	}

	/**
	 * @param array $orderBy
	 */
	public function setOrderBy(array $orderBy = array()) {
		$this->orderBy = $orderBy;
	}

	/**
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * @param array $params
	 */
	public function setParams(array $params = array()) {
		$this->params = $params;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\RetrieveMultipleResponse
	 * @see \Application\Service\TargetRetrieveMultiple::retrieve()
	 */
	public function retrieve() {
		/* @var $q Query */
		$response = new RetrieveMultipleResponse();
		
		if ($this->getEntityManager() == null) {
			$response->setMessage( 'EntityManager not found.' );
			return $response;
		}
		if (empty( $this->clazz )) {
			$response->setMessage( 'No FQCN object class name supplied.' );
			return $response;
		}
		if (count( $this->params ) == 0) {
			$response->setMessage( 'No query parameters supplied.' );
			return $response;
		}
		
		try {
			$em = $this->getEntityManager();
			$q = $em->createQuery( $this->buildDQL() );
			$recordSet = $q->getResult();
			
			$response->setRecordSet( $recordSet );
			$response->setMessage( 'Query successful.' );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		return $response;
	}

	/**
	 * @return string
	 */
	protected function buildDQL() {
		$result = 'select ' .
			 $this->alias .
			 ' from ' .
			 $this->clazz .
			 ' ' .
			 $this->alias .
			 ' where ';
		
		foreach ( $this->params as $key => $value ) {
			if ($value) {
				if (is_numeric( $value )) {
					$result .= $this->alias . '.' . $key . ' = ' . $value . ', ';
				} else {
					$result .= 'lower(' .
						 $this->alias .
						 '.' .
						 $key .
						 ") like lower('%" .
						 $value .
						 "%'), ";
				}
			}
		}
		$result = substr( $result, 0, -2 );
		
		if (count( $this->orderBy )) {
			$result .= ' order by ';
			foreach ( $this->orderBy as $key => $value ) {
				$result .= $this->alias . '.' . $key . ' ' . $value . ', ';
			}
			$result = substr( $result, 0, -2 );
		}
		
		return $result;
	}
}
?>