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
 * @version     SVN $Id: TargetCreateCampaign.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\Campaign;
use Application\Model\CampaignState;
use Application\Model\CampaignStatus;
use Application\Service\TargetCreate;
use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;

/**
 * Contains the data needed to create a campaign.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetCreateCampaign.php 13 2013-08-05 22:53:55Z  $
 */
class TargetCreateCampaign extends TargetCreate {

	/**
	 * Constructor
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
			$entity = $this->prepareCampaign( $entity );
			$em->persist( $entity );
			$em->flush();
			
			$response->setResult( true );
			$response->setId( $entity->getId() );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * @param Campaign $campaign
	 * @return Campaign
	 */
	private function prepareCampaign(Campaign $campaign) {
		$user = $this->getUser();
		$now = new \DateTime();
		
		// Set state
		switch ($campaign->getStatus()->getName()) {
			case CampaignStatus::PROPOSED :
				$campaign->setState( CampaignState::ACTIVE );
				break;
			case CampaignStatus::READY :
				$campaign->setState( CampaignState::ACTIVE );
				break;
			case CampaignStatus::LAUNCHED :
				$campaign->setState( CampaignState::ACTIVE );
				break;
			case CampaignStatus::COMPLETED :
				$campaign->setState( CampaignState::ACTIVE );
				break;
			case CampaignStatus::CANCELED :
				$campaign->setState( CampaignState::ACTIVE );
				break;
			case CampaignStatus::SUSPENDED :
				$campaign->setState( CampaignState::ACTIVE );
				break;
			case CampaignStatus::INACTIVE :
				$campaign->setState( CampaignState::INACTIVE );
				break;
		}
		
		// Set user characteristics
		if ($campaign->getOwner() != null) {
			$campaign->setBusinessUnit( $campaign->getOwner()
				->getBusinessUnit() );
		} else {
			$campaign->setOwner( $user );
			$campaign->setBusinessUnit( $user->getBusinessUnit() );
		}
		
		// Set creation and modification dates
		if ($campaign->getId() == null || $campaign->getId() == 0) {
			$campaign->setCreationDate( $now );
		}
		$campaign->setLastUpdateDate( $now );
		
		return $campaign;
	}
}
?>