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
 * @version     SVN $Id: BaseActivityFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\Account;
use Application\Model\Contact;
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
 * The basic activity fieldset for forms. To be extended in concrete classes.
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: BaseActivityFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class BaseActivityFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'activity';
	const ID = 'id';
	const DISCRIMINATOR = 'discriminator';
	const ACCOUNT = 'account';
	const BUSINESSUNIT = 'businessUnit';
	consT CAMPAIGN = 'campaign';
	const CONTACT = 'contact';
	const DESCRIPTION = 'description';
	const LEAD = 'lead';
	const LONGNOTES = 'longNotes';
	const NOTES = 'notes';
	const OPPORTUNITY = 'opportunity';
	const OWNER = 'owner';
	const SUBJECT = 'subject';

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var \ReflectionClass
	 */
	private $rc;
	
	/* ---------- Constructor ---------- */

	/**
	 * @param EntityManager $em
	 *        	The ObjectManager to use
	 * @param string $clazz
	 *        	The FQCN of the hydrated/extracted object */
	public function __construct(EntityManager $em, $clazz) {
		parent::__construct();
		$this->em = $em;
		
		$this->rc = new \ReflectionClass( $clazz );
		$this->setHydrator( new DoctrineHydrator( $this->em, $clazz ) )->setObject( $this->rc->newInstance() );
		
		$this->init();
	}
	
	/* ---------- Getter/Setters ---------- */

	/**
	 * @return ReflectionClass
	 */
	public function getClazz() {
		return $this->rc;
	}

	/**
	 * @return \Doctrine\ORM\EntityManager
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
	 * @param \DateTime|string $datetime
	 * @param string $format
	 * @return \DateTime
	 */
	public function formatDateTime($datetime, $format) {
		if (is_string( $datetime ) && $datetime != '') {
			$object = new \DateTime( $datetime );
			return $object->format( $format );
		} elseif ($datetime instanceof \DateTime) {
			return $datetime->format( $format );
		}
		return $datetime;
	}

	/**
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::ID
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::DISCRIMINATOR,
			'attributes' => array(
				'value' => substr( $this->rc->getName(), 18 )
			)
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::ACCOUNT
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::BUSINESSUNIT
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::CAMPAIGN
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::CONTACT
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::LEAD
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::OPPORTUNITY
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
				'class' => 'input-text',
				'cols' => 145,
				'rows' => 10,
				'title' => 'Description'
			)
		) );
		$this->add( array(
			'type' => 'Textarea',
			'name' => self::LONGNOTES,
			'options' => array(
				'label' => 'Long Notes',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 5,
				'title' => 'Long Notes'
			)
		) );
		$this->add( array(
			'type' => 'Textarea',
			'name' => self::NOTES,
			'options' => array(
				'label' => 'Notes',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 3,
				'title' => 'Notes'
			)
		) );
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::OWNER,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Ownership',
				'label_attributes' => array(
					'class' => 'input-label required'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\User',
				'property' => 'fullName',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array(
							'lastName' => 'asc'
						)
					)
				)
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::SUBJECT,
			'options' => array(
				'label' => 'Subject',
				'label_attributes' => array(
					'class' => 'input-label required'
				)
			),
			'attributes' => array(
				'class' => 'input-text bold',
				'size' => 127,
				'title' => 'Subject'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::ID => array(
				'required' => false
			),
			self::DISCRIMINATOR => array(
				'required' => false
			),
			self::ACCOUNT => array(
				'required' => false
			),
			self::BUSINESSUNIT => array(
				'required' => false
			),
			self::CAMPAIGN => array(
				'required' => false
			),
			self::CONTACT => array(
				'required' => false
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
			),
			self::LEAD => array(
				'required' => false
			),
			self::LONGNOTES => array(
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
			),
			self::NOTES => array(
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
			),
			self::OPPORTUNITY => array(
				'required' => false
			),
			self::OWNER => array(
				'required' => true
			),
			self::SUBJECT => array(
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
							'max' => 200
						)
					)
				)
			),
		);
		
		return $spec;
	}
}
?>