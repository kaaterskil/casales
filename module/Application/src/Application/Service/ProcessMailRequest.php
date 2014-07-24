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
 * @version     SVN $Id: ProcessMailRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\ActivityState;
use Application\Model\Attachment;
use Application\Model\Direction;
use Application\Model\EmailStatus;
use Application\Model\EmailInteraction;
use Application\Model\User;
use Application\Model\Regarding;
use Application\Service\ProcessMailResponse;
use Application\Service\Request;
use Application\Service\Exception\FilesystemException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Zend\Mail\Headers;
use Zend\Mail\Header\GenericHeader;
use Zend\Mail\Header\Received;
use Zend\Mail\Storage\AbstractStorage;
use Zend\Mail\Storage\Imap;
use Zend\Mail\Storage\Message as MailMessage;
use Zend\Mail\Storage\Part;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\RendererInterface;
use Zend\File\Transfer\Adapter\Http;
use Application\Model\Contact;
use Application\Model\Lead;

/**
 * ProcessMailRequest Class
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: ProcessMailRequest.php 13 2013-08-05 22:53:55Z  $
 */
class ProcessMailRequest extends Request {

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * An array of contacts or leads that match message senders
	 * @var array
	 */
	private $entities = array();

	/**
	 * @var AbstractStorage
	 */
	private $mail;

	/**
	 * An array of mail messages
	 * @var array
	 */
	private $mailStore = array();

	/**
	 * @var User
	 */
	private $user;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 *
	 * @param AbstractStorage $mail
	 */
	public function __construct(AbstractStorage $mail) {
		parent::__construct( 'ProcessMailRequest' );
		$this->mail = $mail;
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
	 * @return \Application\Service\ProcessMailRespons
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		$response = new ProcessMailResponse();
		
		if ($this->getEntityManager() == null) {
			$response->setMessage( 'No EntityManager provided.' );
			return $response;
		}
		if ($this->mail == null) {
			$response->setMessage( 'No mail storage object provided.' );
			return $response;
		}
		
		$this->fetchMail();
		if (!count( $this->mailStore )) {
			$response->setMessage( 'No new mail.' );
			return $response;
		}
		
		$found = $this->fetchContactOrLead();
		if (!$found) {
			$response->setMessage( 'No mail received matching existing Contacts or Leads.' );
			return $response;
		}
		
		try {
			$this->processMail();
			
			$response->setResult( true );
			$response->setMessage( 'Mail Processing successful.' );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * Retrieves contacts or leads that match the message sender
	 *
	 * @return boolean
	 * 		TRUE if matches have been found, FALSE if mail contains no matches
	 */
	private function fetchContactOrLead() {
		/* @var $message MailMessage */
		$queries = array();
		
		// Loop through each mail item and construct a DQL query to match a Contact with
		// the message sender. First test for an email address. If none found, build the
		// query around the sender name. Then create another query for a Lead.
		foreach ( $this->mailStore as $messageNum => $message ) {
			$dql = '';
			$from = $message->from;
			$found1 = preg_match( '/<(.*)>/', $from, $matches );
			if ($found1) {
				$address = strtolower( $matches[1] );
				$dql = "select o from Application\Model\Contact o where (lower(o.email1) = '" .
					 $address .
					 "' or lower(o.email2) = '" .
					 $address .
					 "')";
			} else {
				$found2 = preg_match( '/^(.*?)</', strtolower( $from ), $matches );
				if ($found2) {
					$words = preg_split( '/\s/', trim( $matches[1] ) );
					$criteria = '';
					foreach ( $words as $word ) {
						$criteria .= " and lower(o.displayName) like '%" . trim( $word ) . "%'";
					}
					$criteria = substr( $criteria, 5 );
					$dql = "select o from Application\Model\Contact o where (" . $criteria . ")";
				}
			}
			
			if ($found1 || $found2) {
				$queries[$messageNum] = array(
					'contactDql' => $dql,
					'leadDql' => preg_replace( '/Contact/', 'Lead', $dql )
				);
			}
		}
		
		// Fetch the contact or lead
		$em = $this->getEntityManager();
		foreach ( $queries as $messageNum => $query ) {
			$entity = null;
			
			// First search for a matching lead
			$q = $em->createQuery( $query['leadDql'] );
			$recordSet = $q->getResult();
			if (count( $recordSet )) {
				$this->entities[$messageNum] = $recordSet[0];
				continue;
			}
			
			// If no lead is found, search for a contact
			$q = $em->createQuery( $query['contactDql'] );
			$recordSet = $q->getResult();
			if (count( $recordSet )) {
				$this->entities[$messageNum] = $recordSet[0];
			}
		}
		
		if (count( $this->entities )) {
			return true;
		}
		return false;
	}

	/**
	 * Fetches mail and places messages in the registry
	 */
	private function fetchMail() {
		if ($this->mail instanceof Imap) {
			$folder = $this->mail->getFolders()->INBOX;
			$this->mail->selectFolder( $folder );
		}
		
		foreach ( $this->mail as $messageNum => $message ) {
			$this->mailStore[$messageNum] = $message;
		}
	}

	/**
	 * Process mail
	 *
	 * @throws FilesystemException
	 */
	private function processMail() {
		/* @var $message MailMessage */
		$now = new \DateTime();
		$em = $this->getEntityManager();
		
		foreach ( $this->mailStore as $messageNum => $message ) {
			// Test if matching contact or lead exists, otherwise skip
			if (!isset( $this->entities[$messageNum] )) {
				continue;
			}
			
			$headers = $message->getHeaders();
			
			// Parse headers
			$charset = '';
			$contentType = '';
			$deliveryDate = new \DateTime();
			$encoding = '';
			$messageId = '';
			if ($headers->has( 'Content-Type' )) {
				$contentType = strtok( $message->contentType, ';' );
				$found = preg_match( '/charset="(.*)"/', $message->contentType, $matches );
				if ($found) {
					$charset = $matches[1];
				}
			}
			if ($headers->has( 'Delivery-Date' )) {
				$deliveryDate = new \DateTime( $message->deliveryDate );
			} elseif ($headers->has( 'Date' )) {
				$deliveryDate = new \DateTime( $message->date );
			}
			if ($headers->has( 'Content-Transfer-Encoding' )) {
				$encoding = $message->contentTransferEncoding;
			}
			if ($headers->has( 'Message-ID' )) {
				$messageId = $message->messageId;
			}
			$from = $message->from;
			$subject = $message->subject;
			$to = $message->to;
			
			// Test if message has already been processed. Skip if true.
			$phrase = ($messageId != '' ? " e.messageId = '" . $messageId . "' and" : '');
			$dql = "select e from Application\Model\EmailInteraction e where" .
				 $phrase .
				 " e.actualEnd='" .
				 $deliveryDate->format( 'Y-m-d H:i:s' ) .
				 "'";
			$q = $em->createQuery( $dql );
			$recordSet = $q->getResult();
			if (count( $recordSet )) {
				continue;
			}
			
			// Parse message body
			$content = '';
			$attachments = array();
			try {
				if ($message->isMultipart()) {
					$content = $this->processPart( $message, 'text/html' );
					$attachments = $this->processAttachment( $message );
				} else {
					$content = $this->decode( $message->getContent(), $charset, $encoding );
				}
			} catch ( FilesystemException $e ) {
				throw $e;
			}
			
			// Create the email object
			$email = new EmailInteraction();
			$email->setActualEnd( $deliveryDate );
			$email->setActualStart( $deliveryDate );
			$email->setBusinessUnit( $this->user->getBusinessUnit() );
			$email->setCreationDate( $now );
			$email->setDescription( $content );
			$email->setDirection( Direction::INBOUND );
			$email->setFrom( $from );
			$email->setLastUpdateDate( $now );
			$email->setMessageId( $messageId );
			$email->setMimetype( $contentType );
			$email->setOwner( $this->user );
			$email->setState( ActivityState::COMPLETED );
			$email->setStatus( EmailStatus::RECEIVED );
			$email->setSubject( $subject );
			$email->setTo( $to );

			// Create association to attachments
			if (count( $attachments )) {
				foreach ( $attachments as $attachment ) {
					$email->addAttachment( $attachment );
				}
			}

			// Create associations to parent
			$entity = $this->entities[$messageNum];
			if ($entity instanceof Contact) {
				$contact = $entity;
				$contact->addActivity( $email );
			} elseif ($entity instanceof Lead) {
				$lead = $entity;
				$lead->addInteraction( $email );
			}
			
			// Persist
			$em->persist( $email );
		}
		
		$em->flush();
	}

	/**
	 * Process attachments
	 *
	 * @param Part $message
	 * @param int $attachmentNum
	 * @throws FilesystemException
	 * @return array:\Application\Model\Attachment
	 */
	private function processAttachment(Part $message, $attachmentNum = 0) {
		/* @var $part Part */
		/* @var $renderer RendererInterface */
		
		$result = array();
		
		if ($message->isMultipart()) {
			foreach ( new \RecursiveIteratorIterator( $message ) as $part ) {
				$attachments = $this->processAttachment( $part, count( $result ) );
				if (count( $attachments )) {
					$result = array_merge( $result, $attachments );
				}
			}
		} else {
			// Test for header
			if ($message->getHeaders()->has( 'Content-Disposition' )) {
				$filename = '';
				$presesntation = strtok( $message->contentDisposition, ';' );
				if ($presesntation == 'attachment') {
					$filename = '';
					$found = preg_match( '/filename="(.*)"/', strtok( ';' ), $matches );
					if ($found) {
						$filename = trim( $matches[1], ' "' );
					}
				}
				if ($filename != '') {
					// Fetch headers
					$mimetype = '';
					$encoding = '';
					if ($message->getHeaders()->has( 'Content-Type' )) {
						$mimetype = strtok( $message->contentType, ';' );
					}
					if ($message->getHeaders()->has( 'Content-Transfer-Encoding' )) {
						$encoding = $message->contentTransferEncoding;
					}
					
					// decode the data
					if ($encoding = 'base64') {
						$data = base64_decode( $message->getContent() );
					} else {
						$data = convert_uudecode( $message->getContent() );
					}
					
					// Write the attachment to the file system
					$filepath = 'data/uploads/' . $filename;
					if (!$fh = fopen( $filepath, 'wb+' )) {
						throw new FilesystemException( 'Cannot open filepath "' . $filepath . '"' );
					}
					$bytesWritten = fwrite( $fh, $data );
					if ($bytesWritten === false) {
						throw new FilesystemException( 'Cannot write to file "' . $filepath . '"' );
					}
					fclose( $fh );
					
					// Create the attachment object
					$now = new \DateTime();
					$attachment = new Attachment();
					$attachment->setAttachmentNumber( ++$attachmentNum );
					$attachment->setCreationDate( $now );
					$attachment->setFilename( $filename );
					$attachment->setFilesize( $bytesWritten );
					$attachment->setLastUpdateDate( $now );
					$attachment->setMimetype( $mimetype );
					$attachment->setSubject( $filename );
					
					$result[] = $attachment;
				}
			}
		}
		
		return $result;
	}

	/**
	 * Parses a multipart message recursively.
	 *
	 * @param Part $message The multipart message
	 * @param string $type The specified content-type to parse
	 * @return string
	 */
	private function processPart(Part $message, $type = null) {
		$content = '';
		$contentType = '';
		$encoding = '';
		$charset = '';
		
		if ($message->isMultipart()) {
			foreach ( new \RecursiveIteratorIterator( $message ) as $part ) {
				$content .= $this->processPart( $part, $type );
			}
			// If the specified content type is not found, fall back to 'text/plain'
			if ($content == '') {
				foreach ( new \RecursiveIteratorIterator( $message ) as $part ) {
					$content = $this->processPart( $part, 'text/plain' );
				}
			}
		} else {
			if ($message->getHeaders()->has( 'Content-Type' )) {
				$contentType = strtok( $message->contentType, ';' );
				$found = preg_match( '/charset="(.*)"/', $message->contentType, $matches );
				if ($found) {
					$charset = $matches[1];
				}
			}
			if ($message->getHeaders()->has( 'Content-Transfer-Encoding' )) {
				$encoding = $message->contentTransferEncoding;
			}
			
			if (($type != null) && ($contentType == $type)) {
				$content = $this->decode( $message->getContent(), $charset, $encoding );
			}
		}
		
		return $content;
	}

	/**
	 * Decodes the message content
	 *
	 * @param string $str
	 * @param string $charset
	 * @param string $encoding
	 * @return string
	 */
	private function decode($str, $charset, $encoding) {
		$result = $str;
		if ($encoding == 'base64') {
			$result = base64_decode( $str );
		} elseif ($encoding == 'quoted-printable') {
			$result = quoted_printable_decode( $str );
		}
		
		if ($charset != 'utf-8' && $charset != 'us-ascii') {
			$result = utf8_decode( $result );
		}
		
		return $result;
	}
}
?>