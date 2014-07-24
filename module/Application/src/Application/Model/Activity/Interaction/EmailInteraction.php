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
 * @package     Application\Model\Activity\Interaction
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: EmailInteraction.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\AbstractInteraction;
use Application\Model\Attachment;
use Application\Model\EmailStatus;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents an activity that is delivered using e-mail protocols.
 *
 * @package     Application\Model\Activity\Interaction
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: EmailInteraction.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 */
class EmailInteraction extends AbstractInteraction {

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $bcc;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $cc;

	/**
	 * @ORM\Column(type="integer", name="delivery_receipt_requested")
	 * @var boolean
	 */
	private $deliveryReceiptRequested = false;

	/**
	 * The Message-ID of the email message. Used only for an email that is received.
	 * @ORM\Column(type="string", name="message_id")
	 * @var string
	 */
	private $messageId;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $mimetype;

	/**
	 * @ORM\Column(type="integer", name="read_receipt_requested")
	 * @var boolean
	 */
	private $readReceiptRequested = false;

	/**
	 * TRANSIENT - NOT IN DATABASE
	 * @var boolean
	 */
	private $issueSend = false;
	
	/* ---------- One-to-Many Associations ---------- */
	
	/**
	 * Bidirectional One-to-Many: INVERSE SIDE
	 * @ORM\OneToMany(targetEntity="Attachment", mappedBy="activity", cascade={"persist", "remove"})
	 * @var ArrayCollection
	 */
	private $attachments;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->attachments = new ArrayCollection();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return string
	 */
	public function getBcc() {
		return $this->bcc;
	}

	/**
	 * @param string $bcc
	 */
	public function setBcc($bcc) {
		if (is_array( $bcc )) {
			$bcc = implode( ';', $bcc );
		}
		$this->bcc = (string) $bcc;
	}

	/**
	 * @return string
	 */
	public function getCc() {
		return $this->cc;
	}

	/**
	 * @param string $cc
	 */
	public function setCc($cc) {
		if (is_array( $cc )) {
			$cc = implode( ';', $cc );
		}
		$this->cc = (string) $cc;
	}

	/**
	 * @return boolean
	 */
	public function getDeliveryReceiptRequested() {
		return $this->deliveryReceiptRequested;
	}

	/**
	 * @param boolean $deliveryReceiptRequested
	 */
	public function setDeliveryReceiptRequested($deliveryReceiptRequested) {
		if (is_bool( $deliveryReceiptRequested ) || is_numeric( $deliveryReceiptRequested )) {
			$this->deliveryReceiptRequested = (bool) $deliveryReceiptRequested;
		} else {
			$this->deliveryReceiptRequested = ($deliveryReceiptRequested == 'true' ? true : false);
		}
	}

	/**
	 * @return string
	 * @see \Application\Model\AbstractActivity::getDiscriminator()
	 */
	public function getDiscriminator() {
		if ($this->discriminator == null) {
			$this->discriminator = 'EmailInteraction';
		}
		return $this->discriminator;
	}

	/**
	 * @return string
	 */
	public function getMessageId() {
		return $this->messageId;
	}

	/**
	 * @param string $messageId
	 */
	public function setMessageId($messageId) {
		$this->messageId = (string) $messageId;
	}

	/**
	 * @return string
	 */
	public function getMimetype() {
		return $this->mimetype;
	}

	/**
	 * @param string $mimetype
	 */
	public function setMimetype($mimetype) {
		$this->mimetype = (string) $mimetype;
	}

	/**
	 * @return boolean
	 */
	public function getReadReceiptRequested() {
		return $this->readReceiptRequested;
	}

	/**
	 * @param boolean $readReceiptRequested
	 */
	public function setReadReceiptRequested($readReceiptRequested) {
		if (is_bool( $readReceiptRequested ) || is_numeric( $readReceiptRequested )) {
			$this->readReceiptRequested = (bool) $readReceiptRequested;
		} else {
			$this->readReceiptRequested = ($readReceiptRequested == 'true' ? true : false);
		}
	}

	/**
	 * @return \Application\Model\EmailStatus
	 * @see \Application\Model\StatefulActivity::getStatus()
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string|EmailStatus $status
	 * @see \Application\Model\StatefulActivity::setStatus()
	 */
	public function setStatus($status = null) {
		if ($status instanceof EmailStatus) {
			$this->status = $status;
		} elseif ($status != null) {
			$this->status = EmailStatus::instance( (string) $status );
		} else {
			$this->status = null;
		}
	}

	/**
	 * @return boolean
	 */
	public function getIssueSend() {
		return $this->issueSend;
	}

	/**
	 * @param boolean $issueSend
	 */
	public function setIssueSend($issueSend) {
		$this->issueSend = (bool) $issueSend;
	}
	
	/* ---------- One-to-Many Association Getter/Setters ---------- */
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAttachments() {
		return $this->attachments;
	}

	public function setAttachments(ArrayCollection $attachments) {
		$this->attachments = $attachments;
	}

	public function addAttachment(Attachment $attachment) {
		$attachment->setActivity( $this );
		$this->attachments->add( $attachment );
	}

	public function removeAttachment(Attachment $attachment) {
		$attachment->setActivity( null );
		$this->attachments->removeElement( $attachment );
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * Sets the initiation time for the email send operation. Bypasses the filter for
	 * the actual date setter.
	 *
	 * @param \DateTime $startDate
	 */
	public function setSendStartDate($startDate) {
		if (!$startDate instanceof \DateTime) {
			$startDate = new \DateTime( $startDate );
		}
		$this->actualStart = $startDate;
	}

	/**
	 * Sets the completion time for the email send operation. Bypasses the filter for
	 * the actual date setter.
	 *
	 * @param \DateTime $endDate
	 */
	public function setSendEndDate($endDate) {
		if (!$endDate instanceof \DateTime) {
			$endDate = new \DateTime( $endDate );
		}
		$this->actualEnd = $endDate;
	}

	/**
	 * @return string
	 * @see \Application\Model\AbstractInteraction::__toString()
	 */
	public function __toString() {
		return parent::__toString() .
			 ',bcc=' .
			 $this->getBcc() .
			 ',cc=' .
			 $this->getCc() .
			 ',deliveryReceiptRequested=' .
			 ($this->getDeliveryReceiptRequested() ? 'true' : 'false') .
			 ',messageId=' .
			 $this->getMessageId() .
			 ',mimetype=' .
			 $this->getMimetype() .
			 ',readReceiptRequested=' .
			 ($this->getReadReceiptRequested() ? 'true' : 'false') .
			 ',issueSend=' .
			 ($this->getIssueSend() ? 'true' : 'false') .
			 ']';
	}
}
?>