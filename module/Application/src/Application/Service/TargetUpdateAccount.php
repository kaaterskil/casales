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
 * @version     SVN $Id: TargetUpdateAccount.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\Address;
use Application\Model\Account;
use Application\Model\AccountState;
use Application\Model\AccountStatus;
use Application\Model\Contact;
use Application\Model\Telephone;
use Application\Service\TargetUpdate;
use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\Audit;
use Application\Model\AuditAction;
use Application\Model\AuditOperation;

/**
 * Contains the data needed to update an account.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetUpdateAccount.php 13 2013-08-05 22:53:55Z  $
 */
class TargetUpdateAccount extends TargetUpdate {

	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct( $em );
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return \Application\Service\UpdateResponse
	 * @see \Application\Service\TargetUpdate::update()
	 */
	public function update() {
		$response = new UpdateResponse();
		
		if ($this->getEntityManager() == null) {
			$response->setMessage( 'No EntityManager found.' );
			return $response;
		}
		
		$entity = $this->getEntity();
		if ($entity == null) {
			$response->setMessage( 'No record found to update.' );
			return $response;
		} elseif ($entity->getId() == null || $entity->getId() < 1) {
			$response->setMessage( 'Cannot update a new unmanaged record.' );
			return $response;
		}
		
		try {
			$em = $this->getEntityManager();
			$entity = $this->prepareAccount( $entity );
			$em->persist( $entity );
			$em->flush();
			
			$response->setMessage( 'Update successful.' );
			$response->setResult( true );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * @param Account $account
	 * @return Account
	 */
	private function prepareAccount(Account $account) {
		/* @var $em EntityManager */
		/* @var $contact Contact */
		/* @var $address Address */
		/* @var $telephone Telephone */
		/* @var $rp \ReflectionProperty */
		
		$em = $this->getEntityManager();
		$user = $this->getUser();
		$now = new \DateTime();
		
		if ($account->getPrimaryContact() != null) {
			$contact = $account->getPrimaryContact();
			$contact->setAccount( $account );
			$contact->setIsPrimaryContact( true );
			$this->prepareContactTelephone( $contact );
			$em->persist( $contact );
		}
		if ($account->getParentAccount() != null) {
			$account->getParentAccount()->addChildAccount( $account );
			$this->prepareTelephones( $account->getParentAccount() );
			$this->prepareAddresses( $account->getParentAccount() );
			$em->persist( $account->getParentAccount() );
		}
		
		// Strip or persist any Many-to-One associations
		$this->prepareTelephones( $account );
		$this->prepareAddresses( $account );
		
		// Set state
		switch ($account->getStatus()->getName()) {
			case AccountStatus::ACTIVE :
				$account->setState( AccountState::ACTIVE );
				break;
			case AccountStatus::INACTIVE :
				$account->setState( AccountState::INACTIVE );
				break;
		}
		
		// Set user characteristics
		if ($account->getOwner() != null) {
			$account->setBusinessUnit( $account->getOwner()
				->getBusinessUnit() );
		} else {
			$account->setOwner( $user );
			$account->setBusinessUnit( $user->getBusinessUnit() );
		}
		
		// Set modification dates
		$account->setLastUpdateDate( $now );
		
		return $account;
	}

	/**
	 * Tests and discards a telephone if null
	 * @param Account $account
	 */
	private function prepareTelephones(Account $account) {
		/* @var $telephone Telephone */
		$telephones = new ArrayCollection();
		foreach ( $account->getTelephones() as $telephone ) {
			if (strlen( $telephone->getPhone() ) > 0) {
				$this->prepareAssociation( $telephone, $account );
				$telephones->add( $telephone );
			} else {
				$account->removeTelephone( $telephone );
			}
		}
		$account->setTelephones( $telephones );
	}

	/**
	 * Tests and discards a null address
	 * @param Account $account
	 */
	private function prepareAddresses(Account $account) {
		/* @var $address Address */
		$addresses = new ArrayCollection();
		foreach ( $account->getAddresses() as $address ) {
			if (!$address->isEmpty()) {
				$this->prepareAssociation( $address, $account );
				$addresses->add( $address );
			} else {
				$account->removeAddress( $address );
			}
		}
		$account->setAddresses( $addresses );
	}

	private function prepareContactTelephone(Contact $contact) {
		/* @var $telephone Telephone */
		$telephones = new ArrayCollection();
		foreach ( $contact->getTelephones() as $telephone ) {
			if (strlen( $telephone->getPhone() ) > 0) {
				$this->prepareAssociation( $telephone, $contact );
				$telephones->add( $telephone );
			} else {
				$contact->removeTelephone( $telephone );
			}
		}
		$contact->setTelephones( $telephones );
	}

	/**
	 * @param Entity $entity
	 */
	private function prepareAssociation(Entity $entity, $parent) {
		$em = $this->getEntityManager();
		$now = new \DateTime();
		
		$owner = $parent->getOwner();
		$businessUnit = ($parent->getOwner() != null ? $parent->getOwner()->getBusinessUnit() : null);
		
		if (method_exists( $entity, 'setOwner' )) {
			$entity->setOwner( $owner );
		}
		if (method_exists( $entity, 'setBusinessUnit' )) {
			$entity->setBusinessUnit( $businessUnit );
		}
		
		if ($entity->getId() == null || $entity->getId() == 0) {
			$entity->setCreationDate( $now );
			$entity->setLastUpdateDate( $now );
		} else {
			$old = $em->getRepository( $entity->getClass() )
				->find( $entity->getId() );
			if ($old !== $entity) {
				$entity->setLastUpdateDate( $now );
			}
		}
	}
}
?>