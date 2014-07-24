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
 * @version     SVN $Id: LoseOpportunityForm.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Application\Model\OpportunityClose;
use Application\Model\OpportunityStatus;

/**
 * Lose Opportunity input form
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: LoseOpportunityForm.php 13 2013-08-05 22:53:55Z  $
 */
class LoseOpportunityForm extends Form implements InputFilterProviderInterface {
	const STATUS = 'status';
	const CLOSEDATE = 'actualEnd';
	const DESCRIPTION = 'description';
	const SUBMIT = 'loseOpportunitySubmitBtn';
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->setHydrator( new ClassMethodsHydrator( false ) )->setObject( new OpportunityClose() );
		$this->setInputFilter( new InputFilter() );
		$this->init();
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$this->add( array(
			'type' => 'Select',
			'name' => self::STATUS,
			'options' => array(
				'label' => 'Reason',
				'label_attributes' => array(
					'class' => 'input-label required'
				),
				'empty_option' => 'Select...',
				'value_options' => array(
					OpportunityStatus::CANCELED => OpportunityStatus::CANCELED,
					OpportunityStatus::NOTINTERESTED => OpportunityStatus::NOTINTERESTED
				)
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Reason'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::CLOSEDATE,
			'options' => array(
				'label' => 'Close Date',
				'label_attributes' => array(
					'class' => 'input-label required'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 15,
				'title' => 'Close Date'
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
				'cols' => 37,
				'rows' => 5,
				'title' => 'Description'
			)
		) );
		$this->add( array(
			'type' => 'Submit',
			'name' => self::SUBMIT,
			'attributes' => array(
				'class' => 'button mlm',
				'value' => 'OK',
				'title' => 'OK'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::STATUS => array(
				'required' => true
			),
			self::CLOSEDATE => array(
				'required' => true,
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
			)
		);
		
		return $spec;
	}
}