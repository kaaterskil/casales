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
 * @version     SVN $Id: RemoveMemberListRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\MarketingList;
use Application\Service\Request;
use Application\Model\MemberType;
use Doctrine\ORM\EntityManager;

/**
 * Contains the data that is needed to remove a member from a list (marketing list).
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: RemoveMemberListRequest.php 13 2013-08-05 22:53:55Z  $
 */
class RemoveMemberListRequest extends Request {

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var MarketingList
	 */
	private $entity = null;

	/**
	 * @var array
	 */
	private $memberIds = array();
	
	/* ---------- Constructor ---------- */
	
	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct( 'AddListMembersListRequest' );
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
	 * @return MarketingList
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * @param MarketingList $entity
	 */
	public function setEntity(MarketingList $entity) {
		$this->entity = $entity;
	}

	/**
	 * @return array:
	 */
	public function getMemberIds() {
		return $this->memberIds;
	}

	/**
	 * @param array $memberIds
	 */
	public function setMemberIds(array $memberIds = array()) {
		$this->memberIds = $memberIds;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\AddListMembersListResponse
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		$response = new AddListMembersListResponse();
		
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
			
			$memberType = $entity->getMemberType();
			switch ($memberType) {
				case MemberType::ACCOUNT :
					foreach ( $this->getMemberIds() as $id ) {
						$account = $em->getRepository( 'Application\Model\Account' )->find( $id );
						$this->entity->removeAccount($account);
					}
					break;
				case MemberType::CONTACT :
					foreach ( $this->getMemberIds() as $id ) {
						$contact = $em->getRepository( 'Application\Model\Contact' )->find( $id );
						$this->entity->removeContact($contact);
					}
					break;
				case MemberType::LEAD :
					foreach ( $this->getMemberIds() as $id ) {
						$lead = $em->getRepository( 'Application\Model\Lead' )->find( $id );
						$this->entity->removeLead($lead);
					}
					break;
			}
			
			$em->persist( $this->entity );
			$em->flush();
			$end = microtime( true );
			
			$elapsedTime = $end - $start;
			$numRecords = count( $this->getMemberIds() );
			
			$response->setResult( true );
			$response->setMessage( sprintf( '%s records retrieved in %s seconds', $numRecords, $elapsedTime ) );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		return $response;
	}
}
?>