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
 * @version     SVN $Id: AddressFieldSet.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\Address;
use Application\Model\AddressType;
use Application\Model\Region;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * Address fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AddressFieldSet.php 13 2013-08-05 22:53:55Z  $
 */
class AddressFieldset extends Fieldset implements InputFilterProviderInterface {
	const ID = 'id';
	const TYPE = 'type';
	const ADDRESS1 = 'address1';
	const ADDRESS2 = 'address2';
	const ADDRESS3 = 'address3';
	const CITY = 'city';
	const REGION = 'region';
	const POSTAL_CODE = 'postalCode';
	const CREATIONDATE = 'creationDate';
	const LASTUPDATEDATE = 'lastUpdateDate';

	/**
	 * @var EntityManager
	 */
	private $em;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 * @param string $name
	 * @param array $options
	 */
	public function __construct(EntityManager $em, $name = null, $options = array()) {
		parent::__construct( $name, $options );
		$this->em = $em;
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\Address' ) )->setObject( new Address() );
		
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
			'type' => 'Select',
			'name' => self::TYPE,
			'options' => array(
				'label' => 'Type',
				'label_attributes' => array(
					'class' => 'input-label collection-label'
				),
				'empty_option' => 'Select...',
				'value_options' => AddressType::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Type'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::ADDRESS1,
			'options' => array(
				'label' => 'Address',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Address Line 1'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::ADDRESS2,
			'options' => array(
				'label' => ' ',
				'label_attributes' => array(
					'class' => 'input-label no-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Address Line 2'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::ADDRESS3,
			'options' => array(
				'label' => ' ',
				'label_attributes' => array(
					'class' => 'input-label no-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 50,
				'title' => 'Address Line 3'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::CITY,
			'options' => array(
				'label' => 'City',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 40,
				'title' => 'City'
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::REGION,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'State',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\Region',
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
			'name' => self::POSTAL_CODE,
			'options' => array(
				'label' => 'Postal Code',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Postal Code'
			)
		) );
	}

	/**
	 * Returns an array InputFilter specification
	 *
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::ID => array(
				'required' => false
			),
			self::CREATIONDATE => array(
				'required' => false
			),
			self::LASTUPDATEDATE => array(
				'required' => false
			),
			self::TYPE => array(
				'required' => false
			),
			self::ADDRESS1 => array(
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
			self::ADDRESS2 => array(
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
			self::ADDRESS3 => array(
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
			self::CITY => array(
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
			self::REGION => array(
				'required' => false,
				'allow_empty' => true,
				'filters' => array(
					array(
						'name' => 'Digits'
					)
				),
				'validators' => array()
			),
			self::POSTAL_CODE => array(
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
							'max' => 12
						)
					),
					array(
						'name' => 'Regex',
						'options' => array(
							'pattern' => '/^[0-9]{5}(-[0-9]{4})?$/'
						)
					)
				)
			)
		);
		
		return $spec;
	}
}
?>