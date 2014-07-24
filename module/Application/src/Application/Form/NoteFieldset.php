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
 * @version     SVN $Id: NoteFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\BaseActivityFieldset;
use Application\Model\Account;
use Application\Model\Address;
use Application\Model\Contact;
use Application\Model\ActivityPriority;
use Application\Model\ActivityState;
use Application\Model\ActivityStatus;
use Application\Model\Direction;
use Application\Model\Telephone;
use Application\Model\Opportunity;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * Note fieldset for forms
 *
 * Provides additional form fields over the basic activity fieldset
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: NoteFieldset.php 13 2013-08-05 22:53:55Z  $
 * @see			BaseActivityFieldset
 */
class NoteFieldset extends BaseActivityFieldset {
	const PRIORITY = 'priority';
	
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
     * @return void
     * @see \Application\Form\BaseActivityFieldset::init()
     */
	public function init() {
		parent::init();
		
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
	}

	/**
     * @return array
     * @see \Application\Form\BaseActivityFieldset::getInputFilterSpecification()
     */
	public function getInputFilterSpecification() {
		$array1 = parent::getInputFilterSpecification();
		
		$array2 = array(
			self::PRIORITY => array(
				'required' => false
			)
		);
		
		return array_merge( $array1, $array2 );
	}
}
?>