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
 * @version     SVN $Id: VisitFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\InteractionFieldset;
use Application\Model\Address;
use Doctrine\ORM\EntityManager;
use Application\Model\AppointmentStatus;

/**
 * Visit fieldset for forms
 *
 * Provides additional form fields over the basic activity fieldset
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: VisitFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class VisitFieldset extends InteractionFieldset {
	const ADDRESS = 'address';
	const STATUS = 'status';
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$clazz = 'Application\Model\VisitInteraction';
		parent::__construct( $em, $clazz );
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return void
	 * @see \Application\Form\InteractionFieldset::init()
	 */
	public function init() {
		parent::init();
		
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::ADDRESS,
			'attributes' => array(
				'class' => 'input-select activityAddress'
			),
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Address',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->getEntityManager(),
				'target_class' => 'Application\Model\Address',
				'property' => 'address1',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array(
							'city' => 'asc',
							'address1' => 'asc'
						)
					)
				)
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::STATUS,
			'options' => array(
				'label' => 'Status',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => AppointmentStatus::toArray()
			),
			'attributes' => array(
				'class' => 'input-select activityStatus',
				'title' => 'Status'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Application\Form\InteractionFieldset::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$array1 = parent::getInputFilterSpecification();
		
		$array2 = array(
			self::ADDRESS => array(
				'required' => false
			),
			self::STATUS => array(
				'required' => true
			)
		);
		
		return array_merge( $array1, $array2 );
	}
}
?>