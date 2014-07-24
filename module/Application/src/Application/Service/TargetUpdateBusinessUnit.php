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
 * @version     SVN $Id: TargetUpdateBusinessUnit.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\BusinessUnit;
use Application\Service\TargetUpdate;
use Application\Service\UpdateResponse;
use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;

/**
 * Contains the data needed to update a business unit.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetUpdateBusinessUnit.php 13 2013-08-05 22:53:55Z  $
 */
class TargetUpdateBusinessUnit extends TargetUpdate {

	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct( $em );
	}
	
	/* ---------- Methods ---------- */

	/**;
	 * @return \Application\Service\UpdateResponse
	 * @see \Application\Service\TargetUpdate::update()
	 */
	public function update() {
		$response = new UpdateResponse();
		
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
		
		if ($this->getEntityManager() == null || $this->getEntity() == null) {
			return $response;
		}
		
		try {
			$em = $this->getEntityManager();
			$entity = $this->prepareBusinessUnit( $entity );
			$em->persist( $entity );
			$em->flush();
			
			$response->setResult( true );
			$response->setMessage( 'Update successful.' );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * @param BusinessUnit $BusinessUnit
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
		$bu->setLastUpdateDate($now);
		
		return $bu;
	}
}
?>