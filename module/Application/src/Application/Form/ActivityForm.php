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
 * @version     SVN $Id: ActivityForm.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\BaseActivityFieldset;
use Application\Form\AppointmentFieldset;
use Application\Form\CampaignActivityFieldset;
use Application\Form\CampaignResponseFieldset;
use Application\Form\EmailFieldset;
use Application\Form\FaxFieldset;
use Application\Form\InteractionFieldset;
use Application\Form\LetterFieldset;
use Application\Form\NoteFieldset;
use Application\Form\TaskFieldset;
use Application\Form\TelephoneInteractionFieldset;
use Application\Form\VisitFieldset;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Activity input form
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: ActivityForm.php 13 2013-08-05 22:53:55Z  $
 */
class ActivityForm extends Form {
	const SUBMIT = 'submit_btn';
	const SUBMITCLOSE = 'saveAndCloseActivity';

	/**
	 * @var string
	 */
	private $clazz;

	/**
	 * @var EntityManager
	 */
	private $em;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 * @param string $clazz
	 */
	public function __construct(EntityManager $em, $clazz) {
		parent::__construct();
		$this->em = $em;
		$this->clazz = $clazz;
		
		$this->setHydrator( new DoctrineHydrator( $this->em, $clazz ) );
		$this->setInputFilter( new InputFilter() );
		$this->init();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return string
	 */
	public function getClazz() {
		return $this->clazz;
	}

	/**
	 * @param string $clazz
	 */
	public function setClazz($clazz) {
		$this->clazz = (string) $clazz;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$fieldset = $this->getFieldset();
		$fieldset->setName( BaseActivityFieldset::FIELDSETNAME );
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

	/**
	 * Returns the fieldset for the specified activity class
	 * @return BaseActivityFieldset
	 */
	private function getFieldset() {
		$type = substr( $this->clazz, 18 );
		
		if (preg_match( '/Appointment/', $type )) {
			return new AppointmentFieldset( $this->em, $this->clazz );
		}
		if (preg_match( '/CampaignActivity/', $type )) {
			return new CampaignActivityFieldset( $this->em, $this->clazz );
		}
		if (preg_match( '/CampaignResponse/', $type )) {
			return new CampaignResponseFieldset( $this->em, $this->clazz );
		}
		if (preg_match( '/EmailInteraction/', $type )) {
			return new EmailFieldset( $this->em );
		}
		if (preg_match( '/FaxInteraction/', $type )) {
			return new FaxFieldset( $this->em );
		}
		if (preg_match( '/LetterInteraction/', $type )) {
			return new LetterFieldset( $this->em );
		}
		if (preg_match( '/Note/', $type )) {
			return new NoteFieldset( $this->em, $this->clazz );
		}
		if (preg_match( '/Task/', $type )) {
			return new TaskFieldset( $this->em, $this->clazz );
		}
		if (preg_match( '/TelephoneInteraction/', $type )) {
			return new TelephoneInteractionFieldset( $this->em );
		}
		if (preg_match( '/VisitInteraction/', $type )) {
			return new VisitFieldset( $this->em );
		}
		if (preg_match( '/Interaction/', $type )) {
			return new InteractionFieldset( $this->em, $this->clazz );
		}
		throw new \InvalidArgumentException( "Invalid ActivityType: " . $type );
	}
}
?>