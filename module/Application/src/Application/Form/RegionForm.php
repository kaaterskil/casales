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
 * @version     SVN $Id: RegionForm.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Zend\Form\Annotation\InputFilter;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\InputFilter\InputFilter as Filter;

/**
 * Region input form
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: RegionForm.php 13 2013-08-05 22:53:55Z  $
 */
class RegionForm extends Form {
	const ID = 'id';
	const NAME = 'name';
	const ABBREVIATION = 'abbreviation';
	const SUBMIT = 'submit_btn';
	
	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null) {
		parent::__construct();
		$this->init();
		// $this->setInputFilter($this->filterInit());
	}
	
	public function init() {
		$id = new Element\Hidden();
		$id->setName(self::ID);
		$this->add($id);
		
		$name = new Element\Text();
		$name->setAttributes(array(
			'class' => 'input-text',
			'maxlength' => 64,
			'name' => self::NAME,
			'placeholder' => 'Enter a name',
			'title' => 'Name',
		));
		$name->setOptions(array(
			'label' => 'Name:',
			'label_attributes' => array(
				'class' => 'input-label',
			),
		));
		$this->add($name);
		
		$abbreviation = new Element\Text();
		$abbreviation->setAttributes(array(
			'class' => 'input-text',
			'maxlength' => 12,
			'name' => self::ABBREVIATION,
			'placeholder' => 'Enter an ANSI code',
			'title' => 'Abbreviation',
		));
		$abbreviation->setOptions(array(
			'label' => 'Abbreviation:',
			'label_attributes' => array(
				'class' => 'input-label',
			),
		));
		$this->add($abbreviation);
		
		$submit = new Element\Submit();
		$submit->setAttributes(array(
			'class' => 'button button-confirm',
			'name' => self::SUBMIT,
			'value' => 'Create',
			'title' => 'Create',
		));
		$this->add($submit);
	}
	
	private function filterInit() {
		$filter = new Filter();
		$factory = new InputFilterFactory();
		
		$spec = $factory->createInput(array(
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'required' => true,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 64,
				)),
			),
		));
		$filter->add($spec, self::NAME);
		
		$spec = $factory->createInput(array(
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'required' => true,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 12,
				)),
			),
		));
		$filter->add($spec, self::ABBREVIATION);
		
		return $filter;
	}
}
?>