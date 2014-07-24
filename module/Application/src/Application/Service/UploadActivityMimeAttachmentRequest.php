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
 * @version     SVN $Id: UploadActivityMimeAttachmentRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Form\EmailFieldset;
use Application\Model\AbstractActivity;
use Application\Model\Attachment;
use Application\Model\EmailInteraction;
use Application\Service\Request;
use Application\Service\UploadActivityMimeAttachmentResponse;
use Zend\Form\Form;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\Size;

/**
 * Contains the data needed to upload an activity MIME attachment (e-mail attachment).
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: UploadActivityMimeAttachmentRequest.php 13 2013-08-05 22:53:55Z  $
 */
class UploadActivityMimeAttachmentRequest extends Request {

	/**
	 * @var AbstractActivity
	 */
	private $activity;

	/**
	 * @var Form
	 */
	private $form;

	/**
	 * @var array
	 */
	private $postData = array();
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'UploadActivityMimeAttachmentRequest' );
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\AbstractActivity
	 */
	public function getActivity() {
		return $this->activity;
	}

	/**
	 * @param AbstractActivity $activity
	 */
	public function setActivity(AbstractActivity $activity) {
		$this->activity = $activity;
	}

	/**
	 * @return \Zend\Form\Form
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * @param Form $form
	 */
	public function setForm(Form $form) {
		$this->form = $form;
	}

	/**
	 * @return array
	 */
	public function getPostData() {
		return $this->postData;
	}

	/**
	 * @param array $postData
	 */
	public function setPostData(array $postData) {
		$this->postData = $postData;
	}

	/**
	 * @return \Application\Service\UploadActivityMimeAttachmentResponse
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		$response = new UploadActivityMimeAttachmentResponse();
		
		$activity = $this->getActivity();
		if ($activity == null) {
			$response->setMessage( 'No email found to attach the upload.' );
			return $response;
		} elseif (!$activity instanceof EmailInteraction) {
			// Wrong activity
			$response->setResult( true );
			return $response;
		}
		
		$postData = $this->getPostData();
		if (!count( $postData )) {
			$response->setMessage( 'No POST data to process.' );
			return $response;
		}
		
		$form = $this->getForm();
		if ($form == null) {
			$response->setMessage( 'No upload form found.' );
			return $response;
		}
		
		$fieldset = EmailFieldset::FIELDSETNAME;
		$key = EmailFieldset::FILEUPLOAD;
		if (isset( $postData[$fieldset][$key] ) && ($postData[$fieldset][$key]['tmp_name'] != '')) {
			
			// Set maximum file size at 20MB
			$validators = array(
				'max' => new Size( 20000000 )
			);
			
			// Test and transfer file upload to upload directory
			$adapter = new Http();
			$adapter->setValidators( $validators, $postData[$fieldset]['filename'] );
			if (!$adapter->isValid()) {
				$dataError = $adapter->getMessages();
				$errors = array();
				foreach ( $dataError as $errorKey => $errorValue ) {
					$errors[] = $errorValue;
				}
				$form->setMessages( array(
					$key => $errors
				) );
				return $response;
			}
			
			$filename = $postData[$fieldset]['filename'];
			$filesize = $postData[$fieldset]['filesize'];
			$mimetype = $postData[$fieldset]['mimetype'];
			$destination = 'data/uploads';
			
			try {
				// Transfer the file to the upload directory
				$adapter->setDestination( $destination );
				$success = $adapter->receive( $filename );
				if (!$success) {
					$response->setMessage( 'Application Error: File upload failed.' );
					return $response;
				}
				
				// Create an attachment record and associate it with the activity
				$now = new \DateTime();
				$attachment = new Attachment();
				$attachment->setCreationDate( $now );
				$attachment->setFilename( $destination . '/' . $filename );
				$attachment->setFilesize( $filesize );
				$attachment->setLastUpdateDate( $now );
				$attachment->setMimetype( $mimetype );
				$attachment->setSubject( $filename );
				
				$activity->addAttachment( $attachment );
				
				// Wrap up
				$response->setResult( true );
				$response->setMessage( 'File upload successful.' );
			} catch ( Exception $e ) {
				$response->setMessage( $e->getMessage() );
			}
		} else {
			$response->setMessage( 'No files to upload.' );
			$response->setResult( true );
			return $response;
		}
		
		return $response;
	}
}
?>