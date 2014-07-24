<?php

/**
 * Casales Library
 *
 * PHP version 5.4
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
 * KAATERSKIL MANAGEMENT, LLC BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Casales
 * @package     Application\Controller
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: MailController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Controller\AbstractApplicationController;
use Application\Form\ActivityForm;
use Application\Form\EmailFieldset;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\Contact;
use Application\Model\Direction;
use Application\Model\EmailInteraction;
use Application\Model\EmailStatus;
use Application\Model\Lead;
use Application\Model\Regarding;
use Application\Model\User;
use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\ProcessMailRequest;
use Application\Service\ProcessMailResponse;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\SendEmailRequest;
use Application\Service\SendEmailResponse;
use Application\Service\TargetCreateActivity;
use Application\Service\TargetDeleteActivity;
use Application\Service\TargetRetrieveActivity;
use Application\Service\TargetRetrieveMultipleByQuery;
use Application\Service\UploadActivityMimeAttachmentRequest;
use Zend\Http\Request;
use Zend\Mail\Headers;
use Zend\Mail\Header\GenericHeader;
use Zend\Mail\Header\Received;
use Zend\Mail\Storage\Imap;
use Zend\Mail\Storage\Message as MailMessage;
use Zend\View\Model\ViewModel;

/**
 * Mailbox action controller
 *
 * @package Application\Controller
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN: $Rev: 13 $
 */
class MailController extends AbstractApplicationController {

	/**
	 * Alias for indexAction
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function inboxAction() {
		return $this->redirect()->toRoute( 'mail' );
	}

	/**
	 * Displays the collection of received mail in the Inbox
	 *
	 * @return \Zend\View\Model\ViewModel
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		$service = $this->getService();
		
		$retrieveMultipleRequest = $this->getInboxRetrieveRequest();
		$response = $service->retrieveMultiple( $retrieveMultipleRequest );
		$recordSet = $response->getRecordSet();
		$statusMessage = $response->getStatistics();
		
		$view = new ViewModel( array(
			'inboxClass' => ' hidden',
			'mail' => $recordSet,
			'pageTitle' => 'Inbox',
			'receiveMailClass' => '',
			'sentClass' => '',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/activity/emailIndex' );
		return $view;
	}

	/**
	 * Deletes the specified entity
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function deleteAction() {
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$action = $this->params( 'mailbox', 'index' );
		if ($id < 1) {
			return $this->redirect()->toRoute( 'mail' );
		}
		
		$target = new TargetDeleteActivity( 'Application\Model\EmailInteraction' );
		$target->setId( $id );
		$deleteRequest = new DeleteRequest();
		$deleteRequest->setTarget( $target );
		
		$service = $this->getService();
		$deleteResponse = $service->delete( $deleteRequest );
		if ($deleteResponse->getResult()) {
			return $this->redirect()->toRoute( 'mail', array(
				'action' => $action
			) );
		}
		
		$statusMessage = $deleteResponse->getMessage();
		
		$retrieveMultipleRequest = $this->getInboxRetrieveRequest();
		$response = $service->retrieveMultiple( $retrieveMultipleRequest );
		$recordSet = $response->getRecordSet();
		
		$view = new ViewModel( array(
			'mail' => $recordSet,
			'pageTitle' => 'Inbox',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/activity/emailIndex' );
		return $view;
	}

	/**
	 * Fetches mail from the specified mailbox and processes any incoming mail that
	 * matches a Contact or Lead in this system.
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function receiveAction() {
		/* @var $processMailResponse ProcessMailResponse */
		
		$service = $this->getService();
		$statusMessage = '';
		
		// Open mailbox
		$connected = false;
		try {
			$mail = $this->getServiceLocator()->get( 'mail.inbox' );
			$connected = true;
		} catch ( \Zend\Mail\Storage\Exception\RuntimeException $e ) {
			$statusMessage = 'Storage exception: ' . $e->getMessage();
		} catch ( \Zend\Mail\Storage\Exception\InvalidArgumentException $e ) {
			$statusMessage = 'Invalid Argument exception: ' . $e->getMessage();
		} catch ( \Zend\Mail\Protocol\Exception\RuntimeException $e ) {
			$statusMessage = 'Protocol exception: ' . $e->getMessage();
		} catch ( \Zend\ServiceManager\Exception\ServiceNotCreatedException $e ) {
			$statusMessage = 'ServiceManager exception: ' . $e->getMessage();
		}
		
		// Process any mail
		if ($connected) {
			$processMailRequest = new ProcessMailRequest( $mail );
			$processMailResponse = $service->execute( $processMailRequest );
			if ($processMailResponse->getResult()) {
				return $this->redirect()->toRoute( 'mail' );
			}
			$statusMessage = $processMailResponse->getMessage();
		}
		
		$retrieveMultipleRequest = $this->getInboxRetrieveRequest();
		$retrieveMultipleResponse = $service->retrieveMultiple( $retrieveMultipleRequest );
		$recordSet = $retrieveMultipleResponse->getRecordSet();
		
		$view = new ViewModel( array(
			'inboxClass' => ' hidden',
			'mail' => $recordSet,
			'pageTitle' => 'Inbox',
			'receiveMailClass' => '',
			'sentClass' => '',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/activity/emailIndex' );
		return $view;
	}

	/**
	 * Processes a reply
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function replyAction() {
		/* @var $request Request */
		$clazz = 'Application\Model\EmailInteraction';
		$owner = $this->zfcUserAuthentication()->getIdentity();
		$service = $this->getService();
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'mail' );
		}
		$originalEmail = $this->fetchEmail( $id );
		
		// Create the form
		$form = new ActivityForm( $service->getEntityManager(), $clazz );
		$reply = new EmailInteraction();
		$form->bind( $reply );
		
		// Initialize form values using original email
		$this->setReplyValues( $form, $originalEmail );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$postData = $this->fetchPostData( $request );
			$form->setData( $postData );
			if ($form->isValid()) {
				$statusMessage = $this->processMail( $reply, $form, $postData );
				$canSave = ($statusMessage == '' ? true : false);
				
				// Persist the entity and return
				if ($canSave) {
					$target = new TargetCreateActivity();
					$target->setEntity( $reply );
					$createRequest = new CreateRequest();
					$createRequest->setTarget( $target );
					$createResponse = $service->create( $createRequest );
					if ($createResponse->getResult()) {
						return $this->redirect()->toRoute( 'mail', array(
							'action' => 'sent'
						) );
					}
					$statusMessage = sprintf( self::MSG_ERROR_CREATE, $createResponse->getMessage() );
				}
			} else {
				$statusMessage = sprintf( self::MSG_INVALID_CREATE_FORM );
			}
		}
		
		$view = new ViewModel( array(
			'form' => $form,
			'id' => $id,
			'pageTitle' => $originalEmail->getSubject(),
			'type' => 'EmailInteraction',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/activity/createReplyEmail' );
		return $view;
	}

	/**
	 * Displays the collection of sent mail
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function sentAction() {
		$service = $this->getService();
		
		$retrieveMultipleRequest = $this->getSentRetrieveRequest();
		$response = $service->retrieveMultiple( $retrieveMultipleRequest );
		$recordSet = $response->getRecordSet();
		$statusMessage = $response->getStatistics();
		
		$view = new ViewModel( array(
			'inboxClass' => '',
			'mail' => $recordSet,
			'pageTitle' => 'Sent Mail',
			'receiveMailClass' => ' hidden',
			'sentClass' => ' hidden',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/activity/emailIndex' );
		return $view;
	}

	/**
	 * Fetches the specified email
	 *
	 * @param int $id
	 * @param string $clazz
	 * @return EmailInteraction
	 */
	private function fetchEmail($id) {
		// Create the receiver and command objects
		$target = new TargetRetrieveActivity( 'Application\Model\EmailInteraction' );
		$target->setId( $id );
		$retrieveRequest = new RetrieveRequest();
		$retrieveRequest->setTarget( $target );
		
		// Fetch the specified entity
		$service = $this->getService();
		$retrieveResponse = $service->retrieve( $retrieveRequest );
		return $retrieveResponse->getEntity();
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	private function fetchPostData(Request $request) {
		// Merge the form post data with any file uploads
		$post = array_merge_recursive( $request->getPost()->toArray(), $request->getFiles()->toArray() );
		
		$fieldset = EmailFieldset::FIELDSETNAME;
		$key = EmailFieldset::FILEUPLOAD;
		if (isset( $post[$fieldset][$key] )) {
			$post[$fieldset]['filename'] = $post[$fieldset][$key]['name'];
			$post[$fieldset]['filesize'] = $post[$fieldset][$key]['size'];
			$post[$fieldset]['mimetype'] = $post[$fieldset][$key]['type'];
		}
		return $post;
	}

	/**
	 * Returns a Request object to fetch the Inbox
	 *
	 * @return \Application\Service\RetrieveMultipleRequest
	 */
	private function getInboxRetrieveRequest() {
		/* @var $owner User */
		$service = $this->getService();
		$user = $this->zfcUserAuthentication()->getIdentity();
		
		// Create receiver and command objects
		$dql = "select e from Application\Model\EmailInteraction e
				where e.owner = '" . $user->getId() . "' and e.direction = 'Inbound'
				order by e.actualEnd desc";
		$target = new TargetRetrieveMultipleByQuery();
		$target->setDQL( $dql );
		$retrieveMultipleRequest = new RetrieveMultipleRequest();
		$retrieveMultipleRequest->setTarget( $target );
		return $retrieveMultipleRequest;
	}

	/**
	 * Returns a Request object to fetch the Sent mail
	 *
	 * @return \Application\Service\RetrieveMultipleRequest
	 */
	private function getSentRetrieveRequest() {
		/* @var $owner User */
		$service = $this->getService();
		$user = $this->zfcUserAuthentication()->getIdentity();
		
		// Create receiver and command objects
		$dql = "select e from Application\Model\EmailInteraction e
				where e.owner = '" . $user->getId() . "' and e.direction = 'Outbound'
				order by e.actualEnd desc";
		$target = new TargetRetrieveMultipleByQuery();
		$target->setDQL( $dql );
		$retrieveMultipleRequest = new RetrieveMultipleRequest();
		$retrieveMultipleRequest->setTarget( $target );
		return $retrieveMultipleRequest;
	}

	/**
	 * Processes an email activity by uploading any attachments and performing
	 * any send request. Returns an empty string on success and an error message on failure.
	 *
	 * @param EmailInteraction $email
	 * @param ActivityForm $form
	 * @param array $postData
	 * @return string
	 */
	private function processMail(EmailInteraction $email, ActivityForm $form, array $postData) {
		/* @var $response1 UploadActivityMimeAttachmentResponse */
		/* @var $response2 SendEmailResponse */
		
		$result = '';
		
		$email->setStatus( EmailStatus::PENDINGSEND );
		
		// Process any file uploads
		$mar = new UploadActivityMimeAttachmentRequest();
		$mar->setActivity( $email );
		$mar->setForm( $form );
		$mar->setPostData( $postData );
		
		$service = $this->getService();
		$response1 = $service->execute( $mar );
		if ($response1->getResult()) {
			$email->setStatus( EmailStatus::SENDING );
			
			// Process any send request
			$send = new SendEmailRequest();
			$send->setActivity( $email );
			$send->setIssueSend( $email->getIssueSend() );
			
			$response2 = $service->execute( $send );
			if ($response2->getResult()) {
				$email->setStatus( EmailStatus::SENT );
			} else {
				$email->setStatus( EmailStatus::FAILED );
				$result = sprintf( 'Email error: %s', $response2->getMessage() );
			}
		} else {
			$result = $response1->getMessage();
		}
		
		return $result;
	}

	/**
	 * Initialize form values for a new reply email
	 *
	 * @param ActivityForm $form
	 * @param EmailInteraction $email
	 */
	private function setReplyValues(ActivityForm $form, EmailInteraction $email) {
		/* @var $efs EmailFieldset */
		/* @var $ro Regarding */
		$efs = $form->get( EmailFieldset::FIELDSETNAME );
		$now = new \DateTime();
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		// Set One-to-Many associations
		$firstName = '';
		if ($email->getContact()) {
			$address = $email->getContact()->getEmail1();
			$efs->get( EmailFieldset::CONTACT )->setValue( $email->getContact()
				->getId() );
		} elseif ($email->getLead()) {
			$address = $email->getLead()->getEmail1();
			$efs->get( EmailFieldset::LEAD )->setValue( $email->getLead()
				->getId() );
		}
		
		// Set body
		$body = '<p></p><p>On ' .
			 $email->getFormattedActualEnd( 'M d, Y, \a\t g:i A, ' ) .
			 "<a href=\"mailto:$address\">$address</a>  wrote:</p><blockquote>" .
			 $email->getDescription() .
			 '</blockquote>';
		$signature = $owner->getEmailSignature();
		if ($signature != '') {
			$body .= '<p>' . $signature . '</p>';
		}
		$body = '<div class="non-editable" contenteditable="false">' . $body . '</div>';
		$efs->get( EmailFieldset::DESCRIPTION )->setValue( $body );
		
		// Exchange visible for hidden form elements
		$efs->remove( EmailFieldset::DIRECTION );
		$efs->add( array(
			'type' => 'Hidden',
			'name' => EmailFieldset::DIRECTION
		) );
		$efs->get( EmailFieldset::DIRECTION )->setValue( Direction::OUTBOUND );
		
		// Set other values
		$efs->get( EmailFieldset::ACTUALSTART )->setValue( $now->format( 'm/d/Y' ) );
		$efs->get( EmailFieldset::BUSINESSUNIT )->setValue( $owner->getBusinessUnit()
			->getId() );
		$efs->get( EmailFieldset::CC )->setValue( $email->getCc() );
		$efs->get( EmailFieldset::DISCRIMINATOR )->setValue( 'EmailInteraction' );
		$efs->get( EmailFieldset::FROM )->setValue( $owner->getEmail() );
		$efs->get( EmailFieldset::MESSAGEID )->setValue( $email->getMessageId() );
		$efs->get( EmailFieldset::OWNER )->setValue( $owner->getId() );
		$efs->get( EmailFieldset::PRIORITY )->setValue( ActivityPriority::NORMAL );
		$efs->get( EmailFieldset::SCHEDULEDEND )->setValue( $now->format( 'm/d/Y' ) );
		$efs->get( EmailFieldset::STATUS )->setValue( EmailStatus::DRAFT );
		$efs->get( EmailFieldset::SUBJECT )->setValue( 'Re: ' . $email->getSubject() );
		$efs->get( EmailFieldset::TO )->setValue( $address );
	}
}
?>