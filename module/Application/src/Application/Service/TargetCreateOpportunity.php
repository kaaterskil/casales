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
 * @version     SVN $Id: TargetCreateOpportunity.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\CreateResponse;
use Application\Service\TargetCreate;
use Application\Model\Opportunity;
use Application\Model\OpportunityStatus;
use Application\Model\OpportunityState;

/**
 * Contains the data needed to create an opportunity.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetCreateOpportunity.php 13 2013-08-05 22:53:55Z  $
 */
class TargetCreateOpportunity extends TargetCreate {

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
	 * @return \Application\Service\CreateResponse
	 * @see \Application\Service\TargetCreate::create()
	 */
	public function create() {
		$response = new CreateResponse();
		
		if ($this->getEntityManager() == null) {
			$response->setMessage('No EntityManager found.');
			return $response;
		}
		
		$entity = $this->getEntity();
		if($entity == null) {
			$response->setMessage( 'No record found to save.' );
			return $response;
		}
		if ($entity->getId() != null && $entity->getId() > 0) {
			$response->setMessage( 'Cannot create an already existing record.' );
			return $response;
		}
			
		try {
			$em = $this->getEntityManager();
			$entity = $this->prepareOpportunity( $entity );
			$em->persist( $entity );
			$em->flush();
			
			$response->setMessage('Opportunity successfully created.');
			$response->setResult(true);
			$response->setId( $entity->getId() );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}
	
	private function prepareOpportunity(Opportunity $opportunity) {
		$em = $this->getEntityManager();
		$user = $this->getUser();
		$now = new \DateTime();
		
		// Set One-To-Many associations
		if($opportunity->getAccount() != null) {
			$em->persist($opportunity->getAccount());
		}
		if($opportunity->getContact() != null) {
			$em->persist($opportunity->getContact());
		}
		if($opportunity->getOriginatingLead() != null) {
			$em->persist($opportunity->getOriginatingLead());
		}
		
		// Map status code to state code
		switch ($opportunity->getStatus()->getName()) {
			case OpportunityStatus::CANCELED :
				$opportunity->setState(OpportunityState::LOST);
				break;
			case OpportunityStatus::INPROGRESS :
				$opportunity->setState(OpportunityState::OPEN);
				break;
			case OpportunityStatus::NEWOPPORTUNITY :
				$opportunity->setState(OpportunityState::OPEN);
				break;
			case OpportunityStatus::NOTINTERESTED :
				$opportunity->setState(OpportunityState::LOST);
				break;
			case OpportunityStatus::ONHOLD :
				$opportunity->setState(OpportunityState::OPEN);
				break;
			case OpportunityStatus::WON :
				$opportunity->setState(OpportunityState::WON);
				break;
			default:
				$opportunity->setState(OpportunityState::OPEN);
		}
		
		// Set user characteristics
		if($opportunity->getOwner() != null) {
			$opportunity->setBusinessUnit($opportunity->getOwner()->getBusinessUnit());
		} else {
			$opportunity->setOwner($user);
			$opportunity->setBusinessUnit($user->getBusinessUnit());
		}
		
		// Set creation and modification dates
		if ($opportunity->getId() == null || $opportunity->getId() == 0) {
			$opportunity->setCreationDate( $now );
		}
		$opportunity->setLastUpdateDate( $now );
		
		return $opportunity;
	}
}
?>