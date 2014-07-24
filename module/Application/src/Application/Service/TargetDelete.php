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
 * @version     SVN $Id: TargetDelete.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\DeleteResponse;
use Application\Service\Command;
use Doctrine\ORM\EntityManager;

/**
 * Represents the abstract base class used to describe the target for a delete operation
 * using the Delete message.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetDelete.php 13 2013-08-05 22:53:55Z  $
 * @abstract
 */
abstract class TargetDelete {

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $clazz;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		$this->em = $em;
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
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = (int) $id;
	}

	/**
	 * @return string
	 */
	public function getClazz() {
		return $this->clazz;
	}

	/**
	 * @param string $clazz
	 */
	public function setClazz($clazz) {
		$this->clazz = (string) $clazz;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\DeleteResponse
	 */
	public function delete() {
		$response = new DeleteResponse();
		
		if ($this->getId() == null || $this->getId() < 1) {
			$response->setMessage( 'Record id not found.' );
			return $response;
		}
		if ($this->getId() < 1) {
			$response->setMessage( 'Cannot delete an record that has not yet been saved.' );
			return $response;
		}
		if (($this->getClazz() == null) || (!is_string( $this->getClazz() )) || ($this->getClazz() == '')) {
			$response->setMessage( 'Unknown entity type passed to Delete.' );
			$response->setResult( false );
			return $response;
		}
		
		$em = $this->getEntityManager();
		if ($em == null) {
			$response->setMessage( 'EntityManager not found.' );
			return $response;
		}
		
		try {
			$entity = $em->getRepository( $this->getClazz() )
				->find( $this->getId() );
			if ($entity == null) {
				$response->setMessage( 'Target record not found.' );
				return $response;
			}
			
			$em->remove( $entity );
			$em->flush();
			
			$response->setMessage( 'Deletion successful.' );
			$response->setResult( true );
		} catch ( \Exception $e ) {
			echo '<pre>' . $e->getMessage() . '</pre>';
			die();
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}
}
?>