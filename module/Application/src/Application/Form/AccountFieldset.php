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
 * @version     SVN $Id: AccountFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\Account;
use Application\Model\AccountCategory;
use Application\Model\AccountGroup;
use Application\Model\AccountSource;
use Application\Model\AccountStatus;
use Application\Model\AccountType;
use Application\Form\AddressFieldset;
use Application\Form\TelephoneFieldset;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Element\Collection;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;
use Zend\Validator\Hostname;

/**
 * Account fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AccountFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class AccountFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'account';
	const ID = 'id';
	const ACCOUNTGROUP = 'accountGroup';
	const ACCOUNTTYPE = 'accountType';
	const BUSINESSUNIT = 'businessUnit';
	const CATEGORY = 'category';
	const DESCRIPTION = 'description';
	const DONOTCALL = 'doNotCall';
	const DONOTMAIL = 'doNotMail';
	const DONOTEMAIL = 'doNotEmail';
	const EMAIL1 = 'email1';
	const EMAIL2 = 'email2';
	const NAME = 'name';
	const NOTE = 'note';
	const ORIGINATINGLEAD = 'originatingLead';
	const PARENTACCOUNT = 'parentAccount';
	const PRIMARYCONTACT = 'primaryContact';
	const OWNER = 'owner';
	const REFERRER = 'referrer';
	const SOURCE = 'source';
	const STATE = 'status';
	const WEBSITE = 'website';
	const ADDRESSES = 'addresses';
	const TELEPHONES = 'telephones';

	/**
	 * @var EntityManager
	 * */
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
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\Account' ) )->setObject( new Account() );
		
		$this->init();
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * Initializes form elements
	 *
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::ID
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::ACCOUNTGROUP,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Group',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\AccountGroup',
				'property' => 'description',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array(
							'description' => 'asc'
						)
					)
				)
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::ACCOUNTTYPE,
			'options' => array(
				'label' => 'Account Type',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => AccountType::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Account Type'
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::BUSINESSUNIT,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Business Unit',
				'label_attributes' => array(
					'class' => 'input-label'
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
			'type' => 'Select',
			'name' => self::CATEGORY,
			'options' => array(
				'label' => 'Category',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => AccountCategory::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Category'
			)
		) );
		$this->add( array(
			'type' => 'Textarea',
			'name' => self::DESCRIPTION,
			'options' => array(
				'label' => 'Description',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 16,
				'title' => 'Description'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::DONOTCALL,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Do Not Call',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Do Not Call'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::DONOTMAIL,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Do Not Mail',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Do Not Mail'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::DONOTEMAIL,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Do Not Email',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Do Not Email'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::EMAIL1,
			'options' => array(
				'label' => 'Email 1',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 60,
				'title' => 'Email 1'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::EMAIL2,
			'options' => array(
				'label' => 'Email 2',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 60,
				'title' => 'Email 2'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::NAME,
			'options' => array(
				'label' => 'Name',
				'label_attributes' => array(
					'class' => 'input-label required'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 40,
				'title' => 'Name'
			)
		) );
		$this->add( array(
			'type' => 'Textarea',
			'name' => self::NOTE,
			'options' => array(
				'label' => 'Notes',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 100,
				'rows' => 3,
				'title' => 'Notes'
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::ORIGINATINGLEAD,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Originating Lead',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\Lead',
				'property' => 'fullName',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array(
							'lastName' => 'asc'
						)
					)
				)
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::OWNER,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Ownership',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\User',
				'property' => 'fullName',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array(
							'lastName' => 'asc'
						)
					)
				)
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::PARENTACCOUNT,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Parent Account',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\Account',
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
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::PRIMARYCONTACT,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Primary Contact',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\Contact',
				'property' => 'displayName',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array(
							'lastName' => 'asc'
						)
					)
				)
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::REFERRER,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Referrer',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\Account',
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
			'type' => 'Select',
			'name' => self::SOURCE,
			'options' => array(
				'label' => 'Source',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => AccountSource::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Source'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::STATE,
			'options' => array(
				'label' => 'Status',
				'label_attributes' => array(
					'class' => 'input-label required'
				),
				'empty_option' => 'Select...',
				'value_options' => AccountStatus::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::WEBSITE,
			'options' => array(
				'label' => 'Website',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 60,
				'title' => 'Website'
			)
		) );
		
		$telephoneFieldset = new TelephoneFieldset( $this->em );
		$tfc = new Collection();
		$tfc->setName( self::TELEPHONES );
		$tfc->setOptions( array(
			'label' => 'Say Hello',
			'target_element' => $telephoneFieldset,
			'count' => 1,
			'allow_add' => true,
			'allow_remove' => true,
			'should_create_template' => true,
			'template_placeholder' => '__telephone__'
		) );
		$this->add( $tfc );
		
		$addressFieldset = new AddressFieldset( $this->em );
		$afc = new Collection();
		$afc->setName( self::ADDRESSES );
		$afc->setOptions( array(
			'label' => 'Addresses',
			'target_element' => $addressFieldset,
			'count' => 1,
			'allow_add' => true,
			'allow_remove' => true,
			'should_create_template' => true,
			'template_placeholder' => '__address__'
		) );
		$this->add( $afc );
	}

	/**
	 * Provides validation instructions
	 *
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::ACCOUNTGROUP => array(
				'required' => false
			),
			self::ACCOUNTTYPE => array(
				'required' => false
			),
			self::BUSINESSUNIT => array(
				'required' => false
			),
			self::CATEGORY => array(
				'required' => false
			),
			self::DESCRIPTION => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array()
			),
			self::DONOTCALL => array(
				'required' => false
			),
			self::DONOTMAIL => array(
				'required' => false
			),
			self::DONOTEMAIL => array(
				'required' => false
			),
			self::EMAIL1 => array(
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
			self::EMAIL2 => array(
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
			self::NAME => array(
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
							'min' => 5,
							'max' => 160
						)
					)
				)
			),
			self::NOTE => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					)
				),
				'validators' => array()
			),
			self::ORIGINATINGLEAD => array(
				'required' => false
			),
			self::OWNER => array(
				'required' => false
			),
			self::PARENTACCOUNT => array(
				'required' => false
			),
			self::PRIMARYCONTACT => array(
				'required' => false
			),
			self::REFERRER => array(
				'required' => false
			),
			self::SOURCE => array(
				'required' => false
			),
			self::STATE => array(
				'required' => true
			),
			self::WEBSITE => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'StripTags'
					),
					array(
						'name' => 'StringTrim'
					),
					array(
						'name' => 'UriNormalize'
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
						'name' => 'Hostname',
						'allow' => Hostname::ALLOW_DNS
					)
				)
			)
		);
		
		return $spec;
	}
}
?>