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
 * @version     SVN $Id: TargetUpdate.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\User;
use Application\Service\UpdateResponse;
use Application\Stdlib\Entity;
use Doctrine\ORM\EntityManager;

/**
 * Represents the abstract base class to describe the target for an update operation
 * using the Update message.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TargetUpdate.php 13 2013-08-05 22:53:55Z  $
 * @abstract
 */
abstract class TargetUpdate {

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var Entity
	 */
	private $entity = null;

	/**
	 * @var User
	 */
	private $user;
	
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
	 * @return Entity
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * @param Entity $entity
	 */
	public function setEntity(Entity $entity) {
		$this->entity = $entity;
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
	 * @return UpdateResponse
	 */
	public abstract function update();
}
?>