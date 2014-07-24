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
 * @version     SVN $Id: AjaxController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Controller\AbstractApplicationController;
use Application\Model\Attachment;
use Application\Model\SalesLiteratureItem;
use Application\Service\RetrieveRequest;
use Application\Service\RetrieveResponse;
use Application\Service\TargetRetrieveMimeAttachment;
use Application\Service\TargetRetrieveSalesLiteratureItem;
use Doctrine\ORM\EntityManager;
use Zend\Http\Response;
use Zend\Json\Json;

/**
 * Ajax action controller
 *
 * @package		Application\Controller
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AjaxController.php 13 2013-08-05 22:53:55Z  $
 */
class AjaxController extends AbstractApplicationController {
	
	/**
	 * Downloads a Sales Literature Item
	 *
	 * @return void|\Zend\Http\Response
	 */
	public function downloadFileAction() {
		/* @var $item SalesLiteratureItem */
		/* @var $response Response */
		
		// Fetch and test parameters
		$id = $this->params('param1', 0);
		if(empty($id)) {
			return;
		}
		
		// Create receiver and command objects
		$target = new TargetRetrieveSalesLiteratureItem();
		$target->setId($id);
		$retrieveRequest = new RetrieveRequest();
		$retrieveRequest->setTarget($target);
		
		$service = $this->getService();
		$retrieveResponse = $service->retrieve($retrieveRequest);
		$item = $retrieveResponse->getEntity();
				
		$contentType = $item->getFiletype();
		$filename = $item->getFilename();
		$url = $item->getDocumentUrl();
		$content = file_get_contents($url);
		
		$response = $this->getResponse();
		$response->setContent($content);
		
		$headers = $response->getHeaders();
		$headers->clearHeaders();
		$headers->addHeaderLine('Content-Type', $contentType)
				->addHeaderLine('Content-Disposition', 'attachment; filename="' . $filename . '"')
				->addHeaderLine('Content-Length', strlen($content));
		return $response;
	}
	
	/**
	 * Downloads an email attachment
	 *
	 * @return void|\Zend\Http\Response
	 */
	public function downloadAttachmentAction() {
		/* @var $item Attachment */
		/* @var $response Response */
		
		// Fetch and test parameters
		$id = $this->params('param1', 0);
		if(empty($id)) {
			return;
		}
		
		// Create receiver and command objects
		$target = new TargetRetrieveMimeAttachment();
		$target->setId($id);
		$retrieveRequest = new RetrieveRequest();
		$retrieveRequest->setTarget($target);
		
		$service = $this->getService();
		$retrieveResponse = $service->retrieve($retrieveRequest);
		$item = $retrieveResponse->getEntity();
				
		$contentType = $item->getMimetype();
		$filename = $item->getFilename();
		$url = 'data/uploads/' . $filename;
		$content = file_get_contents($url);
		
		$response = $this->getResponse();
		$response->setContent($content);
		
		$headers = $response->getHeaders();
		$headers->clearHeaders();
		$headers->addHeaderLine('Content-Type', $contentType)
				->addHeaderLine('Content-Disposition', 'attachment; filename="' . $filename . '"')
				->addHeaderLine('Content-Length', strlen($content));
		return $response;
	}
}
?>