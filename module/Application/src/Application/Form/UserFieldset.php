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
 * @version     SVN $Id: UserFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\AccessMode;
use Application\Model\BusinessUnit;
use Application\Model\LicenseType;
use Application\Model\Salutation;
use Application\Model\User;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * User fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: UserFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class UserFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'user';
	const ID = 'id';
	const ACCESSMODE = 'accessMode';
	const BUSINESSUNIT = 'businessUnit';
	const DISABLEDREASON = 'disabledReason';
	const EMAIL = 'email';
	const EMAILSIGNATURE = 'emailSignature';
	const FIRSTNAME = 'firstName';
	const ISDISABLED = 'isDisabled';
	const JOBTITLE = 'jobTitle';
	const LASTNAME = 'lastName';
	const LICENSETYPE = 'licenseType';
	const MIDDLENAME = 'middleName';
	const NICKNAME = 'nickname';
	const PASSWORD = 'password';
	const SALUTATION = 'salutation';
	const USERNAME = 'username';

	/**
	 * @var EntityManager
	 */
	private $em;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		parent::__construct( self::FIELDSETNAME );
		$this->em = $em;
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\User' ) )->setObject( new User() );
		
		$this->init();
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
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::ID
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::ACCESSMODE,
			'options' => array(
				'label' => 'Access Mode',
				'label_attributes' => array(
					'class' => 'input-label collection-label required'
				),
				'empty_option' => 'Select...',
				'value_options' => AccessMode::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Access Mode'
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::BUSINESSUNIT,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Business Unit',
				'label_attributes' => array(
					'class' => 'input-label required'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\BusinessUnit',
				'property' => 'name',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array(
							'name' => 'asc'
						)
					)
				)
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::DISABLEDREASON,
			'options' => array(
				'label' => 'Disabled Reason',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Disabled Reason'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::EMAIL,
			'options' => array(
				'label' => 'Email',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Email'
			)
		) );
		$this->add( array(
			'type' => 'Textarea',
			'name' => self::EMAILSIGNATURE,
			'options' => array(
				'label' => 'Signature',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 6,
				'title' => 'Signature'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::FIRSTNAME,
			'options' => array(
				'label' => 'First Name',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 40,
				'title' => 'First Name'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::ISDISABLED,
			'options' => array(
				'use_hidden_element' => true,
				'label' => 'Disabled',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Disabled'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::JOBTITLE,
			'options' => array(
				'label' => 'Job Title',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Job Title'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::LASTNAME,
			'options' => array(
				'label' => 'Last Name',
				'label_attributes' => array(
					'class' => 'input-label required'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 40,
				'title' => 'Last Name'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::LICENSETYPE,
			'options' => array(
				'label' => 'License Type',
				'label_attributes' => array(
					'class' => 'input-label required'
				),
				'empty_option' => 'Select...',
				'value_options' => LicenseType::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'License Type'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::MIDDLENAME,
			'options' => array(
				'label' => 'Middle Name',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 40,
				'title' => 'Middle Name'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::NICKNAME,
			'options' => array(
				'label' => 'Nickname',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Nickname'
			)
		) );
		$this->add( array(
			'type' => 'Password',
			'name' => self::PASSWORD,
			'options' => array(
				'label' => 'Password',
				'label_attributes' => array(
					'class' => 'input-label required'
				)
			),
			'attributes' => array(
				'autocomplete' => 'off',
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Password'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::SALUTATION,
			'options' => array(
				'label' => 'Salutation',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => Salutation::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Salutation'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::USERNAME,
			'options' => array(
				'label' => 'Username',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'autocomplete' => 'off',
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Username'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::ID => array(
				'required' => false
			),
			self::ACCESSMODE => array(
				'required' => true
			),
			self::BUSINESSUNIT => array(
				'required' => true
			),
			self::DISABLEDREASON => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 100
						)
					)
				)
			),
			self::EMAIL => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 255
						)
					),
					array(
						'name' => 'EmailAddress'
					)
				)
			),
			self::EMAILSIGNATURE => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array()
			),
			self::FIRSTNAME => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 64
						)
					)
				)
			),
			self::ISDISABLED => array(
				'required' => false
			),
			self::JOBTITLE => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 128
						)
					)
				)
			),
			self::LASTNAME => array(
				'required' => true,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 3,
							'max' => 64
						)
					)
				)
			),
			self::ISDISABLED => array(
				'required' => true
			),
			self::MIDDLENAME => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 64
						)
					)
				)
			),
			self::NICKNAME => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 50
						)
					)
				)
			),
			self::PASSWORD => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 128
						)
					)
				)
			),
			self::SALUTATION => array(
				'required' => false
			),
			self::USERNAME => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 255
						)
					)
				)
			)
		);
		
		return $spec;
	}
}
?>