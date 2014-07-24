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
 * @version     SVN $Id: AppointmentFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\BaseActivityFieldset;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Application\Model\AppointmentStatus;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;
use Zend\Form\Element\DateTime;

/**
 * Appointment fieldset for forms
 *
 * Provides additional form fields over the basic activity fieldset
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AppointmentFieldset.php 13 2013-08-05 22:53:55Z  $
 * @see			BaseActivityFieldset
 */
class AppointmentFieldset extends BaseActivityFieldset {
	const SCHEDULEDSTART = 'scheduledStart';
	const SCHEDULEDEND = 'scheduledEnd';
	const ACTUALSTART = 'actualStart';
	const ACTUALEND = 'actualEnd';
	const PRIORITY = 'priority';
	const STATUS = 'status';
	const LOCATION = 'location';
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 * @param unknown $clazz
	 */
	public function __construct(EntityManager $em, $clazz) {
		parent::__construct( $em, $clazz );
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * Initializes form elements
	 *
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		parent::init();
		
		$this->remove( BaseActivityFieldset::OWNER );
		
		$this->add( array(
			'type' => 'DateTime',
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
			'type' => 'DateTime',
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
			'type' => 'Text',
			'name' => self::LOCATION,
			'options' => array(
				'label' => 'Location',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 153,
				'title' => 'Location'
			)
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::OWNER
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
				'value_options' => ActivityPriority::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Priority'
			)
		) );
		$this->add( array(
			'type' => 'DateTime',
			'name' => self::SCHEDULEDEND,
			'options' => array(
				'label' => 'End Time',
				'label_attributes' => array(
					'class' => 'input-label required'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'End Time'
			)
		) );
		$this->add( array(
			'type' => 'DateTime',
			'name' => self::SCHEDULEDSTART,
			'options' => array(
				'label' => 'Start Time',
				'label_attributes' => array(
					'class' => 'input-label required'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Start Time'
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
				'value_options' => AppointmentStatus::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
			)
		) );
	}

	/**
	 * Provides validation instructions
	 *
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$array1 = parent::getInputFilterSpecification();
		
		$array2 = array(
			self::ACTUALEND => array(
				'required' => false
			),
			self::ACTUALSTART => array(
				'required' => false
			),
			self::LOCATION => array(
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
			),
			self::PRIORITY => array(
				'required' => false
			),
			self::SCHEDULEDEND => array(
				'required' => true
			),
			self::SCHEDULEDSTART => array(
				'required' => true
			),
			self::STATUS => array(
				'required' => true
			)
		);
		
		return array_merge( $array1, $array2 );
	}
}
?>