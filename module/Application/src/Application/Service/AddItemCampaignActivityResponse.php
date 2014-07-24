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
 * @version     SVN $Id: AddItemCampaignActivityResponse.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\CampaignItem;
use Application\Service\Response;

/**
 * Contains the response from the AddItemCampaignActivity message.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AddItemCampaignActivityResponse.php 13 2013-08-05 22:53:55Z  $
 */
class AddItemCampaignActivityResponse implements Response {
	
	/**
	 * @var CampaignItem
	 */
	private $campaignItem;
	
	/**
	 * @var string
	 */
	private $message;
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\CampaignItem
	 */
	public function getCampaignItem() {
		return $this->campaignItem;
	}
	
	/**
	 * @param CampaignItem $campaignItem
	 */
	public function setCampaignItem(CampaignItem $campaignItem) {
		$this->campaignItem = $campaignItem;
	}
	
	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}
	
	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = (string) $message;
	}
}
?>