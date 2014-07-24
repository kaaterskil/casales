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
 * @version     SVN $Id: WinOpportunityRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\Opportunity;
use Application\Model\OpportunityClose;
use Application\Model\OpportunityCloseState;
use Application\Model\OpportunityCloseStatus;
use Application\Model\OpportunityStatus;
use Application\Model\OpportunityState;

use Application\Service\Request;
use Doctrine\ORM\EntityManager;
use Application\Model\ActivityState;

/**
 * Contains the data needed to set the state of an opportunity to Won.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: WinOpportunityRequest.php 13 2013-08-05 22:53:55Z  $
 */
class WinOpportunityRequest extends Request {

	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @var OpportunityClose
	 */
	private $opportunityClose;
	
	/**
	 * @var int
	 */
	private $opportunityId;
	
	/**
	 * @var OpportunityCloseStatus
	 */
	private $status;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct('WinOpportunityRequest');
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
	 * @return \Application\Model\OpportunityClose
	 */
	public function getOpportunityClose() {
		return $this->opportunityClose;
	}
	
	/**
	 * @param OpportunityClose $opportunityClose
	 */
	public function setOpportunityClose(OpportunityClose $opportunityClose) {
		$this->opportunityClose = $opportunityClose;
	}
	
	/**
	 * @return int
	 */
	public function getOpportunityId() {
		return $this->opportunityId;
	}
	
	/**
	 * @param Opportunity $opportunity
	 */
	public function setOpportunityId($id) {
		$this->opportunityId = (int) $id;
	}
	
	/**
	 * @return \Application\Model\OpportunityStatus
	 */
	public function getStatus() {
		if($this->status == null) {
			$this->status = OpportunityStatus::WON;
		}
		return $this->status;
	}
	
	public function setStatus($status = null) {
		if($status instanceof OpportunityStatus) {
			$this->status = $status;
		} elseif($status != null) {
			$this->status = OpportunityStatus::instance($status);
		} else {
			$this->status = null;
		}
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\WinOpportunityResponse
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		$response = new WinOpportunityResponse();
		
		if($this->getEntityManager() == null) {
			$response->setMessage('EntityManager not found.');
			return $response;
		}
		if($this->getOpportunityId() == 0) {
			$response->setMessage('Opportunity not found.');
			return $response;
		}
		if($this->getOpportunityClose() == null) {
			$response->setMessage('Activity not found.');
			return $response;
		}
		
		try{
			$em = $this->getEntityManager();
			$opportunity = $em->getRepository('Application\Model\Opportunity')->find($this->getOpportunityId());

			$activity = $this->getOpportunityClose();
			$this->prepareActivity($opportunity, $activity);
			$em->persist($activity);
			
			$this->prepareOpportunity($opportunity, $activity);
			$opportunity->addOpportunityClose($activity);
			$em->persist($opportunity);
			$em->flush();
			
			$response->setMessage('Win succeeded');
			$response->setResult(true);
		}catch(Exception $e){
			$response->setMessage($e->getMessage());
		}
		
		return $response;
	}
	
	/**
	 * @param Opportunity $opportunity
	 * @param OpportunityClose $activity
	 */
	private function prepareActivity(Opportunity $opportunity, OpportunityClose $activity) {
		$now = new \DateTime();
		
		$activity->setActualStart($activity->getActualEnd());
		$activity->setState(ActivityState::COMPLETED);
		$activity->setStatus(OpportunityCloseStatus::COMPLETED);
		$activity->setCreationDate($now);
		$activity->setLastUpdateDate($now);
	}
	
	/**
	 * @param Opportunity $opportunity
	 * @param OpportunityClose $activity
	 */
	private function prepareOpportunity(Opportunity $opportunity, OpportunityClose $activity) {
		$now = new \DateTime();

		$opportunity->setActualCloseDate($activity->getActualEnd());
		$opportunity->setActualValue($activity->getActualRevenue());
		$opportunity->setState(OpportunityState::WON);
		$opportunity->setStatus($this->getStatus());
		$opportunity->setLastUpdateDate($now);
	}
}
?>