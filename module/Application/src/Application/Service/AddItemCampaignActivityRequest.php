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
 * @version     SVN $Id: AddItemCampaignActivityRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\CampaignActivity;
use Application\Model\CampaignItem;
use Application\Service\Request;
use Doctrine\ORM\EntityManager;

/**
 * Contains the parameters needed to add an item to a campaign activity.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AddItemCampaignActivityRequest.php 13 2013-08-05 22:53:55Z  $
 */
class AddItemCampaignRequest extends Request {

	/**
	 * @var CampaignActivity
	 */
	private $campaignActivity;

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var CampaignItem
	 */
	private $campaignItem;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'AddItemCampaignRequest' );
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\CampaignActivity
	 */
	public function getCampaignActivity() {
		return $this->campaignActivity;
	}

	/**
	 * @param CampaignActivity $campaignActivity
	 */
	public function setCampaignActivity(CampaignActivity $campaignActivity) {
		$this->campaignActivity = $campaignActivity;
	}

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
	 * @return \Application\Model\CampaignItem
	 */
	public function getCampaignItem() {
		return $this->campaignItem;
	}

	/**
	 * @param CampaignItem $campaignItem
	 */
	public function setCampaignItem(CampaignItem $campaignItem) {
		$this->campaignItem = $campaignItem;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\AddItemCampaignResponse
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		$response = new AddItemCampaignResponse();
		
		if ($this->getEntityManager() == null) {
			$response->setMessage( 'No EntityManager found.' );
			return $response;
		}
		
		$entity = $this->getCampaignActivity();
		if ($entity == null) {
			$response->setMessage( 'No campaign activity found to update.' );
			return $response;
		} elseif ($entity->getId() == null || $entity->getId() < 1) {
			$response->setMessage( 'Cannot update a new unmanaged record.' );
			return $response;
		}
		
		$campaignItem = $this->getCampaignItem();
		if ($campaignItem == null) {
			$response->setMessage( 'No item found to add to the campaign activity.' );
			return $response;
		}
		
		try {
			$em = $this->getEntityManager();
			
			if ($campaignItem instanceof MarketingList) {
				$entity->addList( $campaignItem );
			} elseif ($campaignItem instanceof SalesLiterature) {
				$entity->addSalesLiterature( $campaignItem );
			}
			
			$em->persist( $entity );
			$em->flush();
			
			$response->setCampaignItem( $campaignItem );
			$response->setMessage( 'CampaignItem successfully added.' );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		return $response;
	}
}
?>