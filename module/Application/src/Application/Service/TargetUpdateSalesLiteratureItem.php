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
 * @version     SVN $Id: TargetUpdateSalesLiteratureItem.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\SalesLiteratureItem;
use Application\Service\TargetUpdate;
use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;

/**
 * Contains the data needed to update a sales literature item (document).
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetUpdateSalesLiteratureItem.php 13 2013-08-05 22:53:55Z  $
 */
class TargetUpdateSalesLiteratureItem extends TargetUpdate {

	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct( $em );
	}
	
	/* ---------- Methods ---------- */

	/**
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
		
		try {
			$em = $this->getEntityManager();
			$entity = $this->prepareSalesLiteratureItem( $entity );
			$em->persist( $entity );
			$em->flush();

			$response->setMessage('Update successful.');
			$response->setResult( true );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * @param SalesLiteratureItem $item
	 * @return SalesLiteratureItem
	 */
	private function prepareSalesLiteratureItem(SalesLiteratureItem $item) {
		$user = $this->getUser();
		$now = new \DateTime();
		
		
		// Set user characteristics
		if ($item->getSalesLiterature()->getOwner() != null) {
			$org = $item->getSalesLiterature()->getOwner()->getBusinessUnit()->getOrganization();
		} else {
			$item->setOwner( $user );
			$org = $user->getBusinessUnit()->getOrganization();
		}
		$item->setOrganization($org);
		
		// Set modification dates
		$item->setLastUpdateDate( $now );
		
		return $item;
	}
}
?>