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
 * @version     SVN $Id: SalesLiteratureItemFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\SalesLiteratureItem;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * Sales Literature Item fieldset for forms
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: SalesLiteratureItemFieldset.php 13 2013-08-05 22:53:55Z  $
 */
class SalesLiteratureItemFieldset extends Fieldset implements InputFilterProviderInterface {
	const FIELDSETNAME = 'salesLiteratureItem';
	const ID = 'id';
	const SUMMARY = 'abstract';
	const AUTHOR = 'author';
	const DOCUMENTURL = 'documentUrl';
	const FILENAME = 'filename';
	const FILESIZE = 'filesize';
	const FILETYPE = 'filetype';
	const FILEUPLOAD = 'fileUpload';
	const MIMETYPE = 'mimetype';
	const KEYWORDS = 'keywords';
	const TITLE = 'title';

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
		parent::__construct( self::FIELDSETNAME );
		$this->em = $em;
		
		$this->setHydrator( new DoctrineHydrator( $this->em, 'Application\Model\SalesLiteratureItem' ) )->setObject( new SalesLiteratureItem() );
		
		$this->init();
	}
	
	/* ---------- Methods ---------- */
	
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
			'type' => 'Textarea',
			'name' => self::SUMMARY,
			'options' => array(
				'label' => 'Abstract',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 13,
				'title' => 'Abstract'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::AUTHOR,
			'options' => array(
				'label' => 'Author',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 125,
				'title' => 'Author'
			)
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::DOCUMENTURL
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::FILENAME
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::FILESIZE
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::FILETYPE
		) );
		$this->add( array(
			'type' => 'File',
			'name' => self::FILEUPLOAD,
			'options' => array(
				'label' => 'File name',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'title' => 'File name'
			)
		) );
		$this->add( array(
			'type' => 'Textarea',
			'name' => self::KEYWORDS,
			'options' => array(
				'label' => 'Keywords',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-textarea',
				'cols' => 150,
				'rows' => 3,
				'title' => 'Keywords'
			)
		) );
		$this->add( array(
			'type' => 'Hidden',
			'name' => self::MIMETYPE
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::TITLE,
			'options' => array(
				'label' => 'Title',
				'label_attributes' => array(
					'class' => 'input-label bold required'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 125,
				'title' => 'Title'
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
			self::SUMMARY => array(
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
			self::AUTHOR => array(
				'required' => false,
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
							'max' => 255
						)
					)
				)
			),
			self::DOCUMENTURL => array(
				'required' => false
			),
			self::FILENAME => array(
				'required' => false
			),
			self::FILESIZE => array(
				'required' => false
			),
			self::FILETYPE => array(
				'required' => false
			),
			self::FILEUPLOAD => array(
				'required' => false
			),
			self::KEYWORDS => array(
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
			self::MIMETYPE => array(
				'required' => false
			),
			self::TITLE => array(
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
			)
		);
		
		return $spec;
	}
}
?>