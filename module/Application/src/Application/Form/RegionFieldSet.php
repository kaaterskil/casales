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
 * @version     SVN $Id: RegionFieldSet.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\Region;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Region fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: RegionFieldSet.php 13 2013-08-05 22:53:55Z  $
 */
class RegionFieldset extends Fieldset implements InputFilterProviderInterface {
	const ID = 'id';
	const NAME = 'name';
	const ABBREV = 'abbreviation';
	const CREATIONDATE = 'creationDate';
	const LASTUPDATEDATE = 'lastUpdateDate';

	/**
	 * @var EntityManager
	 */
	private $em;
	
	/* ---------- Constructor ---------- */

	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param EntityManager $em
	 */
	public function __construct($name, EntityManager $em) {
		$this->em = $em;
		parent::__construct($name);
		$this->setHydrator(new DoctrineHydrator($this->em, 'Application\Model\Region'))
			 ->setObject(new Region());
		$this->init();
	}
	
	/* ---------- Getter/Setters ---------- */
	
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
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$this->add(array(
			'type' => 'Hidden',
			'name' => self::ID,
		));
		$this->add(array(
			'type' => 'Hidden',
			'name' => self::CREATIONDATE,
		));
		$this->add(array(
			'type' => 'Hidden',
			'name' => self::LASTUPDATEDATE,
		));
		$this->add(array(
			'type' => 'Text',
			'name' => self::NAME,
			'options' => array(
				'label' => 'Name',
				'label_attributes' => array(
					'class' => 'input-label',
				),
			),
			'attributes' => array(
				'class' => 'input-text',
				'required' => 'required',
				'size' => 30,
				'title' => 'Name',
			),
		));
		$this->add(array(
			'type' => 'Text',
			'name' => self::ABBREV,
			'options' => array(
				'label' => 'Abbreviation',
				'label_attributes' => array(
					'class' => 'input-label',
				),
			),
			'attributes' => array(
				'class' => 'input-text',
				'required' => 'required',
				'size' => 5,
				'title' => 'Name',
			),
		));
	}

	/**
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::ID => array(
				'required' => false,
			),
			self::CREATIONDATE => array(
				'required' => false,
			),
			self::LASTUPDATEDATE => array(
				'required' => false,
			),
			self::NAME => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 64,
						),
					),
				),
			),
			self::ABBREV => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'max' => 2,
						),
					),
				),
			),
		);

		return $spec;
	}
}
?>