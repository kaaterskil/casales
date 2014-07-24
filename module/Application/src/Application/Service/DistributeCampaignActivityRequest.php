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
 * @version     SVN $Id: DistributeCampaignActivityRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\AbstractInteraction;
use Application\Model\Account;
use Application\Model\ActivityState;
use Application\Model\BulkOperation;
use Application\Model\BulkOperationType;
use Application\Model\BulkOperationStatus;
use Application\Model\BulkOperationState;
use Application\Model\Campaign;
use Application\Model\CampaignActivity;
use Application\Model\Contact;
use Application\Model\Contactable;
use Application\Model\Direction;
use Application\Model\EmailInteraction;
use Application\Model\EmailStatus;
use Application\Model\FaxStatus;
use Application\Model\Lead;
use Application\Model\LetterStatus;
use Application\Model\MarketingList;
use Application\Model\MemberType;
use Application\Model\TargetedRecordType;
use Application\Model\TelephoneStatus;
use Application\Model\User;
use Application\Service\PropagationOwnershipOptions;
use Application\Service\Request;
use Application\Service\SendEmailRequest;
use Application\Service\SendEmailResponse;
use Doctrine\ORM\EntityManager;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Headers;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Application\Model\TelephoneInteraction;
use Application\Model\FaxInteraction;
use Application\Model\LetterInteraction;
use Application\Model\VisitInteraction;
use Application\Model\Telephone;

/**
 * Specifies the parameters needed to distribute the campaign activity, creating
 * the needed activity for each member in the list, for the specified campaign activity.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: DistributeCampaignActivityRequest.php 13 2013-08-05 22:53:55Z  $
 */
class DistributeCampaignActivityRequest extends Request {

	/**
	 * The activity to be distributed
	 * @var AbstractInteraction
	 */
	private $activity;

	/**
	 * The campaign activity for which the activity will be distributed
	 * @var CampaignActivity
	 */
	private $campaignActivity;

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * The owner of the newly-created activity
	 * @var User
	 */
	private $owner;

	/**
	 * The ownership options for the activity.
	 * @var PropagationOwnershipOptions
	 */
	private $ownershipOptions;

	/**
	 * USED FOR MS OUTLOOK INTEGRATION
	 * @var boolean
	 */
	private $propagate = false;

	/**
	 * A value that indicates whether to send the e-mail for the campaign. This is for
	 * campaign activities of type e-mail.
	 * @var boolean
	 */
	private $sendEmail = false;

	/**
	 * @var SmtpTransport
	 */
	private $transport;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'DistributeCampaignActivityRequest' );
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\AbstractInteraction
	 */
	public function getActivity() {
		return $this->activity;
	}

	/**
	 * @param AbstractInteraction $activity
	 */
	public function setActivity(AbstractInteraction $activity) {
		$this->activity = $activity;
	}

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
	 * @return \Application\Model\User
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * @param User $owner
	 */
	public function setOwner(User $owner) {
		$this->owner = $owner;
	}

	/**
	 * @return \Application\Service\PropagationOwnershipOptions
	 */
	public function getOwnershipOptions() {
		return $this->ownershipOptions;
	}

	/**
	 * @param string|PropagationOwnershipOptions $ownershipOptions
	 */
	public function setOwnershipOptions($ownershipOptions = null) {
		if ($ownershipOptions instanceof PropagationOwnershipOptions) {
			$this->ownershipOptions = $ownershipOptions;
		} elseif ($ownershipOptions != null) {
			$this->ownershipOptions = PropagationOwnershipOptions::instance( $ownershipOptions );
		} else {
			$this->ownershipOptions = null;
		}
	}

	/**
	 * @return boolean
	 */
	public function getPropagate() {
		return $this->propagate;
	}

	/**
	 * @param boolean $propagate
	 */
	public function setPropagate($propagate) {
		$this->propagate = (bool) $propagate;
	}

	/**
	 * @return boolean
	 */
	public function getSendEmail() {
		return $this->sendEmail;
	}

	/**
	 * @param boolean $sendEmail
	 */
	public function setSendEmail($sendEmail) {
		$this->sendEmail = (bool) $sendEmail;
	}

	/**
	 * @return \Zend\Mail\Transport\Smtp
	 */
	public function getTransport() {
		return $this->transport;
	}

	public function setTransport(SmtpTransport $transport) {
		$this->transport = $transport;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\DistributeCampaignActivityResponse
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		$response = new DistributeCampaignActivityResponse();
		
		// Fetch transport - this is an SMTP transport configured by a factory method in
		// the module manager and injected by the service. The configuration is found in
		// the local.php file
		$transport = $this->getTransport();
		if ($transport == null) {
			$response->setMessage( 'No transport found.' );
			return $response;
		}
		
		if ($this->getActivity() == null) {
			$response->setMessage( 'No activity provided to distribute.' );
			return $response;
		}
		if ($this->getCampaignActivity() == null) {
			$response->setMessage( 'No campaign activity provided for the activity.' );
			return $response;
		}
		if ($this->getOwner() == null) {
			$response->setMessage( 'No activity owner provided.' );
			return $response;
		}
		if ($this->getOwnershipOptions() == null || $this->getOwnershipOptions() == '') {
			$response->setMessage( 'No ownership options provided.' );
			return $response;
		}
		
		try {
			$em = $this->getEntityManager();
			
			$bo = $this->distributeActivity();
			if ($this->getActivity() instanceof EmailInteraction) {
				$this->sendEmails( $bo );
			}
			
			// Update values
			$now = new \DateTime();
			$bo->setActualEnd( $now );
			$bo->setStatus( BulkOperationStatus::COMPLETED );
			$bo->setState( BulkOperationState::COMPLETED );
			$em->flush();
			
			$response->setResult( true );
			$response->setMessage( 'Distribution to the bulk operation successful' );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * Clones the specified activity for each of the members of the specified campaign
	 * activiy's marketing lists
	 *
	 * @return \Application\Model\BulkOperation
	 */
	private function distributeActivity() {
		/* @var $a AbstractInteraction */
		/* @var $account Account */
		/* @var $contact Contact */
		/* @var $lead Lead */
		/* @var $list MarketingList */

		$now = new \DateTime();
		$em = $this->getEntityManager();
		
		$activity = $this->getActivity();
		$campaignActivity = $this->getCampaignActivity();
		$owner = $this->getOwner();
		$options = $this->getOwnershipOptions();
		
		// Create a new BulkOperation
		$bo = new BulkOperation();
		$bo->setActualStart( $now );
		$bo->setBusinessUnit( $owner->getBusinessUnit() );
		$bo->setCampaign( $campaignActivity->getCampaign() );
		$bo->setCreatedRecordType( $activity->getDiscriminator() );
		$bo->setCreationDate( $now );
		$bo->setDescription( $campaignActivity->getDescription() );
		$bo->setLastUpdateDate( $now );
		$bo->setOperationType( BulkOperationType::DISTRIBUTE );
		$bo->setOwner( $owner );
		$bo->setScheduledEnd( $campaignActivity->getScheduledEnd() );
		$bo->setScheduledStart( $campaignActivity->getScheduledStart() );
		$bo->setState( BulkOperationState::OPEN );
		$bo->setStatus( BulkOperationStatus::PENDING );
		$bo->setSubject( $campaignActivity->getSubject() );
		
		// Associate the BulkOperation with the CampaignActivity
		$campaignActivity->addBulkOperation( $bo );
		$em->persist( $campaignActivity );
		
		// Set up duplicate check
		$distributed = array(
			'accounts' => array(),
			'contacts' => array(),
			'leads' => array()
		);
		
		// Set up duplicate check
		$skipped = array(
			'accounts' => array(),
			'contacts' => array(),
			'leads' => array()
		);
		
		// Set up duplicate check
		$duplicates = array(
			'accounts' => array(),
			'contacts' => array(),
			'leads' => array()
		);
		
		foreach ( $campaignActivity->getLists() as $list ) {
			$options = $this->getOwnershipOptions();
			if ($options == PropagationOwnershipOptions::CALLER) {
				$owner = $this->getOwner();
			} else {
				$owner = $list->getOwner();
			}
			
			// Associate the BulkOperation with the MarketingList
			$list->addBulkOperation( $bo );
			
			// Loop through each MarketingList member
			$memberType = $list->getMemberType();
			switch ($memberType) {
				case MemberType::ACCOUNT :
					// Update the member count
					$count = $bo->getTargetMembersCount() + $list->getAccounts()->count();
					$bo->setTargetMembersCount( $count );
					
					// Clone the activity and associate the record with each list member
					// as well as the BulkOperation
					foreach ( $list->getAccounts() as $account ) {
						// Skip if the activity is an email and the target has no email
						// address
						if (($activity instanceof EmailInteraction) && ($account->getEmail1() == '')) {
							$skipped['accounts'][] = $account;
							continue;
						}
						
						// Skip if this is a duplicate
						if (in_array( $account, $distributed['accounts'] )) {
							$duplicates['accounts'][] = $account;
							continue;
						}
						
						$a = clone $activity;
						$this->setDefaultValues( $a, $owner, $account );
						
						$bo->addBulkInteraction( $a );
						$account->addActivity( $a );
						$campaignActivity->addInteraction( $a );
						$campaignActivity->getCampaign()->addInteraction( $a );
						$em->persist( $account );
						$distributed['accounts'][] = $account;
					}
					break;
				case MemberType::CONTACT :
					// Update the member count
					$count = $bo->getTargetMembersCount() + $list->getContacts()->count();
					$bo->setTargetMembersCount( $count );
					
					// Clone the activity and associate the record with each list member
					// as well as the BulkOperation
					foreach ( $list->getContacts() as $contact ) {
						// Skip if the activity is an email and the target has no email
						// address
						if (($activity instanceof EmailInteraction) && ($contact->getEmail1() == '')) {
							$skipped['contacts'][] = $contact;
							continue;
						}
						
						// Skip if this is a duplicate
						if (in_array( $contact, $distributed['contacts'] )) {
							$duplicates['contacts'][] = $contact;
							continue;
						}
						
						$a = clone $activity;
						$this->setDefaultValues( $a, $owner, $contact );
						
						$bo->addBulkInteraction( $a );
						$contact->addActivity( $a );
						$campaignActivity->addInteraction( $a );
						$campaignActivity->getCampaign()->addInteraction( $a );
						$em->persist( $contact );
						$distributed['contacts'][] = $contact;
					}
					break;
				case MemberType::LEAD :
					// Update the member count
					$count = $bo->getTargetMembersCount() + $list->getLeads()->count();
					$bo->setTargetMembersCount( $count );
					
					// Clone the activity and associate the record with each list member
					// as well as the BulkOperation
					foreach ( $list->getLeads() as $lead ) {
						// Skip if the activity is an email and the target has no email
						// address
						if (($activity instanceof EmailInteraction) && ($lead->getEmail1() == '')) {
							$skipped['leads'][] = $lead;
							continue;
						}
						
						// Skip if this is a duplicate
						if (in_array( $lead, $distributed['leads'] )) {
							$duplicates['leads'][] = $lead;
							continue;
						}
						
						$a = clone $activity;
						$this->setDefaultValues( $a, $owner, $lead );
						
						$bo->addBulkInteraction( $a );
						$lead->addInteraction( $a );
						$campaignActivity->addInteraction( $a );
						$campaignActivity->getCampaign()->addInteraction( $a );
						$em->persist( $lead );
						$distributed['leads'][] = $lead;
					}
					break;
			}
		}
		
		return $bo;
	}

	/**
	 * Sends emails using the SendEmailRequest service object
	 * The SMTP transport is used for the entire batch
	 *
	 * @param BulkOperation $bo
	 */
	private function sendEmails(BulkOperation $bo) {
		/* @var $activity EmailInteraction */
		/* @var $response SendEmailResponse */

		$count = 0;
		$owner = $this->getOwner();
		
		if ($this->getSendEmail()) {
			$request = new SendEmailRequest();
			$request->setIssueSend( $this->getSendEmail() );
			$request->setTransport( $this->getTransport() );
			$request->setUser( $owner );
			
			// Everyone has an email address
			foreach ( $bo->getBulkInteractions() as $activity ) {
				$parent = $activity->getRegardingObject();
				$activity->setFrom( $owner->getEmail() );
				$activity->setTo( $parent->getEmail1() );
				$activity->setDirection( Direction::OUTBOUND );
				$request->setActivity( $activity );
				
				// Send the email using the existing transport
				$response = $request->execute();
				if ($response->getResult()) {
					$activity->setStatus( EmailStatus::SENT );
					$activity->setState( ActivityState::COMPLETED );
					$count++;
				} else {
					$activity->setStatus( EmailStatus::FAILED );
					$activity->setState( ActivityState::OPEN );
				}
			}
		} else {
			$now = new \DateTime();
			
			// Set the email values as if the email was sent
			// Everyone has an email address
			foreach ( $bo->getBulkInteractions() as $activity ) {
				$parent = $activity->getRegardingObject();
				$activity->setFrom( $owner->getEmail() );
				$activity->setTo( $parent->getEmail1() );
				if ($activity->getMimetype() == '') {
					$activity->setMimetype( 'text/html' );
				}
				$activity->setSendStartDate( $now );
				$activity->setSendEndDate( $now );
				$activity->setDirection( Direction::OUTBOUND );
				$count++;
			}
		}
		$bo->setSuccessCount( $count );
		$bo->setFailureCount( $bo->getTargetMembersCount() - $count );
	}

	/**
	 * @param TelephoneInteraction $ai
	 * @param User $o
	 * @param Contactable $c
	 */
	private function setDefaultValues(AbstractInteraction $ai, User $o, Contactable $c) {
		// Set recipient
		if ($ai instanceof EmailInteraction) {
			$ai->setTo( $c->getEmail1() );
		} else {
			$ai->setTo( $c->getDisplayName() );
		}
		
		// Set other values
		if (($ai instanceof TelephoneInteraction) || ($ai instanceof FaxInteraction)) {
			$ai->setTelephone( $c->getPrimaryTelephone() );
		}
		if (($ai instanceof LetterInteraction) || ($ai instanceof VisitInteraction)) {
			$ai->setAddress( $c->getPrimaryAddress()
				->getFullAddress() );
		}
		
		// Set status
		$this->setStatus( $ai );
		
		// Set ownership
		$ai->setBusinessUnit( $o->getBusinessUnit() );
		$ai->setOwner( $o );
		
		// Set modification dates
		$now = new \DateTime();
		$ai->setCreationDate( $now );
		$ai->setLastUpdateDate( $now );
	}

	/**
	 * Sets the cloned activity's status and state
	 * @param AbstractInteraction $a
	 */
	private function setStatus(AbstractInteraction $a) {
		switch ($a->getDiscriminator()) {
			case 'EmailInteraction' :
				if ($this->getSendEmail()) {
					$a->setStatus( EmailStatus::PENDINGSEND );
					$a->setState( ActivityState::OPEN );
				} else {
					$a->setStatus( EmailStatus::SENT );
					$a->setState( ActivityState::COMPLETED );
				}
				break;
			case 'FaxInteraction' :
				$a->setStatus( FaxStatus::OPEN );
				$a->setState( ActivityState::OPEN );
				break;
			case 'LetterInteraction' :
				$a->setStatus( LetterStatus::OPEN );
				$a->setState( ActivityState::OPEN );
				break;
			case 'TelephoneInteraction' :
				$a->setStatus( TelephoneStatus::OPEN );
				$a->setState( ActivityState::OPEN );
				break;
		}
	}
}
?>