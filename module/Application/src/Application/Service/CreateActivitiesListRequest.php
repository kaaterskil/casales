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
 * @version     SVN $Id: CreateActivitiesListRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\AbstractActivity;
use Application\Model\MarketingList;
use Application\Model\User;
use Application\Service\PropagationOwnershipOptions;
use Application\Service\Request;
use Doctrine\ORM\EntityManager;

/**
 * Contains the data needed to create an activity for each member on the list.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: CreateActivitiesListRequest.php 13 2013-08-05 22:53:55Z  $
 */
class CreateActivitiesListRequest extends Request {
	
	/**
	 * @var AbstractActivity
	 */
	private $activity;
	
	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @var string
	 */
	private $friendlyName;
	
	/**
	 * @var MarketingList
	 */
	private $marketingList;
	
	/**
	 * @var User
	 */
	private $owner;
	
	/**
	 * @var PropagationOwnershipOptions
	 */
	private $ownershipOptions;
	
	/**
	 * @var boolean
	 */
	private $propagate = false;
	
	/**
	 * @var boolean
	 */
	private $sendEmail = false;
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\AbstractActivity
	 */
	public function getActivity() {;
		return $this->activity;
	}
	
	public function setActivity(AbstractActivity $activity = null) {
		$this->activity = $activity;
	}
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager() {
		return $this->em;
	}
	
	public function setEntityManager(EntityManager $em) {
		$this->em = $em;
	}
	
	/**
	 * @return string
	 */
	public function getFriendlyName() {
		return $this->friendlyName;
	}
	
	public function setFriendlyName($friendlyName) {
		$this->friendlyName = (string) $friendlyName;
	}
	
	/**
	 * @return \Application\Model\MarketingList
	 */
	public function getMarketingList() {
		return $this->marketingList;
	}
	
	public function setMarketingList(MarketingList $marketingList = null) {
		$this->marketingList = $marketingList;
	}
	
	/**
	 * @return \Application\Model\User
	 */
	public function getOwner() {
		return $this->owner;
	}
	
	public function setOwner(User $owner = null) {
		$this->owner = $owner;
	}
	
	/**
	 * @return PropagationOwnershipOptions
	 */
	public function getOwnereshipOptions() {
		return $this->ownershipOptions;
	}
	
	public function setOwnereshipOptions($ownershipOptions = null) {
		if($ownershipOptions instanceof PropagationOwnershipOptions) {
			$this->ownershipOptions = $ownershipOptions;
		} elseif($ownershipOptions != null) {
			$this->ownershipOptions = PropagationOwnershipOptions::instance($ownershipOptions);
		} else {
			$this->ownershipOptions = null;
		}
	}
	
	/**
	 * @return boolean
	 */
	public function getPropagate() {
		return $this->propagate;
	}
	
	public function setPropagate($propagate) {
		$this->propagate = (bool) $propagate;
	}
	
	/**
	 * @return boolean
	 */
	public function getSendEmail() {
		return $this->sendEmail;
	}
	
	public function setSendEmail($sendEmail) {
		$this->sendEmail = (bool) $sendEmail;
	}
}
?>