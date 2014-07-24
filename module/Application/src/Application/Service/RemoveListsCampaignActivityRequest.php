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
 * @version     SVN $Id: RemoveListsCampaignActivityRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\CampaignActivity;
use Application\Service\RemoveListsCampaignActivityResponse;
use Application\Service\Request;
use Doctrine\ORM\EntityManager;

/**
 * RemoveListsCampaignActivityRequest Class
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: RemoveListsCampaignActivityRequest.php 13 2013-08-05 22:53:55Z  $
 */
class RemoveListsCampaignActivityRequest extends Request {

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var CampaignActivity
	 */
	private $entity = null;

	/**
	 * @var array
	 */
	private $listIds = array();
	
	/* ---------- Constructor ---------- */
	
	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct( 'RemoveListsCampaignActivityRequest' );
		$this->em = $em;
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
	 * @return CampaignActivity
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * @param CampaignActivity $entity
	 */
	public function setEntity(CampaignActivity $entity) {
		$this->entity = $entity;
	}

	/**
	 * @return array
	 */
	public function getListIds() {
		return $this->listIds;
	}

	/**
	 * @param array $memberIds
	 */
	public function setListIds(array $listIds = array()) {
		$this->listIds = $listIds;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\AddListMembersListResponse
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		$response = new RemoveListsCampaignActivityResponse();
		
		if ($this->getEntityManager() == null) {
			$response->setMessage( 'No EntityManager found.' );
			return $response;
		}
		
		$entity = $this->getEntity();
		if ($entity == null) {
			$response->setMessage( 'No record found to update.' );
			return $response;
		} elseif ($entity->getId() == null || $entity->getId() < 1) {
			$response->setMessage( 'Cannot update a new unmanaged record.' );
			return $response;
		}
		
		try {
			$start = microtime( true );
			$em = $this->getEntityManager();
			
			foreach ($this->getListIds() as $id){
				$list = $em->getRepository('Application\Model\MarketingList')->find($id);
				$this->entity->removeList($list);
			}
			
			$em->persist( $this->entity );
			$em->flush();
			$end = microtime( true );
			
			$elapsedTime = $end - $start;
			$numRecords = count( $this->getListIds() );
			
			$response->setResult( true );
			$response->setMessage( sprintf( '%s records removed in %s seconds', $numRecords, $elapsedTime ) );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		return $response;
	}
}
?>