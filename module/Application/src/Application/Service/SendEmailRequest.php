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
 * @version     SVN $Id: SendEmailRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\ActivityPriority;
use Application\Model\Attachment;
use Application\Model\EmailInteraction;
use Application\Model\User;
use Application\Service\Request;
use Application\Service\SendEmailResponse;
use Doctrine\ORM\EntityManager;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Headers;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

/**
 * Contains the data that is needed to send an email message.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: SendEmailRequest.php 13 2013-08-05 22:53:55Z  $
 */
class SendEmailRequest extends Request {

	/**
	 * @var SmtpTransport
	 */
	private $transport;

	/**
	 * @var EmailInteraction
	 */
	private $activity;

	/**
	 * TRUE is the email should be sent, FALSE just record it as sent
	 * @var boolean
	 */
	private $issueSend = false;

	/**
	 * @var User
	 */
	private $user;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'SendEmailRequest' );
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Zend\Mail\Transport\Smtp
	 */
	public function getTransport() {
		return $this->transport;
	}

	public function setTransport(SmtpTransport $transport) {
		$this->transport = $transport;
	}

	/**
	 * @return \Application\Model\EmailInteraction
	 */
	public function getActivity() {
		return $this->activity;
	}

	public function setActivity(EmailInteraction $activity) {
		$this->activity = $activity;
	}

	/**
	 * @return boolean
	 */
	public function getIssueSend() {
		return $this->issueSend;
	}

	public function setIssueSend($issueSend) {
		$this->issueSend = (bool) $issueSend;
	}

	/**
	 * @return \Application\Model\User
	 */
	public function getUser() {
		return $this->user;
	}

	public function setUser(User $user) {
		$this->user = $user;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\SendEmailResponse
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		/* @var $attachment Attachment */
		$response = new SendEmailResponse();
		
		// Fetch transport - this is an SMTP transport configured by a factory method in
		// the module manager and injected by the service. The configuration is found in
		// the local.php file
		$transport = $this->getTransport();
		if ($transport == null) {
			$response->setMessage( 'No transport found.' );
			return $response;
		}
		
		// Fetch the email activity record
		if ($this->getActivity() == null) {
			$response->setMessage( 'No email record found to send' );
			return $response;
		}
		
		if ($this->getActivity()->getTo() == '') {
			$response->setMessage( 'No recipients found.' );
			return $response;
		}
		
		// Fetch the user to set the From and Reply-To parameters
		if ($this->getUser() == null) {
			$response->setMessage( 'No user found.' );
			return $response;
		}
		
		// Fetch the user's email address
		if ($this->getUser()->getEmail() == '') {
			$response->setMessage( 'No user email address.' );
			return $response;
		}
		
		// Execute the transport.send() method
		$activity = $this->getActivity();
		if (!$this->getIssueSend()) {
			try {
				// Build message
				$message = $this->buildMessage();
				
				// Execute
				$start = new \DateTime();
				$transport->send( $message );
				$end = new \DateTime();
				
				// Wrap up
				$activity->setSendStartDate( $start );
				$activity->setSendEndDate( $end );
				
				$response->setResult( true );
				$response->setMessage( 'Email sent successfully.' );
			} catch ( Exception $e ) {
				$response->setMessage( $e->getMessage() );
			}
		} else {
			$now = new \DateTime();
			$activity->setSendStartDate( $now );
			$activity->setSendEndDate( $now );
			
			$response->setResult( true );
			$response->setMessage( 'Send email bypassed.' );
		}
		return $response;
	}

	/**
	 * Creates a Message object from the given parameters
	 * @return \Zend\Mail\Message
	 */
	protected function buildMessage() {
		/* @var $attachment Attachment */
		$user = $this->getUser();
		$from = $user->getEmail();
		
		$activity = $this->getActivity();
		$activity->setFrom( $user->getEmail() );
		
		$message = new Message();
		
		// Set addresses
		$message->addTo( $activity->getTo() )
			->addFrom( $from )
			->addReplyTo( $from );
		
		if ($activity->getCc() != '') {
			$message->addCc( $activity->getCc() );
		}
		if ($activity->getBcc() != '') {
			$message->addBcc( $activity->getBcc() );
		}
		
		// Set subject
		$message->setSubject( $activity->getSubject() );
		
		// Set body, including any multipart content
		$parts = array();
		
		$text = new MimePart( $activity->getDescription() );
		$text->type = 'text/html';
		$parts[] = $text;
		
		if ($activity->getAttachments()->count()) {
			foreach ( $activity->getAttachments() as $attachment ) {
				// http://stackoverflow.com/questions/17066078/send-email-with-attached-files-in-zf2
				$part = new MimePart( file_get_contents( $attachment->getFilename() ) );
				$part->type = $attachment->getMimetype() . '; name=' . $attachment->getSubject();
				$part->encoding = 'base64';
				$part->disposition = 'attachment';
				$part->filename = $attachment->getSubject();
				$parts[] = $part;
			}
		}
		
		$body = new MimeMessage();
		$body->setParts( $parts );
		$message->setBody( $body );
		
		// Set requests
		if ($activity->getDeliveryReceiptRequested()) {
			$message->getHeaders()->addHeaderLine( 'Return-Receipt-To', $from );
		}
		if ($activity->getReadReceiptRequested()) {
			$message->getHeaders()->addHeaderLine( 'Disposition-Notification-To', $from );
		}
		
		// Set priority
		if ($activity->getPriority() == ActivityPriority::HIGH) {
			$message->getHeaders()->addHeaderLine( 'X-Priority', '1 (Highest)' );
		}
		
		return $message;
	}
}
?>