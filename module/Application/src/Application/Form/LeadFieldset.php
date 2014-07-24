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
 * @version     SVN $Id: LeadFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\Account;
use Application\Model\Contact;
use Application\Model\InitialContact;
use Application\Model\Lead;
use Application\Model\LeadQuality;
use Application\Model\LeadPriority;
use Application\Model\LeadSource;
use Application\Model\LeadState;
use Application\Model\LeadStatus;
use Application\Model\Need;
use Application\Model\Opportunity;
use Application\Model\PurchaseProcess;
use Application\Model\PurchaseTimeframe;
use Application\Model\SalesStage;
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

/**
 * Lead fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: LeadFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class LeadFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'lead';
	const ID = 'id';
	const ACCOUNT = 'account';
	const BUSINESSUNIT = 'businessUnit';
	const COMPANYNAME = 'companyName';
	const CONFIRMINTEREST = 'confirmInterest';
	const CONTACT = 'contact';
	const DECISIONMAKER = 'decisionMaker';
	const DESCRIPTION = 'description';
	const DONOTEMAIL = 'doNotEmail';
	const DONOTMAIL = 'doNotMail';
	const DONOTPHONE = 'doNotPhone';
	const EMAIL1 = 'email1';
	const EMAIL2 = 'email2';
	const ESTIMATEDCLOSEDATE = 'estimatedCloseDate';
	const ESTIMATEDVALUE = 'estimatedValue';
	const EVALUATEFIT = 'evaluateFit';
	const FIRSTNAME = 'firstName';
	const FULLNAME = 'fullName';
	const INITIALCONTACT = 'initialContact';
	const JOBTITLE = 'jobTitle';
	const LASTNAME = 'lastName';
	const LEADQUALITY = 'leadQuality';
	const LEADSOURCE = 'leadSource';
	const MIDDLENAME = 'middleName';
	const NEED = 'need';
	const OPPORTUNITY = 'opportunity';
	const OWNER = 'owner';
	const PREFIX = 'prefix';
	const PRIORITY = 'priority';
	const PURCHASEPROCESS = 'purchaseProcess';
	const PURCHASETIMEFRAME = 'purchaseTimeframe';
	const QUALIFICATIONCOMMENTS = 'qualificationComments';
	const REVENUE = 'revenue';
	const SALESSTAGE = 'salesStage';
	const SALESSTAGECODE = 'salesStageCode';
	const SALUTATION = 'salutation';
	const SCHEDULEFOLLOWUPPROSPECT = 'scheduleFollowupProspect';
	const SCHEDULEFOLLOWUPQUALIFY = 'scheduleFollowupQualify';
	const STATUS = 'status';
	const SUFFIX = 'suffix';
	const WEBSITE = 'website';
	const TELEPHONE = 'telephones';
	const ADDRESS = 'addresses';

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
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\Lead' ) )->setObject( new Lead() );
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
	 * Initializes form elements, including the Address and Telephone field sets
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
			'name' => self::ACCOUNT,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Account',
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
			'type' => 'Text',
			'name' => self::COMPANYNAME,
			'options' => array(
				'label' => 'Company',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 40,
				'title' => 'Company'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::CONFIRMINTEREST,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Confirm Interest',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Confirm Interest'
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::CONTACT,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Contact',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->getEntityManager(),
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
			'type' => 'Checkbox',
			'name' => self::DECISIONMAKER,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Decision Maker',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Decision Maker'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::DESCRIPTION,
			'options' => array(
				'label' => 'Description',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 153,
				'title' => 'Description'
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
			'name' => self::DONOTPHONE,
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
				'size' => 50,
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
				'size' => 50,
				'title' => 'Email 2'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::ESTIMATEDCLOSEDATE,
			'options' => array(
				'label' => 'Est. Close Date',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Est. Close Date'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::ESTIMATEDVALUE,
			'options' => array(
				'label' => 'Est. Value',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'title' => 'Est. Value'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::EVALUATEFIT,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Evaluate Fit',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Evaluate Fit'
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
			'type' => 'Text',
			'name' => self::FULLNAME,
			'options' => array(
				'label' => 'Full Name',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 40,
				'title' => 'Full Name'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::INITIALCONTACT,
			'options' => array(
				'label' => 'Initial Contact',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => InitialContact::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Initial Contact'
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
					'class' => 'input-label'
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
			'name' => self::LEADQUALITY,
			'options' => array(
				'label' => 'Lead Quality',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => LeadQuality::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Lead Quality'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::LEADSOURCE,
			'options' => array(
				'label' => 'Lead Source',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => LeadSource::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Lead Source'
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
			'type' => 'Select',
			'name' => self::NEED,
			'options' => array(
				'label' => 'Need',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => Need::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Need'
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::OPPORTUNITY,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Opportunity',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->getEntityManager(),
				'target_class' => 'Application\Model\Opportunity',
				'property' => 'name',
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
			'type' => 'Text',
			'name' => self::PREFIX,
			'options' => array(
				'label' => 'Prefix',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 15,
				'title' => 'Prefix'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::PRIORITY,
			'options' => array(
				'label' => 'Priority',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => LeadPriority::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Priority'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::PURCHASEPROCESS,
			'options' => array(
				'label' => 'Purchase Process',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => PurchaseProcess::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Purchase Process'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::PURCHASETIMEFRAME,
			'options' => array(
				'label' => 'Timeframe',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => PurchaseTimeframe::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Purchase Timeframe'
			)
		) );
		$this->add( array(
			'type' => 'Textarea',
			'name' => self::QUALIFICATIONCOMMENTS,
			'options' => array(
				'label' => 'Comments',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 3,
				'title' => 'Comments'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::REVENUE,
			'options' => array(
				'label' => 'Revenue',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'title' => 'Revenue'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::SALESSTAGE,
			'options' => array(
				'label' => 'Sales Stage',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => SalesStage::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Sales Stage'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::SALESSTAGECODE,
			'options' => array(
				'label' => 'Sales Stage Code',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 40,
				'title' => 'Sales Stage Code'
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
			'type' => 'Datetime',
			'name' => self::SCHEDULEFOLLOWUPPROSPECT,
			'options' => array(
				'label' => 'Prospect Followup',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Prospect Followup Date'
			)
		) );
		$this->add( array(
			'type' => 'Datetime',
			'name' => self::SCHEDULEFOLLOWUPQUALIFY,
			'options' => array(
				'label' => 'Qualify Followup',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Qualify Followup Date'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::STATUS,
			'options' => array(
				'label' => 'Status',
				'label_attributes' => array(
					'class' => 'input-label required'
				),
				'empty_option' => 'Select...',
				'value_options' => LeadStatus::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::SUFFIX,
			'options' => array(
				'label' => 'Suffix',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 25,
				'title' => 'Suffix'
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
				'size' => 40,
				'title' => 'Website'
			)
		) );
		
		$telephoneFieldset = new TelephoneFieldset( $this->em );
		$tfc = new Collection();
		$tfc->setName( self::TELEPHONE );
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
		$afc->setName( self::ADDRESS );
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
	 * Returns an array InputFilter specification
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::ACCOUNT => array(
				'required' => false
			),
			self::BUSINESSUNIT => array(
				'required' => false
			),
			self::COMPANYNAME => array(
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
			self::CONFIRMINTEREST => array(
				'required' => false
			),
			self::CONTACT => array(
				'required' => false
			),
			self::DECISIONMAKER => array(
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
			self::DONOTEMAIL => array(
				'required' => false
			),
			self::DONOTMAIL => array(
				'required' => false
			),
			self::DONOTPHONE => array(
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
			self::ESTIMATEDCLOSEDATE => array(
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
							'max' => 10
						)
					),
					array(
						'name' => 'Date',
						'options' => array(
							'locale' => 'us',
							'format' => 'm/d/Y'
						)
					)
				)
			),
			self::ESTIMATEDVALUE => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'NumberFormat',
						'options' => array(
							'locale' => 'en_US',
							'style' => \NumberFormatter::CURRENCY
						)
					)
				),
				'validators' => array()
			),
			self::EVALUATEFIT => array(
				'required' => false
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
			self::FULLNAME => array(
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
			self::INITIALCONTACT => array(
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
			self::LEADQUALITY => array(
				'required' => false
			),
			self::LEADSOURCE => array(
				'required' => false
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
			self::NEED => array(
				'required' => false
			),
			self::OPPORTUNITY => array(
				'required' => false
			),
			self::OWNER => array(
				'required' => false
			),
			self::PREFIX => array(
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
							'max' => 10
						)
					)
				)
			),
			self::PRIORITY => array(
				'required' => false
			),
			self::PURCHASEPROCESS => array(
				'required' => false
			),
			self::PURCHASETIMEFRAME => array(
				'required' => false
			),
			self::QUALIFICATIONCOMMENTS => array(
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
			self::REVENUE => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'NumberFormat',
						'options' => array(
							'locale' => 'en_US',
							'style' => \NumberFormatter::CURRENCY
						)
					)
				),
				'validators' => array()
			),
			self::SALESSTAGE => array(
				'required' => false
			),
			self::SALESSTAGECODE => array(
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
							'max' => 30
						)
					)
				)
			),
			self::SALUTATION => array(
				'required' => false
			),
			self::SCHEDULEFOLLOWUPPROSPECT => array(
				'required' => false
			),
			self::SCHEDULEFOLLOWUPQUALIFY => array(
				'required' => false
			),
			self::STATUS => array(
				'required' => true
			),
			self::SUFFIX => array(
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
							'max' => 30
						)
					)
				)
			),
			self::WEBSITE => array(
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