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
 * @version     SVN $Id: RetrieveMembersBulkOperationRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\EntitySource;
use Application\Service\Request;
use Application\Model\AbstractActivity;
use Application\Model\BulkOperation;

/**
 * Contains the data needed to retrieve the members of a bulk operation.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: RetrieveMembersBulkOperationRequest.php 13 2013-08-05 22:53:55Z  $
 */
class RetrieveMembersBulkOperationRequest extends Request {
	
	/**
	 * @var BulkOperation
	 */
	private $bulkOperation;
	
	/**
	 * @var EntitySource
	 */
	private $entitySource;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct('RetrieveMembersBulkOperationRequest');
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\BulkOperation
	 */
	public function getBulkOperation() {
		return $this->bulkOperation;
	}
	
	/**
	 * @param BulkOperation $bulkOperation
	 */
	public function setBulkOperation(BulkOperation $bulkOperation) {
		$this->bulkOperation = $bulkOperation;
	}
	
	/**
	 * @return \Application\Service\EntitySource
	 */
	public function getEntitySource() {
		return $this->entitySource;
	}
	
	/**
	 * @param string $entitySource
	 */
	public function setEntitySource($entitySource = null) {
		if($entitySource instanceof EntitySource) {
			$this->entitySource = $entitySource;
		} elseif ($entitySource != null) {
			$this->entitySource = EntitySource::instance($entitySource);
		} else {
			$this->entitySource = null;
		}
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\RetrieveMembersBulkOperationResponse
	 * @see \Application\Service\Request::execute()
	 */
	public function execute() {
		/* @var $activity AbstractActivity */
		
		$response = new RetrieveMembersBulkOperationResponse();
		
		if($this->getBulkOperation() == null) {
			$response->setMessage('No Bulk Operation was provided.');
			return $response;
		}
		
		$fqcn = '';
		if($this->getEntitySource() != null) {
			switch ($this->getEntitySource()) {
				case EntitySource::ACCOUNT:
					$fqcn = 'Application\Model\Account';
					break;
				case EntitySource::CONTACT:
					$fqcn = 'Application\Model\Contact';
					break;
				case EntitySource::LEAD:
					$fqcn = 'Application\Model\Lead';
					break;
			}
		}
		
		$collection = array();
		foreach ($this->getBulkOperation()->getBulkInteractions() as $activity) {
			$object = $activity->getRegardingObject();
			if($fqcn != '') {
				if($object instanceof $fqcn) {
					$collection[] = $object;
				}
			} else {
				$collection[] = $object;
			}
		}
		$response->setCollection($collection);
		
		return $response;
	}
}
?>