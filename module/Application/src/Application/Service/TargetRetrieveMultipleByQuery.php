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
 * @version     SVN $Id: TargetRetrieveMultipleByQuery.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\Command;
use Application\Service\Request;
use Application\Service\RetrieveResponse;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

/**
 * TargetRetrieveMultipleByQuery Class
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetRetrieveMultipleByQuery.php 13 2013-08-05 22:53:55Z  $
 */
class TargetRetrieveMultipleByQuery extends TargetRetrieveMultiple {

	/**
	 * @var string
	 */
	private $dql;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct($em);
	}
	
	/* ---------- Getter/Setters ---------- */
	
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
	 * @return string
	 */
	public function getDQL() {
		return $this->dql;
	}

	/**
	 * @param string $dql
	 */
	public function setDQL($dql) {
		$this->dql = (string) $dql;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\RetrieveMultipleResponse
	 * @see \Application\Service\TargetRetrieveMultiple::retrieve()
	 */
	public function retrieve() {
		/* @var $q Query */
		
		$response = new RetrieveMultipleResponse();
		
		if ($this->getDQL() == null || $this->getDQL() == '') {
			return $response;
		}

		$em = $this->getEntityManager();
		$q = $em->createQuery($this->getDQL());
		$recordSet = $q->getResult();
		
		$response->setRecordSet( $recordSet );
		return $response;
	}
}
?>