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
 * @version     SVN $Id: TargetUpdateActivity.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractTask;
use Application\Model\ActivityState;
use Application\Model\CampaignActivityState;
use Application\Model\CampaignResponse;
use Application\Model\CampaignResponseStatus;
use Application\Model\AppointmentStatus;
use Application\Model\EmailInteraction;
use Application\Model\FaxInteraction;
use Application\Model\FaxStatus;
use Application\Model\LetterInteraction;
use Application\Model\LetterStatus;
use Application\Model\TaskStatus;
use Application\Model\TelephoneInteraction;
use Application\Model\TelephoneStatus;
use Application\Model\VisitInteraction;
use Application\Service\TargetUpdate;
use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;
use Application\Model\CampaignActivity;
use Application\Model\CampaignStatus;
use Application\Model\CampaignActivityStatus;
use Application\Model\AppointmentState;

/**
 * TargetUpdateActivity Class
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetUpdateActivity.php 13 2013-08-05 22:53:55Z  $
 */
class TargetUpdateActivity extends TargetUpdate {

	/**
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
			$entity = $this->prepareActivity( $entity );
			$em->persist( $entity );
			$em->flush();
			
			$response->setMessage( 'Update successful.' );
			$response->setResult( true );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * @param AbstractActivity $entity
	 * @return AbstractActivity
	 */
	private function prepareActivity(AbstractActivity $entity) {
		$now = new \DateTime();
		$user = $this->getUser();
		$em = $this->getEntityManager();
		
		// Persist any One-to-Many associations
		if ($entity->getAccount()) {
			$em->persist( $entity->getAccount() );
		}
		if ($entity->getContact()) {
			$em->persist( $entity->getContact() );
		}
		if ($entity->getLead()) {
			$em->persist( $entity->getLead() );
		}
		if ($entity->getOpportunity()) {
			$em->persist( $entity->getOpportunity() );
		}
		
		if ($entity instanceof AbstractAppointment) {
			$entity->setActualStart( $entity->getScheduledStart() );
			$entity->setActualEnd( $entity->getScheduledEnd() );
			switch ($entity->getStatus()->getName()) {
				case AppointmentStatus::BUSY :
					$entity->setState( AppointmentState::SCHEDULED );
					break;
				case AppointmentStatus::CANCELED :
					$entity->setState( AppointmentState::CANCELED );
					break;
				case AppointmentStatus::COMPLETED :
					$entity->setState( AppointmentState::COMPLETED );
					break;
				case AppointmentStatus::FREE :
					$entity->setState( AppointmentState::OPEN );
					break;
				case AppointmentStatus::OUTOFOFFICE :
					$entity->setState( AppointmentState::SCHEDULED );
					break;
				case AppointmentStatus::TENTATIVE :
					$entity->setState( AppointmentState::OPEN );
					break;
			}
		} elseif ($entity instanceof VisitInteraction) {
			switch ($entity->getStatus()->getName()) {
				case AppointmentStatus::BUSY :
					$entity->setState( AppointmentState::SCHEDULED );
					break;
				case AppointmentStatus::CANCELED :
					$entity->setState( AppointmentState::CANCELED );
					break;
				case AppointmentStatus::COMPLETED :
					$entity->setState( AppointmentState::COMPLETED );
					break;
				case AppointmentStatus::FREE :
					$entity->setState( AppointmentState::OPEN );
					break;
				case AppointmentStatus::OUTOFOFFICE :
					$entity->setState( AppointmentState::SCHEDULED );
					break;
				case AppointmentStatus::TENTATIVE :
					$entity->setState( AppointmentState::OPEN );
					break;
			}
		} elseif ($entity instanceof CampaignActivity) {
			switch ($entity->getStatus()->getName()) {
				case CampaignActivityStatus::ABORTED :
					$entity->setState( CampaignActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case CampaignActivityStatus::CANCELED :
					$entity->setState( CampaignActivityState::CANCELED );
					$this->setActualEndDate( $entity, $now );
					break;
				case CampaignActivityStatus::CLOSED :
					$entity->setState( CampaignActivityState::CLOSED );
					$this->setActualEndDate( $entity, $now );
					break;
				case CampaignActivityStatus::COMPLETED :
					$entity->setState( CampaignActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case CampaignActivityStatus::INPROGRESS :
					$entity->setState( CampaignActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case CampaignActivityStatus::PENDING :
					$entity->setState( CampaignActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case CampaignActivityStatus::PROPOSED :
					$entity->setState( CampaignActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
			}
		} elseif ($entity instanceof CampaignResponse) {
			// Set start date
			if ($entity->getReceivedOn() != null) {
				$entity->setActualStart( $entity->getReceivedOn() );
				$entity->setScheduledStart( $entity->getReceivedOn() );
			} else {
				$entity->setActualStart( $now );
				$entity->setScheduledStart( $now );
			}
			
			// Set state
			switch ($entity->getStatus()->getName()) {
				case CampaignResponseStatus::CANCELED :
					$entity->setState( CampaignActivityState::CANCELED );
					$this->setActualEndDate( $entity, $now );
					break;
				case CampaignResponseStatus::CLOSED :
					$entity->setState( CampaignActivityState::CLOSED );
					$this->setActualEndDate( $entity, $now );
					break;
				case CampaignResponseStatus::OPEN :
					$entity->setState( CampaignActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
			}
		} elseif ($entity instanceof AbstractTask) {
			switch ($entity->getStatus()->getName()) {
				case TaskStatus::CANCELED :
					$entity->setState( ActivityState::CANCELED );
					$this->setActualEndDate( $entity, $now );
					break;
				case TaskStatus::COMPLETED :
					$entity->setState( ActivityState::COMPLETED );
					$this->setActualEndDate( $entity, $now );
					break;
				case TaskStatus::DEFERRED :
					$entity->setState( ActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case TaskStatus::INPROGRESS :
					$entity->setState( ActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case TaskStatus::NOTSTARTED :
					$entity->setState( ActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case TaskStatus::WAITING :
					$entity->setState( ActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
			}
		} elseif ($entity instanceof TelephoneInteraction) {
			switch ($entity->getStatus()->getName()) {
				case TelephoneStatus::CANCELED :
					$entity->setState( ActivityState::CANCELED );
					break;
				case TelephoneStatus::MADE :
					$entity->setState( ActivityState::COMPLETED );
					break;
				case TelephoneStatus::OPEN :
					$entity->setState( ActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case TelephoneStatus::RECEIVED :
					$entity->setState( ActivityState::COMPLETED );
					break;
			}
		} elseif ($entity instanceof FaxInteraction) {
			switch ($entity->getStatus()->getName()) {
				case FaxStatus::CANCELED :
					$entity->setState( ActivityState::CANCELED );
					$this->setActualEndDate( $entity, $now );
					break;
				case FaxStatus::COMPLETED :
					$entity->setState( ActivityState::COMPLETED );
					$this->setActualEndDate( $entity, $now );
					break;
				case FaxStatus::OPEN :
					$entity->setState( ActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case FaxStatus::RECEIVED :
					$entity->setState( ActivityState::COMPLETED );
					$this->setActualEndDate( $entity, $now );
					break;
				case FaxStatus::SENT :
					$entity->setState( ActivityState::COMPLETED );
					$this->setActualEndDate( $entity, $now );
					break;
			}
		} elseif ($entity instanceof LetterInteraction) {
			switch ($entity->getStatus()->getName()) {
				case LetterStatus::CANCELED :
					$entity->setState( ActivityState::CANCELED );
					$this->setActualEndDate( $entity, $now );
					break;
				case LetterStatus::DRAFT :
					$entity->setState( ActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case LetterStatus::OPEN :
					$entity->setState( ActivityState::OPEN );
					$this->setActualStartDate( $entity, $now );
					break;
				case LetterStatus::RECEIVED :
					$entity->setState( ActivityState::COMPLETED );
					$this->setActualEndDate( $entity, $now );
					break;
				case LetterStatus::SENT :
					$entity->setState( ActivityState::COMPLETED );
					$this->setActualEndDate( $entity, $now );
					break;
			}
		} elseif ($entity instanceof EmailInteraction) {
			switch ($entity->getStatus()->getName()) {
				case LetterStatus::CANCELED :
					$entity->setState( ActivityState::CANCELED );
					break;
				case LetterStatus::DRAFT :
					$entity->setState( ActivityState::OPEN );
					break;
				case LetterStatus::OPEN :
					$entity->setState( ActivityState::OPEN );
					break;
				case LetterStatus::RECEIVED :
					$entity->setState( ActivityState::COMPLETED );
					break;
				case LetterStatus::SENT :
					$entity->setState( ActivityState::COMPLETED );
					break;
			}
		}
		
		// Set user characteristics
		if ($entity->getOwner() != null) {
			$entity->setBusinessUnit( $entity->getOwner()
				->getBusinessUnit() );
		} else {
			$entity->setOwner( $user );
			$entity->setBusinessUnit( $user->getBusinessUnit() );
		}
		
		// Set modification date
		$entity->setLastUpdateDate( $now );
		
		return $entity;
	}

	private function setActualStartDate(Entity $entity,
			DateTime $proposedStartDate) {
		if ($entity instanceof Trackable) {
			if ($entity->getActualStart() == null) {
				$entity->setActualStart( $proposedStartDate );
			}
		}
	}

	private function setActualEndDate(Entity $entity,\DateTime $proposedEndDate) {
		if ($entity instanceof Trackable) {
			if ($entity->getActualEnd() == null) {
				$entity->setActualEnd( $proposedEndDate );
			}
		}
	}
}
?>
