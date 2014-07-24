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
 * @version     SVN $Id: BusinessUnitFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\BusinessUnit;
use Application\Model\User;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;
use Zend\Validator\Hostname;

/**
 * Business Unit fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: BusinessUnitFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class BusinessUnitFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'businessUnit';
	const ID = 'id';
	const COSTCENTER = 'costCenter';
	const DESCRIPTION = 'description';
	const DISABLEDREASON = 'disabledReason';
	const DIVISIONNAME = 'divisionName';
	const EMAIL = 'email';
	const ISDISABLED = 'isDisabled';
	const NAME = 'name';
	const PARENTBUSINESSUNIT = 'parentBusinessUnit';
	const WEBSITE = 'website';

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
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\BusinessUnit' ) )->setObject( new User() );
		
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
			'type' => 'Text',
			'name' => self::COSTCENTER,
			'options' => array(
				'label' => 'Cost Center',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 55,
				'title' => 'Cost Center'
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
				'rows' => 20,
				'title' => 'Description'
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
				'size' => 55,
				'title' => 'Disabled Reason'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::DIVISIONNAME,
			'options' => array(
				'label' => 'Division',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 55,
				'title' => 'Division'
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
				'size' => 55,
				'title' => 'Email'
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
			'name' => self::PARENTBUSINESSUNIT,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Parent Business',
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
			'name' => self::WEBSITE,
			'options' => array(
				'label' => 'Website',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 55,
				'title' => 'Website'
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
			self::COSTCENTER => array(
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
			self::DIVISIONNAME => array(
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
			self::ISDISABLED => array(
				'required' => false
			),
			self::NAME => array(
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
			self::PARENTBUSINESSUNIT => array(
				'required' => false
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