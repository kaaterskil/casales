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
 * @version     SVN $Id: AccountForm.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\AccountFieldset;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Account input form
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AccountForm.php 13 2013-08-05 22:53:55Z  $
 */
class AccountForm extends Form {
	const SUBMIT = 'submit_btn';
	const SUBMITCLOSE = 'saveAndClose';

	/**
	 * @var EntityManager
	 */
	private $em;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		parent::__construct();
		$this->em = $em;
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\Account' ) );
		$this->setInputFilter( new InputFilter() );
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
	 * Initializes the form
	 *
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$fieldset = new AccountFieldset( $this->em );
		$fieldset->setOptions( array(
			'use_as_base_fieldset' => true
		) );
		$this->add( $fieldset );
		
		$this->add( array(
			'type' => 'Submit',
			'name' => self::SUBMIT,
			'attributes' => array(
				'class' => 'button lfloat mrl mbs',
				'value' => 'Save',
				'title' => 'Save'
			)
		) );
		$this->add( array(
			'type' => 'Submit',
			'name' => self::SUBMITCLOSE,
			'attributes' => array(
				'class' => 'button lfloat mrl mbs',
				'value' => 'Save and Close',
				'title' => 'Save and Close'
			)
		) );
	}
}
?>