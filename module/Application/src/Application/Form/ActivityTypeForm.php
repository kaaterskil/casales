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
 * @version     SVN $Id: ActivityTypeForm.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\ActivityType;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Crypt\PublicKey\Rsa\PublicKey;

/**
 * Activity Type input form
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: ActivityTypeForm.php 13 2013-08-05 22:53:55Z  $
 */
class ActivityTypeForm extends Form implements InputFilterProviderInterface {
	const ACTIVITYTYPE = 'activityType';
	const SUBMIT = 'submit_btn';
	
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
	 * Initializes the form
	 *
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		// Set form attributes
		$this->setAttributes( array(
			'method' => 'post',
			'name' => 'activity-create'
		) );
		
		// Set form elements
		$this->add( array(
			'type' => 'Select',
			'name' => self::ACTIVITYTYPE,
			'options' => array(
				'label' => 'Create Activity',
				'label_attributes' => array(
					'class' => 'input-label lfloat'
				),
				'empty_option' => 'Select...',
				'value_options' => ActivityType::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Type'
			)
		) );
		$this->add( array(
			'type' => 'Submit',
			'name' => self::SUBMIT,
			'attributes' => array(
				'class' => 'button button-add',
				'title' => 'Create',
				'value' => 'Create'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::ACTIVITYTYPE => array(
				'required' => true
			)
		);
		return $spec;
	}
}
?>