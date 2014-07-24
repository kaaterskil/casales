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
 * @version     SVN $Id: TargetCreateActivity.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractTask;
use Application\Model\ActivityState;
use Application\Model\AppointmentState;
use Application\Model\AppointmentStatus;
use Application\Model\CampaignActivity;
use Application\Model\CampaignActivityState;
use Application\Model\CampaignActivityStatus;
use Application\Model\CampaignResponse;
use Application\Model\CampaignResponseStatus;
use Application\Model\EmailInteraction;
use Application\Model\EmailStatus;
use Application\Model\FaxInteraction;
use Application\Model\FaxStatus;
use Application\Model\LetterInteraction;
use Application\Model\LetterStatus;
use Application\Model\TaskStatus;
use Application\Model\TelephoneInteraction;
use Application\Model\TelephoneStatus;
use Application\Model\Trackable;
use Application\Model\VisitInteraction;
use Application\Service\TargetCreate;
use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;

/**
 * TargetCreateActivity Class
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetCreateActivity.php 13 2013-08-05 22:53:55Z  $
 */
class TargetCreateActivity extends TargetCreate {

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
			$entity = $this->prepareActivity( $entity );
			$em->persist( $entity );
			$em->flush();
			
			$response->setMessage( 'Activity successfully created.' );
			$response->setResult( true );
			$response->setId( $entity->getId() );
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
			
			// Copy the parent campaign's marketing lists
			$entity->setLists( $entity->getCampaign()
				->getLists() );
			
			// Set the state
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
					$this->setActualEndDate( $entity, $now );
					break;
				case TelephoneStatus::MADE :
					$entity->setState( ActivityState::COMPLETED );
					$this->setActualEndDate( $entity, $now );
					break;
				case TelephoneStatus::OPEN :
					$entity->setState( ActivityState::OPEN );
					break;
				case TelephoneStatus::RECEIVED :
					$entity->setState( ActivityState::COMPLETED );
					$this->setActualEndDate( $entity, $now );
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
				case EmailStatus::DRAFT :
					$entity->setState( ActivityState::OPEN );
					break;
				case EmailStatus::COMPLETED :
					$entity->setState( ActivityState::COMPLETED );
					break;
				case EmailStatus::SENT :
					$entity->setState( ActivityState::COMPLETED );
					break;
				case EmailStatus::RECEIVED :
					$entity->setState( ActivityState::COMPLETED );
					$this->setActualStartDate( $entity, $now );
					$this->setActualEndDate( $entity, $now );
					break;
				case EmailStatus::CANCELED :
					$entity->setState( ActivityState::CANCELED );
					break;
				case EmailStatus::PENDINGSEND :
					$entity->setState( ActivityState::COMPLETED );
					break;
				case EmailStatus::SENDING :
					$entity->setState( ActivityState::COMPLETED );
					break;
				case EmailStatus::FAILED :
					$entity->setState( ActivityState::OPEN );
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
		
		// Set creation and modification times
		$entity->setCreationDate( $now );
		$entity->setLastUpdateDate( $now );
		
		if ($entity instanceof AbstractAppointment) {
			$entity->setOriginalStartDate( $entity->getScheduledStart() );
		}
		
		return $entity;
	}

	private function setActualStartDate(Entity $entity, \DateTime $proposedStartDate) {
		if ($entity instanceof Trackable) {
			if ($entity->getActualStart() == null) {
				$entity->setActualStart( $proposedStartDate, true );
			}
		}
	}

	private function setActualEndDate(Entity $entity, \DateTime $proposedEndDate) {
		if ($entity instanceof Trackable) {
			if ($entity->getActualEnd() == null) {
				$entity->setActualEnd( $proposedEndDate, true );
			}
		}
	}
}
?>
