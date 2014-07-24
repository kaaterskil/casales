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
 * @version     SVN $Id: TargetCreateContact.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\Address;
use Application\Model\Contact;
use Application\Model\Telephone;
use Application\Service\TargetCreate;
use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;
use Application\Model\ContactStatus;
use Application\Model\ContactState;

/**
 * Contains the data needed to create a contact.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetCreateContact.php 13 2013-08-05 22:53:55Z  $
 */
class TargetCreateContact extends TargetCreate {

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
	 * @return \Application\Service\CreateResponse
	 * @see \Application\Service\TargetCreate::create()
	 */
	public function create() {
		$response = new CreateResponse();
		
		if ($this->getEntityManager() == null) {
			$response->setMessage( 'No EntityManager found.' );
			return $response;
		}
		
		$entity = $this->getEntity();
		if ($entity == null) {
			$response->setMessage( 'No record found to save.' );
			return $response;
		}
		if ($entity->getId() != null && $entity->getId() > 0) {
			$response->setMessage( 'Cannot create an already existing record.' );
			return $response;
		}
		
		try {
			$em = $this->getEntityManager();
			$entity = $this->prepareContact( $entity );
			$em->persist( $entity );
			$em->flush();
			
			$response->setMessage( 'Contact successfully created.' );
			$response->setResult( true );
			$response->setId( $entity->getId() );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * @param Contact $contact
	 * @return Contact
	 */
	private function prepareContact(Contact $contact) {
		/* @var $em EntityManager */
		/* @var $address Address */
		/* @var $telephone Telephone */
		
		$em = $this->getEntityManager();
		$user = $this->getUser();
		$now = new \DateTime();
		
		// Persist any One-to-Many associations
		if ($contact->getAccount() != null) {
			// Test for primary contact
			if ($contact->getIsPrimaryContact()) {
				$contact->getAccount()->setPrimaryContact( $contact );
			}
			$em->persist( $contact->getAccount() );
		}
		
		// Strip or persist any Many-to-One associations
		$this->prepareTelephones( $contact );
		$this->prepareAdresses( $contact );
		
		// Set full name
		$contact->setDisplayName( $contact->computeDisplayName() );
		
		// Set state
		switch ($contact->getStatus()->getName()) {
			case ContactStatus::ACTIVE :
				$contact->setState( ContactState::ACTIVE );
				break;
			case ContactStatus::INACTIVE :
				$contact->setState( ContactState::INACTIVE );
				break;
		}
		
		// Set user characteristics
		if ($contact->getOwner() != null) {
			$contact->setBusinessUnit( $contact->getOwner()
				->getBusinessUnit() );
		} else {
			$contact->setOwner( $user );
			$contact->setBusinessUnit( $user->getBusinessUnit() );
		}
		
		// Set creation and modification dates
		if ($contact->getId() == null || $contact->getId() == 0) {
			$contact->setCreationDate( $now );
		}
		$contact->setLastUpdateDate( $now );
		
		return $contact;
	}

	/**
	 * Tests and discards a null telephone
	 * @param Contact $contact
	 */
	private function prepareTelephones(Contact $contact) {
		/* @var $telephone Telephone */
		$telephones = new ArrayCollection();
		foreach ( $contact->getTelephones() as $telephone ) {
			if (strlen( $telephone->getPhone() ) > 0) {
				$this->prepareAssociation( $telephone );
				$telephones->add( $telephone );
			} else {
				$contact->removeTelephone( $telephone );
			}
		}
		$contact->setTelephones( $telephones );
	}

	/**
	 * Tests and discards a null address
	 * @param Contact $contact
	 */
	private function prepareAdresses(Contact $contact) {
		/* @var $address Address */
		$addresses = new ArrayCollection();
		foreach ( $contact->getAddresses() as $address ) {
			if (!$address->isEmpty()) {
				$this->prepareAssociation( $address );
				$addresses->add( $address );
			} else {
				$contact->removeAddress( $address );
			}
		}
		$contact->setAddresses( $addresses );
	}

	/**
	 * @param Entity $entity
	 */
	private function prepareAssociation(Entity $entity) {
		$em = $this->getEntityManager();
		$now = new \DateTime();
		
		$owner = $account->getOwner();
		$businessUnit = ($account->getOwner() != null ? $account->getOwner()->getBusinessUnit() : null);
		
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