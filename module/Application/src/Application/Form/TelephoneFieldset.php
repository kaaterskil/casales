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
 * @version     SVN $Id: TelephoneFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\Telephone;
use Application\Model\TelephoneType;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator;

/**
 * Telephone fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TelephoneFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class TelephoneFieldset extends Fieldset implements InputFilterProviderInterface {
	const ID = 'id';
	const TYPE = 'type';
	const PHONE = 'phone';
	const ISPRIMARY = 'isPrimary';
	
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
		$this->setHydrator( new DoctrineHydrator( $em, 'Application\Model\Telephone' ) )->setObject( new Telephone() );
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
			'type' => 'Select',
			'name' => self::TYPE,
			'options' => array(
				'label' => 'Type',
				'label_attributes' => array(
					'class' => 'input-label collection-label'
				),
				'empty_option' => 'Select...',
				'value_options' => TelephoneType::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Type'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::PHONE,
			'options' => array(
				'label' => 'Phone',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'title' => 'Phone'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::ISPRIMARY,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 1,
				'unchecked_value' => 0,
				'label' => 'Primary',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Primary'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::TYPE => array(
				'required' => false
			),
			self::PHONE => array(
				'required' => false,
				'filters' => array(
					array(
						'name' => 'Digits'
					)
				),
				'validators' => array(
					array(
						'name' => 'Regex',
						'options' => array(
							'pattern' => '/^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/'
						)
					)
				)
			)
		);
		return $spec;
	}
}
?>