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
 * @package     Application\Model\Activity
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: CampaignResponse.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\CampaignResponseStatus;
use Application\Model\ChannelType;
use Application\Model\ResponseCode;
use Application\Model\TrackedActivity;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a response from an existing or a potential new customer for a campaign.
 *
 * @package     Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 *
 * @ORM\Entity
 */
class CampaignResponse extends TrackedActivity {

	/**
	 * @ORM\Column(type="string", name="channel_type")
	 * @var ChannelType
	 */
	private $channelType;
	
	/**
	 * @ORM\Column(type="string", name="from_sender")
	 * @var string
	 */
	
	protected $from;

	/**
	 * @ORM\Column(type="date", name="received_on")
	 * @var \DateTime
	 */
	private $receivedOn;

	/**
	 * @ORM\Column(type="string", name="response_code")
	 * @var ResponseCode
	 */
	private $responseCode;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return \Application\Model\ChannelType
	 */
	public function getChannelType() {
		return $this->channelType;
	}

	/**
	 * @param string|ChannelType $channelType
	 */
	public function setChannelType($channelType = null) {
		if ($channelType instanceof ChannelType) {
			$this->channelType = $channelType;
		} elseif ($channelType != null) {
			$this->channelType = ChannelType::instance( $channelType );
		} else {
			$this->channelType = null;
		}
	}
	
	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::getDiscriminator()
	 */
	public function getDiscriminator() {
		if ($this->discriminator == null) {
			$this->discriminator = 'CampaignResponse';
		}
		return $this->discriminator;
	}
	
	/**
	 * @return string
	 */
	public function getFrom() {
		return $this->from;
	}
	
	public function setFrom($from) {
		$this->from = (string) $from;
	}

	/**
	 * @return DateTime
	 */
	public function getReceivedOn() {
		return $this->receivedOn;
	}

	public function setReceivedOn($receivedOn = null) {
		if ($receivedOn != null) {
			if (!$receivedOn instanceof \DateTime) {
				$receivedOn = new \DateTime( $receivedOn );
			}
			$now = new \DateTime();
			if ($now->format( 'Y-m-d H:i' ) == $receivedOn->format( 'Y-m-d H:i' )) {
				$this->receivedOn = null;
			}
		}
		$this->receivedOn = $receivedOn;
	}

	/**
	 * @return \Application\Model\ResponseCode
	 */
	public function getResponseCode() {
		return $this->responseCode;
	}

	public function setResponseCode($responseCode = null) {
		if ($responseCode instanceof ResponseCode) {
			$this->responseCode = $responseCode;
		} elseif ($responseCode != null) {
			$this->responseCode = ResponseCode::instance( $responseCode );
		} else {
			$this->responseCode = null;
		}
	}
	
	/**
	 * @return \Application\Model\CampaignResponseStatus
	 * @see \Application\Model\StatefulActivity::getStatus()
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * @param string|CampaignResponseStatus $responseStatus
	 * @see \Application\Model\StatefulActivity::setStatus()
	 */
	public function setStatus($responseStatus = null) {
		if($responseStatus instanceof CampaignResponseStatus) {
			$this->status = $responseStatus;
		} elseif ($responseStatus != null) {
			$this->status = CampaignResponseStatus::instance($responseStatus);
		} else {
			$this->status = null;
		}
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Model\TrackedActivity::equals()
	 */
	public function equals(Object $o) {
		if (!$o instanceof $this) {
			return false;
		}
		if (parent::equals( $o ) && ($o->getChannelType() == $this->getChannelType())
				&& ($o->getFrom() == $this->getFrom())
				&& ($o->getResponseCode() == $this->getResponseCode())
				&& ($o->getReceivedOn() == $this->getReceivedOn())) {
			return true;
		}
		return false;
	}

	/**
	 * @param string $format
	 * @return string
	 */
	public function getFormattedReceivedOn($format = null) {
		if ($format == null) {
			$format = 'm/d/Y';
		}
		$result = '';
		if ($this->receivedOn != null) {
			$result = $this->receivedOn->format( $format );
		}
		return $result;
	}

	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::__toString()
	 */
	public function __toString() {
		return parent::__toString()
		. ',channelType=' . $this->getChannelType()
		. ',receivedOn=' . $this->getFormattedReceivedOn()
		. ',responseCode=' . $this->getResponseCode()
		. ']';
	}
}
?>