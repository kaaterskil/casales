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
 * @version     SVN $Id: FlushListener.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\AccountState;
use Application\Model\AccountStatus;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Application\Model\AppointmentState;
use Application\Model\AppointmentStatus;
use Application\Model\Audit;
use Application\Model\Auditable;
use Application\Model\AuditAction;
use Application\Model\AuditOperation;
use Application\Model\BulkOperationState;
use Application\Model\BulkOperationStatus;
use Application\Model\CampaignActivityState;
use Application\Model\CampaignActivityStatus;
use Application\Model\CampaignResponseStatus;
use Application\Model\ContactState;
use Application\Model\ContactStatus;
use Application\Model\EmailStatus;
use Application\Model\FaxStatus;
use Application\Model\LeadState;
use Application\Model\LeadStatus;
use Application\Model\LetterStatus;
use Application\Model\OpportunityCloseState;
use Application\Model\OpportunityCloseStatus;
use Application\Model\OpportunityState;
use Application\Model\OpportunityStatus;
use Application\Model\TaskStatus;
use Application\Model\TelephoneStatus;
use Application\Model\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * A Doctrine listener to create an audit trail of object property changes.
 *
 * This object is instantiated in the AbstractApplicationController and registered
 * with the Doctrine EventManager.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: FlushListener.php 13 2013-08-05 22:53:55Z  $
 * @see http://docs.doctrine-project.org/en/2.0.x/reference/events.html#onflush
 */
class FlushListener {

	/**
	 * @var User
	 */
	private $user;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 * @param User $user
	 */
	public function __construct(User $user = null) {
		$this->user = $user;
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user) {
		$this->user = $user;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * Interrogates the Unit of Work and creates Audit records for auditable entities that
	 * have dirty properties
	 *
	 * @param OnFlushEventArgs $args
	 */
	public function onFlush(OnFlushEventArgs $args) {
		/* @var $em EntityManager */
		$em = $args->getEntityManager();
		$uow = $em->getUnitOfWork();
		$metadata = $em->getClassMetadata( 'Application\Model\Audit' );
		$now = new \DateTime();
		$user = $this->getUser();
		
		foreach ( $uow->getScheduledEntityInsertions() as $entity ) {
			if ($entity instanceof Auditable) {
				$auditItem = new Audit();
				$auditItem->setAction( AuditAction::CREATE );
				$auditItem->setCreationDate( $now );
				$auditItem->setOperation( AuditOperation::CREATE );
				$auditItem->setUser( $user );
				$entity->addAuditItem( $auditItem );
				
				$em->persist( $auditItem );
				$uow->computeChangeSet( $metadata, $auditItem );
			}
		}
		
		foreach ( $uow->getScheduledEntityUpdates() as $entity ) {
			if ($entity instanceof Auditable) {
				$properties = $entity->getAuditableProperties();
				foreach ( $uow->getEntityChangeSet( $entity ) as $field => $vals ) {
					if (($vals[0] != $vals[1]) && (in_array( $field, $properties ))) {
						$auditItem = new Audit();
						$auditItem->setAction( $this->getAction( $field, $vals[1] ) );
						$auditItem->setNewData( $this->toString( $vals[1] ) );
						$auditItem->setOldData( $this->toString( $vals[0] ) );
						$auditItem->setCreationDate( $now );
						$auditItem->setOperation( AuditOperation::UPDATE );
						$auditItem->setProperty( $field );
						$auditItem->setUser( $user );
						
						$entity->addAuditItem( $auditItem );
						
						$em->persist( $auditItem );
						$uow->computeChangeSet( $metadata, $auditItem );
					}
				}
			}
		}
		
		foreach ( $uow->getScheduledEntityDeletions() as $entity ) {
			if ($entity instanceof Auditable) {
				$auditItem = new Audit();
				$auditItem->setAction( AuditAction::DELETE );
				$auditItem->setCreationDate( $now );
				$auditItem->setOperation( AuditOperation::DELETE );
				$auditItem->setUser( $user );
				$entity->addAuditItem( $auditItem );
				
				$em->persist( $auditItem );
				$uow->computeChangeSet( $metadata, $auditItem );
			}
		}
		
		foreach ( $uow->getScheduledCollectionDeletions() as $collection ) {
		}
		
		foreach ( $uow->getScheduledCollectionUpdates() as $collection ) {
		}
	}

	/**
	 * Returns the action code for the given new value
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return string
	 */
	private function getAction($field, $value) {
		$result = AuditAction::UPDATE;
		
		if ($field == 'state') {
			switch ($value) {
				case AccountState::ACTIVE :
				case ContactState::ACTIVE :
					$result = AuditAction::ACTIVATE;
					break;
				case AccountState::INACTIVE :
				case ContactState::INACTIVE :
					$result = AuditAction::DEACTIVATE;
					break;
				case LeadState::QUALIFIED :
					$result = AuditAction::QUALIFY;
					break;
				case LeadState::DISQUALIFIED :
					$result = AuditAction::DISQUALIFY;
					break;
				case OpportunityState::LOST :
					$result = AuditAction::LOSE;
					break;
				case OpportunityState::WON :
					$result = AuditAction::WIN;
					break;
				case AppointmentState::SCHEDULED :
					$result = AuditAction::RESCHEDULE;
					break;
				case ActivityState::OPEN :
				case BulkOperationState::OPEN :
				case CampaignActivityState::OPEN :
				case OpportunityCloseState::OPEN :
					$result = AuditAction::REOPEN;
					break;
				case ActivityState::COMPLETED :
				case OpportunityCloseState::COMPLETED :
					$result = AuditAction::COMPLETE;
					break;
				case ActivityState::CANCELED :
				case BulkOperationState::CANCELED :
				case CampaignActivityState::CANCELED :
				case OpportunityCloseState::CANCELED :
					$result = AuditAction::CANCEL;
					break;
				case BulkOperationState::CLOSED :
				case CampaignActivityState::CLOSED :
					$result = AuditAction::CLOSE;
					break;
				default :
					$result = AuditAction::SETSTATE;
					break;
			}
		} elseif ($field == 'status') {
			switch ($value) {
				case AccountStatus::ACTIVE :
				case ContactStatus::ACTIVE :
					$result = AuditAction::ACTIVATE;
					break;
				case AccountStatus::INACTIVE :
				case ContactStatus::INACTIVE :
					$result = AuditAction::DEACTIVATE;
					break;
				case LeadStatus::CANCELED :
				case OpportunityStatus::CANCELED :
				case EmailStatus::CANCELED :
				case FaxStatus::CANCELED :
				case LetterStatus::CANCELED :
				case TelephoneStatus::CANCELED :
				case AppointmentStatus::CANCELED :
				case TaskStatus::CANCELED :
				case CampaignActivityStatus::CANCELED :
				case OpportunityCloseStatus::CANCELED:
				case CampaignResponseStatus::CANCELED :
				case BulkOperationStatus::CANCELED:
					$result = AuditAction::CANCEL;
					break;
				case LeadStatus::QUALIFIED :
					$result = AuditAction::QUALIFY;
					break;
				case OpportunityStatus::WON :
					$result = AuditAction::WIN;
					break;
				case OpportunityStatus::NOTINTERESTED :
					$result = AuditAction::LOSE;
					break;
				case EmailStatus::FAILED :
				case EmailStatus::PENDINGSEND :
				case EmailStatus::SENDING :
				case EmailStatus::SENT :
					$result = AuditAction::SENDEMAIL;
					break;
				case FaxStatus::COMPLETED :
				case AppointmentStatus::COMPLETED :
				case TaskStatus::COMPLETED :
				case CampaignActivityStatus::COMPLETED :
				case OpportunityCloseStatus::COMPLETED:
				case BulkOperationStatus::COMPLETED:
					$result = AuditAction::COMPLETE;
					break;
				case CampaignResponseStatus::CLOSED :
					$result = AuditAction::CLOSE;
					break;
				case FaxStatus::OPEN :
				case LetterStatus::OPEN :
				case TelephoneStatus::OPEN :
				case AppointmentStatus::FREE :
				case CampaignActivityStatus::INPROGRESS :
				case CampaignActivityStatus::PROPOSED :
				case CampaignActivityStatus::PENDING :
				case OpportunityCloseStatus::OPEN:
				case CampaignResponseStatus::OPEN :
					$result = AuditAction::REOPEN;
					break;
				default :
					$result = AuditAction::SETSTATE;
					break;
			}
		}
		return $result;
	}

	/**
	 * Returns a string representation of object values, particularly \DateTime,
	 * which has no __toString method
	 *
	 * @param mixed $data
	 * @return string
	 */
	private function toString($data) {
		$result = '';
		if ($data instanceof \DateTime) {
			$result = $data->format( 'Y-m-d H:i:s' );
		} else {
			$result = (string) $data;
		}
		return $result;
	}
}
?>