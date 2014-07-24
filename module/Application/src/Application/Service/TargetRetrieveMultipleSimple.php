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
 * @version     SVN $Id: TargetRetrieveMultipleSimple.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\Command;
use Application\Service\FindByCriteria;
use Application\Service\RetrieveResponse;
use Doctrine\ORM\EntityManager;

/**
 * The base class for retrieving a collection of instances
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetRetrieveMultipleSimple.php 13 2013-08-05 22:53:55Z  $
 * @abstract
 */
abstract class TargetRetrieveMultipleSimple extends TargetRetrieveMultiple {
	const MSG_STATISTICS = '%s records retrieved in %s seconds';

	/**
	 * @var FindByCriteria
	 */
	private $criteria;

	/**
	 * @var string
	 */
	private $clazz;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct( $em );
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return FindByCriteria
	 */
	public function getCriteria() {
		return $this->criteria;
	}

	/**
	 * @param FindByCriteria $criteria
	 */
	public function setCriteria(FindByCriteria $criteria) {
		$this->criteria = $criteria;
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
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\RetrieveMultipleResponse
	 * @see \Application\Service\TargetRetrieveMultiple::retrieve()
	 */
	public function retrieve() {
		$response = new RetrieveMultipleResponse();
		$clazz = $this->getClazz();
		$criteria = $this->getCriteria();
		$em = $this->getEntityManager();
		
		if ($criteria == null) {
			$response->setMessage( 'No fetch criteria specified.' );
			return $response;
		}
		if (($clazz == null) || (!is_string( $clazz )) || ($clazz == '')) {
			$response->setMessage( 'No object class specified.' );
			return $response;
		}
		if ($em == null) {
			$response->setMessage( 'No entity manager provided.' );
			return $response;
		}
		
		// Execute fetch
		$start = microtime( true );
		$recordSet = $em->getRepository( $clazz )->findBy( $criteria->getCriteria(), $criteria->getOrderBy(), $criteria->getLimit(), $criteria->getOffset() );
		$end = microtime( true );
		
		// Prepare statistics
		$numRecords = count( $recordSet );
		$elapsedTime = $end - $start;
		
		// Prepare response
		if ($numRecords) {
			$response->setResult( true );
		}
		$response->setRecordSet( $recordSet );
		$response->setStatistics( sprintf( self::MSG_STATISTICS, $numRecords, $elapsedTime ) );
		return $response;
	}
}
?>