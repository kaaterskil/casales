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
 * @version     SVN $Id: LetterFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\InteractionFieldset;
use Application\Model\Address;
use Doctrine\ORM\EntityManager;
use Application\Model\LetterStatus;

/**
 * Letter fieldset for forms
 *
 * Provides additional form fields over the basic activity fieldset
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: LetterFieldset.php 13 2013-08-05 22:53:55Z  $
 * @see			BaseActivityFieldset
 */
class LetterFieldset extends InteractionFieldset {
	const ADDRESS = 'address';
	
	/* ---------- Constructor ---------- */

	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$clazz = 'Application\Model\LetterInteraction';
		parent::__construct( $em, $clazz );
	}
	
	/* ---------- Methods ---------- */

	/**
	 * @return void
	 * @see \Application\Form\InteractionFieldset::init()
	 */
	public function init() {
		parent::init();
		
		$this->remove(self::STATUS);

		$this->add( array(
			'type' => 'Text',
			'name' => self::ADDRESS,
			'options' => array(
				'label' => 'Address',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 44,
				'title' => 'Address'
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
				'value_options' => LetterStatus::toArray()
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
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 100,
						),
					),
				),
			),
			self::STATUS => array(
				'required' => true
			),
		);
		
		return array_merge( $array1, $array2 );
	}
}
?>