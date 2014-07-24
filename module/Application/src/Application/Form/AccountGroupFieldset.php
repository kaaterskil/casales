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
 * @version     SVN $Id: AccountGroupFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\AccountGroup;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * Account Group fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AccountGroupFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class AccountGroupFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'accountGroup';
	const ID = 'id';
	const DESCRIPTION = 'description';
	const PARENTGROUP = 'parent';

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
		$this->em = $em;
		parent::__construct( self::FIELDSETNAME );
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\AccountGroup' ) )->setObject( new AccountGroup() );
		$this->init();
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * Initializes form elements
	 *
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::ID
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::DESCRIPTION,
			'options' => array(
				'label' => 'Name',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 35,
				'title' => 'Name'
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::PARENTGROUP,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Parent Group',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\AccountGroup',
				'property' => 'description',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array(
							'description' => 'asc'
						)
					)
				)
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
		$spec = array(
			self::DESCRIPTION => array(
				'required' => true,
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
							'min' => 5,
							'max' => 64
						)
					)
				)
			),
			self::PARENTGROUP => array(
				'required' => false
			)
		);
		
		return $spec;
	}
}
?>