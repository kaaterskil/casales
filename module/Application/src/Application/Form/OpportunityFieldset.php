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
 * @version     SVN $Id: OpportunityFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\Account;
use Application\Model\Contact;
use Application\Model\InitialContact;
use Application\Model\LeadPriority;
use Application\Model\Need;
use Application\Model\Opportunity;
use Application\Model\OpportunityPriority;
use Application\Model\OpportunityRating;
use Application\Model\OpportunityState;
use Application\Model\OpportunityStatus;
use Application\Model\OpportunityTimeline;
use Application\Model\PurchaseProcess;
use Application\Model\PurchaseTimeframe;
use Application\Model\SalesStage;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\I18n\Filter\NumberFormat;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * Opportunity fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: OpportunityFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class OpportunityFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'opportunity';
	const ID = 'id';
	const ACCOUNT = 'account';
	const ACTUALCLOSEDATE = 'actualCloseDate';
	const ACTUALVALUE = 'actualValue';
	const BUSINESSUNIT = 'businessUnit';
	const CLOSEPROBABILITY = 'closeProbability';
	const CONFIRMINTEREST = 'confirmInterest';
	const CONTACT = 'contact';
	const CUSTOMERNEED = 'customerNeed';
	const CUSTOMERPAINPOINTS = 'customerPainPoints';
	const DECISIONMAKER = 'decisionMaker';
	const DESCRIPTION = 'description';
	const DEVELOPPROPOSAL = 'developProposal';
	const ESTIMATEDCLOSEDATE = 'estimatedCloseDate';
	const ESTIMATEDVALUE = 'estimatedValue';
	const EVALUATEFIT = 'evaluateFit';
	const FINALDECISIONDATE = 'finalDecisionDate';
	const INITIALCONTACT = 'initialContact';
	const NAME = 'name';
	const NEED = 'need';
	const OPPORTUNITYRATING = 'opportunityRating';
	const ORIGINATINGLEAD = 'originatingLead';
	const OWNER = 'owner';
	const PRESENTPROPOSAL = 'presentProposal';
	const PRIORITY = 'priority';
	const PURCHASEPROCESS = 'purchaseProcess';
	const PURCHASETIMEFRAME = 'purchaseTimeframe';
	const PURSUITDECISION = 'pursuitDecision';
	const QUALIFICATIONCOMMENTS = 'qualificationComments';
	const SALESSTAGE = 'salesStage';
	const SCHEDULEFOLLOWUPPROSPECT = 'scheduleFollowupProspect';
	const SCHEDULEFOLLOWUPQUALIFY = 'scheduleFollowupQualify';
	const SCHEDULEPROPOSALMEETING = 'scheduleProposalMeeting';
	const SENDTHANKYOU = 'sendThankYou';
	const STATE = 'state'; // Not used
	const STATUS = 'status';
	const STEP = 'step';
	const TIMELINE = 'timeline';

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
		parent::__construct();
		$this->em = $em;
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\Opportunity' ) )->setObject( new Opportunity() );
		
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
			'type' => 'Text',
			'name' => self::ACTUALCLOSEDATE,
			'options' => array(
				'label' => 'Actual Close Date',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Actual Close Date'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::ACTUALVALUE,
			'options' => array(
				'label' => 'Actual Value',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'title' => 'Actual Value'
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
			'name' => self::CLOSEPROBABILITY,
			'options' => array(
				'label' => 'Close Probability',
				'label_attributes' => array(
					'class' => 'input-label required'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 5,
				'title' => 'Close Probability'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::CONFIRMINTEREST,
			'options' => array(
				'use_hidden_element' => true,
				'label' => 'Is Interested',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Is Interested'
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
			'type' => 'Textarea',
			'name' => self::CUSTOMERNEED,
			'options' => array(
				'label' => 'Customer Needs',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 4,
				'title' => 'Customer Needs'
			)
		) );
		$this->add( array(
			'type' => 'Textarea',
			'name' => self::CUSTOMERPAINPOINTS,
			'options' => array(
				'label' => 'Pain Points',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 4,
				'title' => 'Pain Points'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::DECISIONMAKER,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Is Decision Maker',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Is Decision Maker'
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
				'rows' => 11,
				'title' => 'Description'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::DEVELOPPROPOSAL,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Develop Proposal',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Develop Proposal'
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
				'title' => 'Estimated Close Date'
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
				'title' => 'Estimated Value'
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
			'name' => self::FINALDECISIONDATE,
			'options' => array(
				'label' => 'Decision Date',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Decision Date'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::INITIALCONTACT,
			'options' => array(
				'label' => 'Initial Contact',
				'label_attributes' => array(
					'class' => 'input-label required'
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
			'name' => self::NAME,
			'options' => array(
				'label' => 'Name',
				'label_attributes' => array(
					'class' => 'input-label required'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 45,
				'title' => 'Name'
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
			'type' => 'Select',
			'name' => self::OPPORTUNITYRATING,
			'options' => array(
				'label' => 'Rating',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => OpportunityRating::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Rating'
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
			'type' => 'Checkbox',
			'name' => self::PRESENTPROPOSAL,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Present Proposal',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Present Proposal'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::PRIORITY,
			'options' => array(
				'label' => 'Priority',
				'label_attributes' => array(
					'class' => 'input-label required'
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
				'label' => 'Purchase Time',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => PurchaseTimeframe::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Purchase Time'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::PURSUITDECISION,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Pursuit Decision',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Pursuit Decision'
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
				'rows' => 5,
				'title' => 'Comments'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::SALESSTAGE,
			'options' => array(
				'label' => 'Stage',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => SalesStage::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Stage'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::SCHEDULEFOLLOWUPPROSPECT,
			'options' => array(
				'label' => 'Prospect Date',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Followup Prospect Date'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::SCHEDULEFOLLOWUPQUALIFY,
			'options' => array(
				'label' => 'Qualify Date',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Schedule Followup Qualify Date'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::SCHEDULEPROPOSALMEETING,
			'options' => array(
				'label' => 'Proposal Date',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Proposal Meeting Date'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::SENDTHANKYOU,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Thank You Sent',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Thank You Sent'
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
				'value_options' => OpportunityStatus::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::STEP,
			'options' => array(
				'label' => 'Sales Step',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'title' => 'Sales Step'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::TIMELINE,
			'options' => array(
				'label' => 'Timeline',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => OpportunityTimeline::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Timeline'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::ACCOUNT => array(
				'required' => false
			),
			self::ACTUALCLOSEDATE => array(
				'required' => false,
				'allow_empty' => true,
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
			self::ACTUALVALUE => array(
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
			self::BUSINESSUNIT => array(
				'required' => false
			),
			self::CLOSEPROBABILITY => array(
				'required' => true,
				'filters' => array(
					array(
						'name' => 'Digits'
					)
				),
				'validators' => array(
					array(
						'name' => 'Between',
						'options' => array(
							'inclusive' => true,
							'min' => 0,
							'max' => 100
						)
					)
				)
			),
			self::CONFIRMINTEREST => array(
				'required' => false
			),
			self::CONTACT => array(
				'required' => false
			),
			self::CUSTOMERNEED => array(
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
			self::CUSTOMERPAINPOINTS => array(
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
			self::DEVELOPPROPOSAL => array(
				'required' => false
			),
			self::ESTIMATEDCLOSEDATE => array(
				'required' => false,
				'allow_empty' => true,
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
			self::FINALDECISIONDATE => array(
				'required' => false,
				'allow_empty' => true,
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
			self::INITIALCONTACT => array(
				'required' => true
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
							'max' => 64
						)
					)
				)
			),
			self::NEED => array(
				'required' => false
			),
			self::OPPORTUNITYRATING => array(
				'required' => false
			),
			self::ORIGINATINGLEAD => array(
				'required' => false
			),
			self::OWNER => array(
				'required' => false
			),
			self::PRESENTPROPOSAL => array(
				'required' => false
			),
			self::PRIORITY => array(
				'required' => true
			),
			self::PURCHASEPROCESS => array(
				'required' => false
			),
			self::PURCHASETIMEFRAME => array(
				'required' => false
			),
			self::PURSUITDECISION => array(
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
			self::SALESSTAGE => array(
				'required' => false
			),
			self::SCHEDULEFOLLOWUPPROSPECT => array(
				'required' => false,
				'allow_empty' => true,
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
							'format' => 'm/d/Y h:i'
						)
					)
				)
			),
			self::SCHEDULEFOLLOWUPQUALIFY => array(
				'required' => false,
				'allow_empty' => true,
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
							'max' => 20
						)
					),
					array(
						'name' => 'Date',
						'options' => array(
							'locale' => 'us',
							'format' => 'm/d/Y h:ia'
						)
					)
				)
			),
			self::SCHEDULEPROPOSALMEETING => array(
				'required' => false,
				'allow_empty' => true,
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
							'format' => 'm/d/Y h:i'
						)
					)
				)
			),
			self::SENDTHANKYOU => array(
				'required' => false
			),
			self::STATUS => array(
				'required' => true
			),
			self::STEP => array(
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
			self::TIMELINE => array(
				'required' => false
			)
		);
		
		return $spec;
	}
}
?>