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
 * @version     SVN $Id: CampaignFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\Campaign;
use Application\Model\CampaignState;
use Application\Model\CampaignStatus;
use Application\Model\CampaignType;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * Campaign fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: CampaignFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class CampaignFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'campaign';
	const ID = 'id';
	const ACTUALEND = 'actualEnd';
	const ACTUALSTART = 'actualStart';
	const BUSINESSUNIT = 'businessUnit';
	const CODENAME = 'codeName';
	const DESCRIPTION = 'description';
	const EXPECTEDRESPONSE = 'expectedResponse';
	const EXPECTEDREVENUE = 'expectedRevenue';
	const MESSAGE = 'message';
	const NAME = 'name';
	const OBJECTIVE = 'objective';
	const OWNER = 'owner';
	const PROPOSEDEND = 'proposedEnd';
	const PROPOSEDSTART = 'proposedStart';
	const STATE = 'state';
	const STATUS = 'status';
	const TYPE = 'type';

	/**
	 * @var EntityManager
	 */
	private $em;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		parent::__construct( self::FIELDSETNAME );
		$this->em = $em;
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\Campaign' ) )->setObject( new Campaign() );
		$this->init();
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
			'type' => 'Date',
			'name' => self::ACTUALEND,
			'options' => array(
				'label' => 'Actual End',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Actual End'
			)
		) );
		$this->add( array(
			'type' => 'Date',
			'name' => self::ACTUALSTART,
			'options' => array(
				'label' => 'Actual Start',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Actual Start'
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
			'name' => self::CODENAME,
			'options' => array(
				'label' => 'Campaign Code',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 30,
				'title' => 'Campaign Code'
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
				'rows' => 8,
				'title' => 'Description'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::EXPECTEDRESPONSE,
			'options' => array(
				'label' => 'Expected Response',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 5,
				'title' => 'Expected Response'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::EXPECTEDREVENUE,
			'options' => array(
				'label' => 'Expected Revenue',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'title' => 'Expected Revenue'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::MESSAGE,
			'options' => array(
				'label' => 'Message',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 100,
				'title' => 'Message'
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
			'type' => 'Textarea',
			'name' => self::OBJECTIVE,
			'options' => array(
				'label' => 'Offer',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 3,
				'title' => 'Offer'
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::OWNER,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Owner',
				'label_attributes' => array(
					'class' => 'input-label required'
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
			'type' => 'Date',
			'name' => self::PROPOSEDEND,
			'options' => array(
				'label' => 'Proposed End',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Proposed End'
			)
		) );
		$this->add( array(
			'type' => 'Date',
			'name' => self::PROPOSEDSTART,
			'options' => array(
				'label' => 'Proposed Start',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Proposed Start'
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
				'value_options' => CampaignStatus::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::TYPE,
			'options' => array(
				'label' => 'Type',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => CampaignType::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Type'
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
			self::ACTUALEND => array(
				'required' => false
			),
			self::ACTUALSTART => array(
				'required' => false
			),
			self::BUSINESSUNIT => array(
				'required' => false
			),
			self::CODENAME => array(
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
			self::EXPECTEDRESPONSE => array(
				'required' => false,
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
			self::EXPECTEDREVENUE => array(
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
			self::MESSAGE => array(
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
							'max' => 128
						)
					)
				)
			),
			self::OBJECTIVE => array(
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
			self::OWNER => array(
				'required' => true
			),
			self::PROPOSEDEND => array(
				'required' => false
			),
			self::PROPOSEDSTART => array(
				'required' => false
			),
			self::STATUS => array(
				'required' => true
			),
			self::TYPE => array(
				'required' => false
			)
		);
		
		return $spec;
	}
}
?>