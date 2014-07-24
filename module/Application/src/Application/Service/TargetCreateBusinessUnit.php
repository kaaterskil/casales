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
 * @version     SVN $Id: TargetCreateBusinessUnit.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\BusinessUnit;
use Application\Service\CreateResponse;
use Application\Service\TargetCreate;
use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;

/**
 * Contains the data needed to create a business unit.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetCreateBusinessUnit.php 13 2013-08-05 22:53:55Z  $
 */
class TargetCreateBusinessUnit extends TargetCreate {
	
	/**
	 * Constructor
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct($em);
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\CreateResponse
	 * @see \Application\Service\TargetCreate::create()
	 */
	public function create() {
		$response = new CreateResponse();
		
		if ($this->getEntityManager() == null) {
			$response->setMessage( 'No EntityManager found.' );
			return $response;
		}
		
		$entity = $this->getEntity();
		if ($entity == null) {
			$response->setMessage( 'No record found to save.' );
			return $response;
		}
		if ($entity->getId() != null && $entity->getId() > 0) {
			$response->setMessage( 'Cannot create an already existing record.' );
			return $response;
		}
		
		try {
			$em = $this->getEntityManager();
			$entity = $this->prepareBusinessUnit( $entity );
			$em->persist( $entity );
			$em->flush();

			$response->setResult(true);
			$response->setId( $entity->getId() );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}
	
	/**
	 * Prepares the entity prior to persistence
	 * @param BusinessUnit $bu
	 * @return BusinessUnit
	 */
	private function prepareBusinessUnit(BusinessUnit $bu) {
		/* @var $em EntityManager */
		
		$em = $this->getEntityManager();
		$now = new \DateTime();
		
		// Persist One-to-Many associations
		if($bu->getParentBusinessUnit() != null) {
			$em->persist($bu->getParentBusinessUnit());
		}
		
		// Set creation and modification dates
		if($bu->getId() == null || $bu->getId() == 0) {
			$bu->setCreationDate($now);
		}
		$bu->setLastUpdateDate($now);
		
		return $bu;
	}
}
?>