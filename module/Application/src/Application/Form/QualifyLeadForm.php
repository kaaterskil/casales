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
 * @version     SVN $Id: QualifyLeadForm.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\LeadState;
use Application\Model\LeadStatus;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Qualify Lead input form
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: QualifyLeadForm.php 13 2013-08-05 22:53:55Z  $
 */
class QualifyLeadForm extends Form implements InputFilterProviderInterface {
	CONST QUALIFY = 'qualify';
	const QUALIFYSTATUS = 'qualifyStatus';
	const DISQUALIFYSTATUS = 'disqualifyStatus';
	const CREATEACCOUNT = 'createAccount';
	const CREATECONTACT = 'createContact';
	const CREATEOPPORTUNITY = 'createOpportunity';
	const SUBMIT = 'qualify_lead_submit_btn';
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$this->add( array(
			'type' => 'Radio',
			'name' => self::QUALIFY,
			'options' => array(
				'label' => 'Qualification',
				'label_attributes' => array(
					'class' => 'input-label qualify-radio'
				),
				'value_options' => array(
					LeadState::QUALIFIED => 'Qualify and convert into the following records',
					LeadState::DISQUALIFIED => 'Disqualify'
				)
			),
			'attributes' => array(
				'value' => '0'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::QUALIFYSTATUS,
			'options' => array(
				'label' => 'Status',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => array(
					LeadStatus::QUALIFIED => LeadStatus::QUALIFIED,
					LeadStatus::NEWLEAD => LeadStatus::NEWLEAD,
					LeadStatus::CONTACTED => LeadStatus::CONTACTED
				)
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::CREATEACCOUNT,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Account',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Account'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::CREATECONTACT,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Contact',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Contact'
			)
		) );
		$this->add( array(
			'type' => 'Checkbox',
			'name' => self::CREATEOPPORTUNITY,
			'options' => array(
				'use_hidden_element' => true,
				'checked_value' => 'true',
				'unchecked_value' => 'false',
				'label' => 'Opportunity',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-checkbox',
				'title' => 'Opportunity'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::DISQUALIFYSTATUS,
			'options' => array(
				'label' => 'Status',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => array(
					LeadStatus::LOST => LeadStatus::LOST,
					LeadStatus::CANNOTCONTACT => LeadStatus::CANNOTCONTACT,
					LeadStatus::NOTINTERESTED => LeadStatus::NOTINTERESTED,
					LeadStatus::CANCELED => LeadStatus::CANCELED
				)
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
			)
		) );
		$this->add( array(
			'type' => 'Submit',
			'name' => self::SUBMIT,
			'attributes' => array(
				'class' => 'button mlm',
				'value' => 'Convert',
				'title' => 'Convert'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::CREATEACCOUNT => array(
				'required' => false
			),
			self::CREATECONTACT => array(
				'required' => false
			),
			self::CREATEOPPORTUNITY => array(
				'required' => false
			),
			self::DISQUALIFYSTATUS => array(
				'required' => false
			),
			self::QUALIFY => array(
				'required' => true
			),
			self::QUALIFYSTATUS => array(
				'required' => false
			)
		);
		
		return $spec;
	}
}
?>