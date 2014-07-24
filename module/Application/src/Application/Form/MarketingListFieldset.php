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
 * @version     SVN $Id: MarketingListFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\ListStatus;
use Application\Model\MarketingList;
use Application\Model\MemberType;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * Marketing List fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: MarketingListFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class MarketingListFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'marketingList';
	const ID = 'id';
	const BUSINESSUNIT = 'businessUnit';
	const DESCRIPTION = 'description';
	const LOCKSTATUS = 'lockStatus';
	const MEMBERTYPE = 'memberType';
	const NAME = 'name';
	const OWNER = 'owner';
	const PURPOSE = 'purpose';
	const SOURCE = 'source';
	const STATUS = 'status';

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
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\MarketingList' ) )->setObject( new MarketingList() );
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
				'rows' => 18,
				'title' => 'Description'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::LOCKSTATUS,
			'options' => array(
				'use_hidden_element' => true,
				'label' => 'Locked',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Locked'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::MEMBERTYPE,
			'options' => array(
				'label' => 'Member Type',
				'label_attributes' => array(
					'class' => 'input-label required'
				),
				'empty_option' => 'Select...',
				'value_options' => MemberType::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Member Type'
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
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::OWNER,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Ownership',
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
			'type' => 'Textarea',
			'name' => self::PURPOSE,
			'options' => array(
				'label' => 'Purpose',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 3,
				'title' => 'Purpose'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::SOURCE,
			'options' => array(
				'label' => 'Source',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 55,
				'title' => 'Source'
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
				'value_options' => ListStatus::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
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
			self::BUSINESSUNIT => array(
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
			self::LOCKSTATUS => array(
				'required' => false
			),
			self::MEMBERTYPE => array(
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
							'max' => 128
						)
					)
				)
			),
			self::OWNER => array(
				'required' => true
			),
			self::PURPOSE => array(
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
			self::SOURCE => array(
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
			self::STATUS => array(
				'required' => true
			)
		);
		
		return $spec;
	}
}
?>