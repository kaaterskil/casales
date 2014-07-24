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
 * @package     Application\Form
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: EmailFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\InteractionFieldset;
use Application\Model\ActivityPriority;
use Application\Model\EmailStatus;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element;

/**
 * Email fieldset for forms
 *
 * Provides additional form fields over the basic activity fieldset
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: EmailFieldset.php 13 2013-08-05 22:53:55Z  $
 * @see			BaseActivityFieldset
 */
class EmailFieldset extends InteractionFieldset {
	const BCC = 'bcc';
	const CC = 'cc';
	const DELIVERYRECEIPTREQUESTED = 'deliveryReceiptRequested';
	const ISSUESEND = 'issueSend';
	const FILEUPLOAD = 'fileUpload';
	const FROM = 'from';
	const MESSAGEID = 'messageId';
	const READRECEIPTREQUESTED = 'readReceiptRequested';
	const TO = 'to';
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$clazz = 'Application\Model\EmailInteraction';
		parent::__construct( $em, $clazz );
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return void
	 * @see \Application\Form\InteractionFieldset::init()
	 */
	public function init() {
		parent::init();
		
		$this->remove( self::DESCRIPTION );
		$this->remove( self::FROM );
		$this->remove( self::OWNER );
		$this->remove( self::STATUS );
		$this->remove( self::SUBJECT );
		$this->remove( self::TO );
		
		$this->add( array(
			'type' => 'Email',
			'name' => self::BCC,
			'options' => array(
				'label' => 'Bcc',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'multiple' => true,
				'size' => 155,
				'title' => 'Bcc'
			)
		) );
		$this->add( array(
			'type' => 'Email',
			'name' => self::CC,
			'options' => array(
				'label' => 'Cc',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'multiple' => true,
				'size' => 155,
				'title' => 'Cc'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::DELIVERYRECEIPTREQUESTED,
			'options' => array(
				'use_hidden_element' => true,
				'label' => 'Delivery Receipt',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Delivery Receipt'
			)
		) );
		$this->add( array(
			'type' => 'Textarea',
			'name' => self::DESCRIPTION,
			'options' => array(
				'label' => 'Body',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text email',
				'cols' => 172,
				'id' => 'email_body',
				'rows' => 16,
				'title' => 'Body'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::ISSUESEND,
			'options' => array(
				'use_hidden_element' => true,
				'label' => 'Do Not Send',
				'label_attributes' => array(
					'class' => 'input-label lfloat'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox mbl mrh',
				'id' => 'issueSend',
				'title' => 'Do Not Send'
			)
		) );
		$this->add( array(
			'type' => 'File',
			'name' => self::FILEUPLOAD,
			'options' => array(
				'label' => 'Attachment',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text mbt',
				'title' => 'Attachment'
			)
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::FROM
		) );
		/* $this->add( array( 'type' => 'Email', 'name' => self::FROM, 'options' => array(
		 * 'label' => 'From', 'label_attributes' => array( 'class' => 'input-label' ) ),
		 * 'attributes' => array( 'class' => 'input-text', 'multiple' => true, 'size' =>
		 * 155, 'title' => 'From' ) ) ); */
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::MESSAGEID
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::READRECEIPTREQUESTED,
			'options' => array(
				'use_hidden_element' => true,
				'label' => 'Read Receipt',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Read Receipt'
			)
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::OWNER
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::PRIORITY
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::STATUS
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::SUBJECT,
			'options' => array(
				'label' => 'Subject',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 128,
				'title' => 'Subject'
			)
		) );
		$this->add( array(
			'type' => 'Email',
			'name' => self::TO,
			'options' => array(
				'label' => 'To',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'multiple' => true,
				'size' => 155,
				'title' => 'To'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Application\Form\InteractionFieldset::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$array1 = parent::getInputFilterSpecification();
		
		// Replace the filter with a new one that will not strip html tags
		if (isset( $array1[self::DESCRIPTION] )) {
			unset( $array1[self::DESCRIPTION] );
		}
		
		$array2 = array(
			self::BCC => array(
				'required' => false
			),
			self::CC => array(
				'required' => false
			),
			self::DELIVERYRECEIPTREQUESTED => array(
				'required' => false
			),
			self::DESCRIPTION => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array()
			),
			self::ISSUESEND => array(
				'required' => false
			),
			self::FILEUPLOAD => array(
				'required' => false
			),
			self::MESSAGEID => array(
				'required' => false
			),
			self::READRECEIPTREQUESTED => array(
				'required' => false
			),
			self::STATUS => array(
				'required' => true
			),
			self::TO => array(
				'required' => false
			)
		);
		
		return array_merge( $array1, $array2 );
	}
}
?>