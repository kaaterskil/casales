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
 * @version     SVN $Id: Attachment.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Model\EmailInteraction;
use Application\Stdlib\Entity;
use Application\Stdlib\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a MIME attachment for an e-mail activity.
 *
 * @package     Application\Model\Activity\Interaction
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: Attachment.php 13 2013-08-05 22:53:55Z  $
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_activity_mime_attachment")
 */
class Attachment implements Entity {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * Unidirectional Many-to-One: OWNING SIDE
	 * @ORM\ManyToOne(targetEntity="EmailInteraction")
	 * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
	 * @var EmailInteraction
	 */
	private $activity;

	/**
	 * @ORM\Column(type="integer", name="attachment_number")
	 * @var int
	 */
	private $attachmentNumber;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $body;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $filename;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $filesize;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $mimetype;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $subject;

	/**
	 * @ORM\Column(type="datetime", name="creation_date")
	 * @var \DateTime
	 */
	private $creationDate;

	/**
	 * @ORM\Column(type="datetime", name="last_update_date")
	 * @var \DateTime
	 */
	private $lastUpdateDate;
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return int
	 * @see \Application\Stdlib\Entity::getId()
	 */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/**
	 * @return \Application\Model\EmailInteraction
	 */
	public function getActivity() {
		return $this->activity;
	}

	public function setActivity(EmailInteraction $activity = null) {
		$this->activity = $activity;
	}

	/**
	 * @return number
	 */
	public function getAttachmentNumber() {
		return $this->attachmentNumber;
	}

	public function setAttachmentNumber($attachmentNumber) {
		$this->attachmentNumber = (int) $attachmentNumber;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	public function setBody($body) {
		$this->body = (string) $body;
	}

	/**
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	public function setFilename($filename) {
		$this->filename = $filename;
	}

	/**
	 * @return number
	 */
	public function getFilesize() {
		return $this->filesize;
	}

	public function setFilesize($filesize) {
		$this->filesize = (int) $filesize;
	}

	/**
	 * @return string
	 */
	public function getMimetype() {
		return $this->mimetype;
	}

	public function setMimetype($mimetype) {
		$this->mimetype = (string) $mimetype;
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	public function setSubject($subject) {
		$this->subject = (string) $subject;
	}
	
	/**
	 * @return DateTime
	 * @see \Application\Stdlib\Entity::getCreationDate()
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}
	
	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}
	
	/**
	 * @return DateTime
	 * @see \Application\Stdlib\Entity::getLastUpdateDate()
	 */
	public function getLastUpdateDate() {
		return $this->lastUpdateDate;
	}
	
	public function setLastUpdateDate($lastUpdateDate) {
		$this->lastUpdateDate = $lastUpdateDate;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\Stdlib\Object::equals()
	 */
	public function equals(Object $o) {
		if (!$o instanceof $this) {
			return false;
		}
		if ($o->getId() == $this->getId()) {
			return true;
		}
		if (($o->getActivity()->equals( $this->getActivity() ))
				&& ($o->getSubject() == $this->getSubject())
				&& ($o->getBody() == $this->getBody())) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return string
	 * @see \Application\Stdlib\Object::getClass()
	 */
	public function getClass() {
		return get_class( $this );
	}

	/**
	 * @return string
	 * @see \Application\Stdlib\Object::__toString()
	 */
	public function __toString() {
		return 'Attachment[id=' . $this->getId()
		. ',activity=' . ($this->getActivity() ? $this->getActivity()->getId() : '')
		. ',attachmentNumber=' . $this->getAttachmentNumber()
		. ',body=' . $this->getBody()
		. ',filename=' . $this->getFilename()
		. ',filesize=' . $this->getFilesize()
		. ',mimetype=' . $this->getMimetype()
		. ',subject=' . $this->getSubject()
		. ',creationDate=' . ($this->getCreationDate() ? $this->getCreationDate()->format('Y-m-d H:i:s') : '')
		. ',lastUpdateDate=' . ($this->getLastUpdateDate() ? $this->getLastUpdateDate()->format('Y-m-d H:i:s') : '')
		. ']';
	}
}
?>