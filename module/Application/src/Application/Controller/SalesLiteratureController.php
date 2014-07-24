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
 * @version     SVN $Id: SalesLiteratureController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Controller\AbstractApplicationController;
use Application\Form\SalesLiteratureItemFieldset;
use Application\Form\SalesLiteratureItemForm;
use Application\Form\SalesLiteratureFieldset;
use Application\Form\SalesLiteratureForm;
use Application\Model\LiteratureType;
use Application\Model\SalesLiterature;
use Application\Model\SalesLiteratureItem;
use Application\Model\User;
use Application\Service\CreateRequest;
use Application\Service\CreateResponse;
use Application\Service\DeleteRequest;
use Application\Service\DeleteResponse;
use Application\Service\FindByCriteria;
use Application\Service\RetrieveMultipleRequest;
use Application\Service\RetrieveMultipleResponse;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\TargetCreateSalesLiterature;
use Application\Service\TargetCreateSalesLiteratureItem;
use Application\Service\TargetDeleteSalesLiterature;
use Application\Service\TargetDeleteSalesLiteratureItem;
use Application\Service\TargetRetrieveSalesLiterature;
use Application\Service\TargetRetrieveSalesLiteratureItem;
use Application\Service\TargetRetrieveMultipleSalesLiterature;
use Application\Service\TargetUpdateSalesLiterature;
use Application\Service\TargetUpdateSalesLiteratureItem;
use Application\Service\UpdateRequest;
use Application\Service\UpdateResponse;
use Doctrine\ORM\EntityManager;
use Zend\File\Transfer\Adapter\Http;
use Zend\Form\Fieldset;
use Zend\Http\Request;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;

/**
 * Sales Literature action controller
 *
 * @package Application\Controller
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN: $Rev: 13 $
 */
class SalesLiteratureController extends AbstractApplicationController {

	/**
	 * Retrieves a collection of sales literature records
	 *
	 * @return \Zend\View\Model\ViewModel
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		/* @var $response RetrieveMultipleResponse */
		$owner = $this->zfcUserAuthentication()->getIdentity();
		$service = $this->getService();
		
		// Create receiver and command objects
		$criteria = new FindByCriteria();
		$criteria->setOrderBy( array(
			'name' => 'asc'
		) );
		$target = new TargetRetrieveMultipleSalesLiterature();
		$target->setCriteria( $criteria );
		$retrieve = new RetrieveMultipleRequest();
		$retrieve->setTarget( $target );
		
		// Fetch records
		$start = microtime( true );
		$response = $service->execute( $retrieve );
		$recordSet = $response->getRecordSet();
		$end = microtime( true );
		
		// Compute statistics
		$elapsedTime = $end - $start;
		$numRecords = count( $recordSet );
		$statusMessage = sprintf( self::MSG_STATISTICS, $numRecords, $elapsedTime );
		
		$view = new ViewModel( array(
			'pageTitle' => 'All Sales Literature',
			'recordSet' => $recordSet,
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/salesLiterature/index' );
		return $view;
	}

	/**
	 * Creates a sales literature record
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createAction() {
		/* @var $request Request */
		/* @var $success CreateResponse */
		
		$service = $this->getService();
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Create the form and bind an empty entity to it
		$form = new SalesLiteratureForm( $service->getEntityManager() );
		$entity = new SalesLiterature();
		$form->bind( $entity );
		$this->initializeFormValues( $form );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the entity and return
				$target = new TargetCreateSalesLiterature();
				$target->setEntity( $entity );
				$create = new CreateRequest();
				$create->setTarget( $target );
				
				$success = $service->execute( $create );
				if ($success->getResult()) {
					$isRedirect = $request->getPost(SalesLiteratureForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute( 'salesLiterature', array(
							'action' => 'index'
						) );
					} else {
						return $this->redirect()->toRoute( 'salesLiterature', array(
							'action' => 'edit',
							'id' => $success->getId()
						) );
					}
				} else {
					$statusMessage = sprintf( self::MSG_ERROR_CREATE, $success->getMessage() );
				}
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		
		$view = new ViewModel( array(
			'form' => $form,
			'pageTitle' => 'New Literature',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/salesLiterature/create' );
		return $view;
	}

	/**
	 * Updates a sales literature record
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		/* @var $entity SalesLiterature */
		/* @var $request Request */
		
		$service = $this->getService();
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$front = $this->params( 'front', 'tab-1' );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'campaign', array(
				'action' => 'index'
			) );
		}
		
		// Create the form and bind the specified entity to it
		$form = new SalesLiteratureForm( $service->getEntityManager() );
		$entity = $this->fetchEntity( $id );
		$form->bind( $entity );
		$this->setFormValues( $form, $entity );
		$statusMessage = sprintf( self::MSG_STATUS, 'Existing' );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData( $request->getPost() );
			if ($form->isValid()) {
				// Persist the entity and return
				$target = new TargetUpdateSalesLiterature();
				$target->setEntity( $entity );
				$update = new UpdateRequest();
				$update->setTarget( $target );
				
				$success = $service->execute( $update );
				if ($success->getResult()) {
					$isRedirect = $request->getPost(SalesLiteratureForm::SUBMITCLOSE, false);
					if($isRedirect) {
						return $this->redirect()->toRoute( 'salesLiterature', array(
							'action' => 'index'
						) );
					}
					$statusMessage = $success->getMessage();
				} else {
					$statusMessage = sprintf( self::MSG_ERROR_UPDATE, $success->getMessage() );
				}
			} else {
				$statusMessage = self::MSG_INVALID_UPDATE_FORM;
			}
		}
		
		// Set view parameters
		$attachments = $entity->getSalesLiteratureItems()->toArray();
		
		$view = new ViewModel( array(
			'id' => $id,
			'attachments' => $attachments,
			'form' => $form,
			'front' => $front,
			'pageTitle' => $entity->getName(),
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/salesLiterature/edit' );
		return $view;
	}

	/**
	 * Removes a sales literature record
	 *
	 * @return \Zend\Stdlib\ResponseInterface>
	 */
	public function deleteAction() {
		/* @var $success DeleteResponse */
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'salesLiterature', array(
				'action' => 'index'
			) );
		}
		
		// Create receiver and command objects
		$target = new TargetDeleteSalesLiterature();
		$target->setId( $id );
		$delete = new DeleteRequest();
		$delete->setTarget( $target );
		
		// Execute end return
		$service = $this->getService();
		$success = $service->execute( $delete );
		if ($success->getResult()) {
			$statusMessage = self::MSG_DELETE_SUCCESS;
		} else {
			$statusMessage = sprintf( self::MSG_ERROR_DELETE, $success->getMessage() );
		}
		return $this->redirect()->toRoute( 'salesLiterature', array(
			'action' => 'index'
		) );
	}

	/**
	 * Creates a sales literature item record
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createItemAction() {
		/* @var $entity SalesLiteratureItem */
		$service = $this->getService();
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'campaign', array(
				'action' => 'index'
			) );
		}
		$statusMessage = self::MSG_NEW_RECORD;
		
		// Create the form and bind an empty entity to it
		$form = new SalesLiteratureItemForm( $service->getEntityManager() );
		$entity = new SalesLiteratureItem();
		$form->bind( $entity );
		
		$parent = $this->fetchEntity( $id );
		$parent->addSalesLiteratureitem( $entity );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			// Merge the form and file upload data arrays
			$post = array_merge_recursive( $request->getPost()->toArray(), $request->getFiles()->toArray() );
			
			// Test for file upload and re-assign data
			$fieldsetName = SalesLiteratureItemFieldset::FIELDSETNAME;
			$key = SalesLiteratureItemFieldset::FILEUPLOAD;
			if (isset( $post[$fieldsetName][$key] )) {
				$post[$fieldsetName]['filename'] = $post[$fieldsetName][$key]['name'];
				$post[$fieldsetName]['filesize'] = $post[$fieldsetName][$key]['size'];
				$post[$fieldsetName]['filetype'] = $post[$fieldsetName][$key]['type'];
				$post[$fieldsetName]['mimetype'] = $post[$fieldsetName][$key]['type'];
			}
			
			$form->setData( $post );
			if ($form->isValid()) {
				$isValid = true;
				
				// Process a file upload
				if (isset( $post[$fieldsetName][$key] )) {
					// Set maximum file size at 20MB
					$validators = array(
						'max' => new Size( 20000000 )
					);
					
					// Test and transfer file upload to upload directory
					$adapter = new Http();
					$adapter->setValidators( $validators, $post[$fieldsetName]['filename'] );
					if (!$adapter->isValid()) {
						$isValid = false;
						$dataError = $adapter->getMessages();
						$error = array();
						foreach ( $dataError as $errorKey => $errorRow ) {
							$error[] = $errorRow;
						}
						$form->setMessages( array(
							SalesLiteratureItemFieldset::FILEUPLOAD => $error
						) );
						$statusMessage = sprintf( self::MSG_ERROR_CREATE, implode( '; ', $error ) );
					} else {
						$adapter->setDestination( 'data/uploads' );
						$adapter->receive( $post[$fieldsetName]['filename'] );
						$entity->setDocumentUrl( 'data/uploads/' . $entity->getFilename() );
					}
				}
				
				// Persist the entity and return
				if ($isValid) {
					$target = new TargetCreateSalesLiteratureItem();
					$target->setEntity( $entity );
					$create = new CreateRequest();
					$create->setTarget( $target );
					
					$success = $service->execute( $create );
					if ($success->getResult()) {
						return $this->redirect()->toRoute( 'salesLiterature', array(
							'action' => 'edit',
							'id' => $id,
							'front' => 'tab-2'
						) );
					}
					$statusMessage = sprintf( self::MSG_ERROR_CREATE, $success->getMessage() );
				}
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		
		$view = new ViewModel( array(
			'form' => $form,
			'id' => $id,
			'pageTitle' => 'New Attachment',
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/salesLiterature/createItem' );
		return $view;
	}

	/**
	 * Updates a sales literature item record
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editItemAction() {
		/* @var $entity SalesLiteratureItem */
		$service = $this->getService();
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$itemId = $this->params( 'itemId', 0 );
		if (empty( $id ) || empty( $itemId )) {
			return $this->redirect()->toRoute( 'campaign', array(
				'action' => 'index'
			) );
		}
		$statusMessage = 'Status: Existing Record';
		
		// Create the form and bind an empty entity to it
		$form = new SalesLiteratureItemForm( $service->getEntityManager() );
		$entity = $this->fetchItem( $itemId );
		$form->bind( $entity );
		$form->get( SalesLiteratureItemForm::SUBMIT )->setValue( 'Save' );
		
		$parent = $this->fetchEntity( $id );
		$parent->addSalesLiteratureitem( $entity );
		
		// Process a form request
		$request = $this->getRequest();
		if ($request->isPost()) {
			// Merge the form and file upload data arrays
			$post = array_merge_recursive( $request->getPost()->toArray(), $request->getFiles()->toArray() );
			
			// Test for file upload and re-assign data
			$fieldsetName = SalesLiteratureItemFieldset::FIELDSETNAME;
			$key = SalesLiteratureItemFieldset::FILEUPLOAD;
			if (isset( $post[$fieldsetName][$key] )) {
				$post[$fieldsetName]['filename'] = $post[$fieldsetName][$key]['name'];
				$post[$fieldsetName]['filesize'] = $post[$fieldsetName][$key]['size'];
				$post[$fieldsetName]['filetype'] = $post[$fieldsetName][$key]['type'];
				$post[$fieldsetName]['mimetype'] = $post[$fieldsetName][$key]['type'];
			}
			
			$form->setData( $post );
			if ($form->isValid()) {
				$isValid = true;
				
				// Process a file upload
				if (isset( $post[$fieldsetName][$key] )) {
					// Set maximum file size at 20MB
					$validators = array(
						'max' => new Size( 20000000 )
					);
					
					// Test and transfer file upload to upload directory
					$adapter = new Http();
					$adapter->setValidators( $validators, $post[$fieldsetName]['filename'] );
					if (!$adapter->isValid()) {
						$isValid = false;
						$dataError = $adapter->getMessages();
						$error = array();
						foreach ( $dataError as $errorKey => $errorRow ) {
							$error[] = $errorRow;
						}
						$form->setMessages( array(
							SalesLiteratureItemFieldset::FILEUPLOAD => $error
						) );
						$statusMessage = sprintf( self::MSG_ERROR_UPDATE, implode( '; ', $error ) );
					} else {
						$adapter->setDestination( 'data/uploads' );
						$adapter->receive( $post[$fieldsetName]['filename'] );
						$entity->setDocumentUrl( 'data/uploads/' . $entity->getFilename() );
					}
				}
				
				// Persist the entity and return
				if ($isValid) {
					$target = new TargetUpdateSalesLiteratureItem();
					$target->setEntity( $entity );
					$create = new UpdateRequest();
					$create->setTarget( $target );
					
					$success = $service->execute( $create );
					if ($success->getResult()) {
						return $this->redirect()->toRoute( 'salesLiterature', array(
							'action' => 'edit',
							'id' => $id
						) );
					}
					$statusMessage = sprintf( self::MSG_ERROR_UPDATE, $success->getMessage() );
				}
			} else {
				$statusMessage = self::MSG_INVALID_CREATE_FORM;
			}
		}
		
		// Set view parameters
		$filename = '';
		$documentUrl = '';
		if ($entity->getFilename()) {
			$filename = $entity->getFilename() . ' (' . number_format( $entity->getFilesize(), 0 ) . ' bytes)';
			$documentUrl = $entity->getDocumentUrl();
		}
		$hasAttachment = $entity->getDocumentUrl() ? true : false;
		
		$view = new ViewModel( array(
			'documentUrl' => $documentUrl,
			'filename' => $filename,
			'form' => $form,
			'hasAttachment' => $hasAttachment,
			'id' => $id,
			'itemId' => $itemId,
			'pageTitle' => $entity->getTitle(),
			'statusMessage' => $statusMessage
		) );
		$view->setTemplate( 'application/salesLiterature/editItem' );
		return $view;
	}

	public function deleteItemAction() {
		/* @var $response DeleteResponse */
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$itemId = $this->params( 'itemId', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'salesLiterature', array(
				'action' => 'index'
			) );
		} elseif (empty( $itemId )) {
			return $this->redirect()->toRoute( 'salesLiterature', array(
				'action' => 'edit',
				'id' => $id
			) );
		}
		$target = new TargetDeleteSalesLiteratureItem();
		$target->setId( $itemId );
		$delete = new DeleteRequest();
		$delete->setTarget( $target );
		
		$service = $this->getService();
		$response = $service->execute( $delete );
		return $this->redirect()->toRoute( 'campaign', array(
			'action' => 'edit',
			'id' => $id
		) );
	}
	
	/**
	 * Removes an uploaded file from an existing sales literature item record
	 *
	 * @return \Zend\Mvc\Controller\Plugin\Redirect
	 */
	public function deleteAttachmentFileAction() {
		/* @var $response DeleteResponse */
		
		// Fetch and test parameters
		$id = $this->params( 'id', 0 );
		$itemId = $this->params( 'itemId', 0 );
		if (empty( $id )) {
			return $this->redirect()->toRoute( 'salesLiterature', array(
				'action' => 'index'
			) );
		} elseif (empty( $itemId )) {
			return $this->redirect()->toRoute( 'salesLiterature', array(
				'action' => 'edit',
				'id' => $id
			) );
		}
		
		$item = $this->fetchItem($itemId);
		$item->removeFile();
		
		$target = new TargetUpdateSalesLiteratureItem();
		$target->setEntity($item);
		$update = new UpdateRequest();
		$update->setTarget($target);
		
		$service = $this->getService();
		$response = $service->execute($update);
		
		return $this->redirect()->toRoute( 'salesLiterature', array(
			'action' => 'editItem',
			'id' => $id,
			'itemId' => $itemId
		) );
	}

	/**
	 * Fetches the specified sales literature record
	 *
	 * @param int $id
	 * @return SalesLiterature
	 */
	private function fetchEntity($id) {
		/* @var $response RetrieveResponse */
		$target = new TargetRetrieveSalesLiterature();
		$target->setId( $id );
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget( $target );
		
		$service = $this->getService();
		$response = $service->execute( $retrieve );
		return $response->getEntity();
	}

	/**
	 * Fetches the specified sales literature item record
	 *
	 * @param int $itemId
	 * @return SalesLiteratureItem
	 */
	private function fetchItem($itemId) {
		/* @var $response RetrieveResponse */
		$target = new TargetRetrieveSalesLiteratureItem();
		$target->setId( $itemId );
		$retrieve = new RetrieveRequest();
		$retrieve->setTarget( $target );
		
		$service = $this->getService();
		$response = $service->execute( $retrieve );
		return $response->getEntity();
	}

	/**
	 * Initializes form values for a new sales literature record
	 *
	 * @param SalesLiteratureForm $form
	 */
	private function initializeFormValues(SalesLiteratureForm $form) {
		/* @var $slfs Fieldset */
		/* @var $owner User */
		
		$now = new \DateTime();
		$owner = $this->zfcUserAuthentication()->getIdentity();
		
		$slfs = $form->get( SalesLiteratureFieldset::FIELDSETNAME );
		$slfs->get( SalesLiteratureFieldset::OWNER )->setValue( $owner->getId() );
		$slfs->get( SalesLiteratureFieldset::LITERATURETYPE )->setValue( LiteratureType::MARKETINGCOLLATERAL );
	}

	/**
	 * Sets nonstandard form values for an existing dales literature record
	 *
	 * @param SalesLiteratureForm $form
	 * @param SalesLiterature $entity
	 */
	private function setFormValues(SalesLiteratureForm $form, SalesLiterature $entity) {
		/* @var $slfs Fieldset */
		$form->get( SalesLiteratureForm::SUBMIT )->setValue( 'Save' );
		
		$slfs = $form->get( SalesLiteratureFieldset::FIELDSETNAME );
		$slfs->get( SalesLiteratureFieldset::EXPIRATIONDATE )->setValue( $entity->getFormattedExpirationDate() );
	}
}
?>