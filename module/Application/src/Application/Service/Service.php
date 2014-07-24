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
 * @version     SVN $Id: Service.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\User;
use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\LoseOpportunityRequest;
use Application\Service\QualifyLeadRequest;
use Application\Service\Request;
use Application\Service\Response;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;
use Application\Service\WinOpportunityRequest;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service Class
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Service.php 13 2013-08-05 22:53:55Z  $
 */
class Service {

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var ServiceLocatorInterface
	 */
	private $sm;

	/**
	 * The current user
	 * @var User
	 */
	private $user;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
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
	public function setEntityManager(EntityManager $em = null) {
		$this->em = $em;
	}

	/**
	 * @return ServiceLocatorInterface
	 */
	public function getServiceManager() {
		return $this->sm;
	}

	public function setServiceManager(ServiceLocatorInterface $sm) {
		$this->sm = $sm;
	}

	/**
	 * @return \Application\Model\User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user = null) {
		$this->user = $user;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * Creates a business entity instance.
	 *
	 * @param CreateRequest $request
	 * @return CreateResponse
	 */
	public function create(CreateRequest $createRequest) {
		$this->inject( $createRequest );
		return $createRequest->create();
	}

	/**
	 * Deletes a business entity instance.
	 *
	 * @param DeleteRequest $request
	 * @return DeleteResponse
	 */
	public function delete(DeleteRequest $deleteRequest) {
		$this->inject( $deleteRequest );
		return $deleteRequest->delete();
	}

	/**
	 * Deletes the entity specified by the given id and FQCN
	 *
	 * @param int $id
	 * 		The id of the specified entity
	 * @param string $clazz
	 * 		The FQCN of the specified entity
	 * @return boolean
	 */
	public function deleteById($id, $clazz) {
		if (!is_string( $clazz )) {
			return false;
		}
		if (!is_int( $id ) || $id < 1) {
			return false;
		}
		
		$em = $this->getEntityManager();
		$entity = $em->getRepository( $clazz )->find( $id );
		if ($entity) {
			$em->remove( $entity );
			$em->flush();
			return true;
		}
		return false;
	}

	/**
	 * Invokes the given command's execute method
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function execute(Request $request) {
		$this->inject( $request );
		return $request->execute();
	}

	/**
	 * Retrieves a business entity instance with the specified ID.
	 *
	 * @param RetrieveRequest $request
	 * @return RetrieveResponse
	 */
	public function retrieve(RetrieveRequest $retrieveRequest) {
		$this->inject( $retrieveRequest );
		return $retrieveRequest->retrieve();
	}

	/**
	 * Retrieves the specified entity
	 *
	 * @param int $id
	 * 		The id of the specified entity
	 * @param string $clazz
	 * 		The FQCN of the specified entity
	 * @return boolean|Entity
	 */
	public function retrieveById($id, $clazz) {
		if (!is_string( $clazz )) {
			return false;
		}
		if (!is_int( $id ) || $id < 1) {
			return false;
		}
		
		$em = $this->getEntityManager();
		$entity = $em->getRepository( $clazz )->find( $id );
		if ($entity == null) {
			return false;
		}
		return $entity;
	}

	/**
	 * Retrieves a collection of business entity instances of a specified type based on query criteria.
	 *
	 * @param RetrieveMultipleRequest $request
	 * @return RetrieveMultipleResponse
	 */
	public function retrieveMultiple(
			RetrieveMultipleRequest $retrieveMultipleRequest) {
		$this->inject( $retrieveMultipleRequest );
		return $retrieveMultipleRequest->retrieve();
	}

	/**
	 * Updates an instance of an entity.
	 *
	 * @param UpdateRequest $request
	 * @return UpdateResponse
	 */
	public function update(UpdateRequest $updateRequest) {
		$this->inject( $updateRequest );
		return $updateRequest->update();
	}

	/**
	 * Injects the entity manager, current user and smtp mail transport into the given request as necessary
	 *
	 * @param Request $request
	 */
	private function inject(Request $request) {
		// Inject the ServiceLocator into the request, if needed
		if (method_exists( $request, 'setServiceLocator' )) {
			$request->setServiceLocator( $this->getServiceManager() );
		}
		
		// Inject the EntityManager into the request, if needed
		if (method_exists( $request, 'getTarget' )) {
			$obj = $request->getTarget();
			if (method_exists( $obj, 'setEntityManager' )) {
				$obj->setEntityManager( $this->em );
			}
			if (method_exists( $obj, 'setUser' )) {
				$obj->setUser( $this->user );
			}
		} elseif (method_exists( $request, 'setEntityManager' )) {
			$request->setEntityManager( $this->em );
		}
		
		// Inject the User into the request, if needed
		if (method_exists( $request, 'setUser' )) {
			$request->setUser( $this->user );
		}
		
		// Inject the SMTP mail transport into the request, if needed
		if (($request instanceof SendEmailRequest) ||
			 ($request instanceof DistributeCampaignActivityRequest)) {
			$transport = $this->getServiceManager()->get( 'mail.transport' );
			$request->setTransport( $transport );
		}
	}
}
?>