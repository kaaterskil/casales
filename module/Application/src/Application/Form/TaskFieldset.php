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
 * @version     SVN $Id: TaskFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\BaseActivityFieldset;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;
use Application\Model\TaskStatus;

/**
 * Task fieldset for forms
 *
 * Provides additional form fields over the basic activity fieldset
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: TaskFieldset.php 13 2013-08-05 22:53:55Z  $
 * @see			BaseActivityFieldset
 */
class TaskFieldset extends BaseActivityFieldset {
	const SCHEDULEDSTART = 'scheduledStart';
	const SCHEDULEDEND = 'scheduledEnd';
	const ACTUALSTART = 'actualStart';
	const ACTUALEND = 'actualEnd';
	const PRIORITY = 'priority';
	const STATUS = 'status';
	const PERCENTCOMPLETE = 'percentComplete';
	
	/* ---------- Constructor ---------- */
	
	/**
     * Constructor
     *
     * @param EntityManager $em
     * @param string $clazz
     */
	public function __construct(EntityManager $em, $clazz) {
		parent::__construct( $em, $clazz );
	}
	
	/* ---------- Methods ---------- */
	
	/**
     * @return void
     * @see \Application\Form\BaseActivityFieldset::init()
     */
	public function init() {
		parent::init();
		
		$this->add( array(
			'type' => 'Date',
			'name' => self::SCHEDULEDSTART,
			'options' => array(
				'label' => 'Scheduled Start',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Scheduled Start'
			)
		) );
		$this->add( array(
			'type' => 'Date',
			'name' => self::SCHEDULEDEND,
			'options' => array(
				'label' => 'Due',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Due'
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
			'type' => 'Select',
			'name' => self::STATUS,
			'options' => array(
				'label' => 'Status',
				'label_attributes' => array(
					'class' => 'input-label required'
				),
				'empty_option' => 'Select...',
				'value_options' => TaskStatus::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::PERCENTCOMPLETE,
			'options' => array(
				'label' => 'Pct. Complete',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 5,
				'title' => 'Percent Complete'
			)
		) );
	}

	/**
     * @return array
     * @see \Application\Form\BaseActivityFieldset::getInputFilterSpecification()
     */
	public function getInputFilterSpecification() {
		$array1 = parent::getInputFilterSpecification();
		
		$array2 = array(
			self::SCHEDULEDSTART => array(
				'required' => false
			),
			self::SCHEDULEDEND => array(
				'required' => false
			),
			self::ACTUALSTART => array(
				'required' => false
			),
			self::ACTUALEND => array(
				'required' => false
			),
			self::PRIORITY => array(
				'required' => false
			),
			self::STATUS => array(
				'required' => true
			),
			self::PERCENTCOMPLETE => array(
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
			)
		);
		
		return array_merge( $array1, $array2 );
	}
}
?>