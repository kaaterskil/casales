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
 * @version     SVN $Id: ContactFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\AccountFieldset;
use Application\Model\Account;
use Application\Model\Contact;
use Application\Model\ContactSortField;
use Application\Model\ContactState;
use Application\Model\ContactStatus;
use Application\Model\Gender;
use Application\Model\Salutation;

use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;

use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Element\Collection;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;
use Zend\Validator\Hostname;

/**
 * Contact fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: ContactFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class ContactFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'contact';
	const ID = 'id';
	const ACCOUNT = 'account';
	const ASSISTANTEMAIL = 'assistantEmail';
	const ASSISTANT = 'assistantName';
	const ASSISTANTPHONE = 'assistantTelephone';
	const BIRTHDATE = 'birthDate';
	const BUSINESSUNIT = 'businessUnit';
	const DESCRIPTION = 'description';
	const DISPLAYNAME = 'displayName';
	const DONOTCALL = 'doNotCall';
	const DONOTEMAIL = 'doNotEmail';
	const DONOTMAIL = 'doNotMail';
	const EMAIL1 = 'email1';
	const EMAIL2 = 'email2';
	const FIRSTNAME = 'firstName';
	const GENDER = 'gender';
	const INTERESTS = 'interests';
	const ISPRIMARY = 'isPrimaryContact';
	const JOBTITLE = 'jobTitle';
	const LASTNAME = 'lastName';
	const MIDDLENAME = 'middleName';
	const NICKNAME = 'nickname';
	const ORIGINATINGLEAD = 'originatingLead';
	const OWNER = 'owner';
	const PREFIX = 'prefix';
	const SALUTATION = 'salutation';
	const SORTNAME = 'sortName';
	const STATUS = 'status';
	const SUFFIX = 'suffix';
	const WEBSITE = 'website';
	const ADDRESS = 'addresses';
	const TELEPHONE = 'telephones';

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
		$this->em = $em;
		parent::__construct( self::FIELDSETNAME );
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\Contact' ) )->setObject( new Contact() );
		
		$this->init();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return EntityManager
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
	 * Initializes form elements, including the Address and Telephone field sets
	 *
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$this->add( array (
			'type' => 'Hidden',
			'name' => self::ID
		) );
		$this->add( array (
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::ACCOUNT,
			'options' => array (
				'empty_option' => 'Select...',
				'label' => 'Account',
				'label_attributes' => array (
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\Account',
				'property' => 'name',
				'find_method' => array (
					'name' => 'findBy',
					'params' => array (
						'criteria' => array (),
						'orderBy' => array (
							'name' => 'asc'
						)
					)
				)
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::ASSISTANTEMAIL,
			'options' => array (
				'label' => 'Assistant Email',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Assistant Email'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::ASSISTANT,
			'options' => array (
				'label' => 'Assistant Name',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Assistant Name'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::ASSISTANTPHONE,
			'options' => array (
				'label' => 'Assistant Phone',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'title' => 'Assistant Phone'
			)
		) );
		$this->add( array (
			'type' => 'Date',
			'name' => self::BIRTHDATE,
			'options' => array (
				'label' => 'Birth Date',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Birth Date'
			)
		) );
		$this->add(array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::BUSINESSUNIT,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Business Unit',
				'label_attributes' => array('class' => 'input-label'),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\BusinessUnit',
				'property' => 'name',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array('name' => 'asc'),
					),
				),
			),
		));
		$this->add( array (
			'type' => 'Textarea',
			'name' => self::DESCRIPTION,
			'options' => array (
				'label' => 'Description',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 13,
				'title' => 'Description'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::DISPLAYNAME,
			'options' => array (
				'label' => 'Full Name',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Full Name'
			)
		) );
		$this->add( array (
			'type' => 'Checkbox',
			'name' => self::DONOTCALL,
			'options' => array (
				'use_hidden_element' => true,
				'label' => 'Do Not Call',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-checkbox',
				'title' => 'Do Not Call'
			)
		) );
		$this->add( array (
			'type' => 'Checkbox',
			'name' => self::DONOTEMAIL,
			'options' => array (
				'use_hidden_element' => true,
				'label' => 'Do Not Email',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-checkbox',
				'title' => 'Do Not Email'
			)
		) );
		$this->add( array (
			'type' => 'Checkbox',
			'name' => self::DONOTMAIL,
			'options' => array (
				'use_hidden_element' => true,
				'label' => 'Do Not Mail',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-checkbox',
				'title' => 'Do Not Mail'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::EMAIL1,
			'options' => array (
				'label' => 'Email 1',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Email 1'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::EMAIL2,
			'options' => array (
				'label' => 'Email 2',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Email 2'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::FIRSTNAME,
			'options' => array (
				'label' => 'First Name',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text bold',
				'size' => 40,
				'title' => 'First Name'
			)
		) );
		$this->add( array (
			'type' => 'Select',
			'name' => self::GENDER,
			'options' => array (
				'label' => 'Gender',
				'label_attributes' => array (
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => \Application\Model\Gender::toArray()
			),
			'attributes' => array (
				'class' => 'input-select',
				'title' => 'Gender'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::INTERESTS,
			'options' => array (
				'label' => 'Interests',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 80,
				'title' => 'Interests'
			)
		) );
		$this->add( array (
			'type' => 'Checkbox',
			'name' => self::ISPRIMARY,
			'options' => array (
				'use_hidden_element' => true,
				'label' => 'Primary Contact',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-checkbox',
				'title' => 'Primary Contact'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::JOBTITLE,
			'options' => array (
				'label' => 'Job Title',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Job Title'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::LASTNAME,
			'options' => array (
				'label' => 'Last Name',
				'label_attributes' => array (
					'class' => 'input-label required'
				)
			),
			'attributes' => array (
				'class' => 'input-text bold',
				'size' => 40,
				'title' => 'Last Name'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::MIDDLENAME,
			'options' => array (
				'label' => 'Middle Name',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text bold',
				'size' => 40,
				'title' => 'Middle Name'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::NICKNAME,
			'options' => array (
				'label' => 'Nickname',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Nickname'
			)
		) );
		$this->add( array (
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::ORIGINATINGLEAD,
			'options' => array (
				'empty_option' => 'Select...',
				'label' => 'Originating Lead',
				'label_attributes' => array (
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\Lead',
				'property' => 'fullName',
				'find_method' => array (
					'name' => 'findBy',
					'params' => array (
						'criteria' => array (),
						'orderBy' => array (
							'lastName' => 'asc'
						)
					)
				)
			)
		) );
		$this->add(array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::OWNER,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Ownership',
				'label_attributes' => array('class' => 'input-label'),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\User',
				'property' => 'fullName',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array('lastName' => 'asc'),
					),
				),
			),
		));
		$this->add( array (
			'type' => 'Text',
			'name' => self::PREFIX,
			'options' => array (
				'label' => 'Prefix',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text bold',
				'size' => 15,
				'title' => 'Prefix'
			)
		) );
		$this->add( array (
			'type' => 'Select',
			'name' => self::SALUTATION,
			'options' => array (
				'label' => 'Salutation',
				'label_attributes' => array (
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => Salutation::toArray()
			),
			'attributes' => array (
				'class' => 'input-select',
				'title' => 'Salutation'
			)
		) );
		$this->add( array (
			'type' => 'Select',
			'name' => self::SORTNAME,
			'options' => array (
				'label' => 'Sort By',
				'label_attributes' => array (
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => \Application\Model\ContactSortField::toArray()
			),
			'attributes' => array (
				'class' => 'input-select',
				'title' => 'Sort By'
			)
		) );
		$this->add( array (
			'type' => 'Select',
			'name' => self::STATUS,
			'options' => array (
				'label' => 'Status',
				'label_attributes' => array (
					'class' => 'input-label required'
				),
				'empty_option' => 'Select...',
				'value_options' => ContactStatus::toArray()
			),
			'attributes' => array (
				'class' => 'input-select',
				'title' => 'Status'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::SUFFIX,
			'options' => array (
				'label' => 'Suffix',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text bold',
				'size' => 25,
				'title' => 'Suffix'
			)
		) );
		$this->add( array (
			'type' => 'Text',
			'name' => self::WEBSITE,
			'options' => array (
				'label' => 'Website',
				'label_attributes' => array (
					'class' => 'input-label'
				)
			),
			'attributes' => array (
				'class' => 'input-text',
				'size' => 80,
				'title' => 'Website'
			)
		) );
		
		
		/* ----- Telephone Information ----- */
		
		$telephoneFieldset = new TelephoneFieldset( $this->em );
		$tfc = new Collection();
		$tfc->setName( self::TELEPHONE );
		$tfc->setOptions( array (
			'label' => 'Say Hello',
			'target_element' => $telephoneFieldset,
			'count' => 1,
			'allow_add' => true,
			'allow_remove' => true,
			'should_create_template' => true,
			'template_placeholder' => '__telephone__'
		) );
		$this->add( $tfc );
		
		/* ----- Address Information ----- */
		
		$addressFieldset = new AddressFieldset( $this->em );
		$afc = new Collection();
		$afc->setName( self::ADDRESS );
		$afc->setOptions( array (
			'label' => 'Addresses',
			'target_element' => $addressFieldset,
			'count' => 1,
			'allow_add' => true,
			'allow_remove' => true,
			'should_create_template' => true,
			'template_placeholder' => '_address'
		) );
		$this->add( $afc );
	}

	/**
	 * Returns an array InputFilter specification
	 *
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array (
			self::ACCOUNT => array (
				'required' => false,
				'allow_empty' => true,
				'filters' => array (
					array (
						'name' => 'Digits'
					)
				),
				'validators' => array ()
			),
			self::ASSISTANTEMAIL => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 255
						)
					),
					array (
						'name' => 'EmailAddress'
					)
				)
			),
			self::ASSISTANT => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 128
						)
					)
				)
			),
			self::ASSISTANTPHONE => array (
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
				'validators' => array(
					array(
						'name' => 'Regex',
						'options' => array(
							'pattern' => '/^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/',
						),
					),
				),
			),
			self::BIRTHDATE => array (
				'required' => false,
			),
			self::BUSINESSUNIT => array(
				'required' => false,
			),
			self::DESCRIPTION => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array ()
			),
			self::DONOTCALL => array(
				'required' => 'false',
			),
			self::DONOTEMAIL => array(
				'required' => 'false',
			),
			self::DONOTMAIL => array(
				'required' => 'false',
			),
			self::EMAIL1 => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 255
						)
					),
					array (
						'name' => 'EmailAddress'
					)
				)
			),
			self::EMAIL2 => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 255
						)
					),
					array (
						'name' => 'EmailAddress'
					)
				)
			),
			self::FIRSTNAME => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 64
						)
					)
				)
			),
			self::GENDER => array (
				'required' => false
			),
			self::INTERESTS => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 128
						)
					)
				)
			),
			self::ISPRIMARY => array (
				'required' => false
			),
			self::JOBTITLE => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 128
						)
					)
				)
			),
			self::LASTNAME => array (
				'required' => true,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'min' => 3,
							'max' => 64
						)
					)
				)
			),
			self::MIDDLENAME => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 64
						)
					)
				)
			),
			self::NICKNAME => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 50
						)
					)
				)
			),
			self::ORIGINATINGLEAD => array (
				'required' => false
			),
			self::OWNER => array(
				'required' => false,
			),
			self::PREFIX => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 10
						)
					)
				)
			),
			self::SALUTATION => array (
				'required' => false
			),
			self::SORTNAME => array (
				'required' => false
			),
			self::STATUS => array (
				'required' => true
			),
			self::SUFFIX => array (
				'required' => false,
				'filters' => array (
					array (
						'name' => 'StripTags'
					),
					array (
						'name' => 'StringTrim'
					)
				),
				'validators' => array (
					array (
						'name' => 'StringLength',
						'options' => array (
							'encoding' => 'UTF-8',
							'max' => 30
						)
					)
				)
			),
			self::WEBSITE => array (
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
					array(
						'name' => 'UriNormalize',
					),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 255,
						),
					),
					array(
						'name' => 'Hostname',
						'allow' => Hostname::ALLOW_DNS,
					),
				),
			),
		);
		return $spec;
	}
}
?>