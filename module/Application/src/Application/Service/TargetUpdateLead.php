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
 * @version     SVN $Id: TargetUpdateLead.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\Address;
use Application\Model\Lead;
use Application\Model\LeadState;
use Application\Model\LeadStatus;
use Application\Model\Telephone;

use Application\Service\TargetUpdate;
use Application\Stdlib\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Contains the data needed to update a lead.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetUpdateLead.php 13 2013-08-05 22:53:55Z  $
 */
class TargetUpdateLead extends TargetUpdate {

	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em = null) {
		parent::__construct( $em );
	}
	
	/* ---------- Methods ---------- */

	/**;
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
		
		if ($this->getEntityManager() == null || $this->getEntity() == null) {
			return $response;
		}
		
		try {
			$em = $this->getEntityManager();
			$entity = $this->prepareLead( $entity );
			$em->persist( $entity );
			$em->flush();

			$response->setMessage('Update successful.');
			$response->setResult( true );
		} catch ( Exception $e ) {
			$response->setMessage( $e->getMessage() );
		}
		
		return $response;
	}

	/**
	 * @param Lead $lead
	 * @return Lead
	 */
	private function prepareLead(Lead $lead) {
		/* @var $em EntityManager */
		/* @var $address Address */
		/* @var $telephone Telephone */
		
		$em = $this->getEntityManager();
		$user = $this->getUser();
		$now = new \DateTime();
		
		// Persist any One-to-Many associations
		if ($lead->getAccount() != null) {
			$em->persist( $lead->getAccount() );
		}
		if ($lead->getContact() != null) {
			$em->persist( $lead->getContact() );
		}
		if ($lead->getOpportunity() != null) {
			$em->persist( $lead->getOpportunity() );
		}
		
		// Strip or persist any Many-to-One associations
		$this->prepareTelephones($lead);
		$this->prepareAddresses($lead);
		
		// Set full name
		$lead->setFullName( $lead->computeFullName() );
		
		// Set state
		switch ($lead->getStatus()->getName()) {
			case LeadStatus::CANCELED :
				$lead->setState( LeadState::DISQUALIFIED );
				break;
			case LeadStatus::CANNOTCONTACT :
				$lead->setState( LeadState::DISQUALIFIED );
				break;
			case LeadStatus::CONTACTED :
				$lead->setState( LeadState::OPEN );
				break;
			case LeadStatus::LOST :
				$lead->setState( LeadState::DISQUALIFIED );
				break;
			case LeadStatus::NEWLEAD :
				$lead->setState( LeadState::OPEN );
				break;
			case LeadStatus::NOTINTERESTED :
				$lead->setState( LeadState::DISQUALIFIED );
				break;
			case LeadStatus::QUALIFIED :
				$lead->setState( LeadState::QUALIFIED );
				break;
		}
		
		// Set user characteristics
		if($lead->getOwner() != null) {
			$lead->setBusinessUnit( $lead->getOwner()->getBusinessUnit() );
		} else {
			$lead->setOwner( $user );
			$lead->setBusinessUnit( $user->getBusinessUnit() );
		}
		
		// Set modification dates
		$lead->setLastUpdateDate( $now );
		
		return $lead;
	}
	
	/**
	 * Tests and discard any null telephone
	 * @param Lead $lead
	 */
	private function prepareTelephones(Lead $lead) {
		/* @var $telephone Telephone */
		$telephones = new ArrayCollection();
		foreach ($lead->getTelephones() as $telephone) {
			if(strlen($telephone->getPhone()) > 0) {
				$this->prepareAssociation($telephone, $lead);
				$telephones->add($telephone);
			} else {
				$lead->removeTelephone($telephone);
			}
		}
		$lead->setTelephones($telephones);
	}
	
	/**
	 * Tests and discards any null address
	 * @param Lead $lead
	 */
	private function prepareAddresses(Lead $lead) {
		/* @var $address Address */
		$addresses = new ArrayCollection();
		foreach ($lead->getAddresses() as $address) {
			if(!$address->isEmpty()) {
				$this->prepareAssociation($address, $lead);
				$addresses->add($address);
			} else {
				$lead->removeAddress($address);
			}
		}
		$lead->setAddresses($addresses);
	}

	/**
	 * @param Entity $entity
	 */
	private function prepareAssociation(Entity $entity, Lead $lead) {
		$em = $this->getEntityManager();
		$now = new \DateTime();
		
		$owner = $lead->getOwner();
		$businessUnit = ($lead->getOwner() != null ? $lead->getOwner()->getBusinessUnit() : null);
		
		if(method_exists($entity, 'setOwner')) {
			$entity->setOwner($owner);
		}
		if(method_exists($entity, 'setBusinessUnit')) {
			$entity->setBusinessUnit($businessUnit);
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