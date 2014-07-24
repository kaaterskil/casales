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
 * @version     SVN $Id: CloseActivityForm.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Model\AppointmentStatus;
use Application\Model\CampaignActivityStatus;
use Application\Model\CampaignResponseStatus;
use Application\Model\EmailStatus;
use Application\Model\FaxStatus;
use Application\Model\LetterStatus;
use Application\Model\TaskStatus;
use Application\Model\TelephoneStatus;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Close Activity input form
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: CloseActivityForm.php 13 2013-08-05 22:53:55Z  $
 */
class CloseActivityForm extends Form implements InputFilterProviderInterface {
	const ACTUALEND = 'actualEnd';
	const STATUS = 'status';
	const SUBMIT = 'submitButton';

	/**
	 * @var string
	 */
	private $activityClazz;
	
	/* ---------- Constructor ---------- */
	
	/**
	 * Constructor
	 * @param string $activityClazz
	 */
	public function __construct($activityClazz) {
		parent::__construct( 'close_activity_form' );
		$this->activityClazz = $activityClazz;
		$this->init();
	}
	
	/* ---------- Getter/Setters ---------- */
	
	/**
	 * @return string
	 */
	public function getActivityClazz() {
		return $this->activityClazz;
	}

	/**
	 * @param string $activityClazz
	 */
	public function setActivityClazz($activityClazz) {
		$this->activityClazz = (string) $activityClazz;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return void
	 * @see \Zend\Form\Element::init()
	 */
	public function init() {
		$now = new \DateTime();
		
		$this->add( array(
			'type' => 'DateTime',
			'name' => self::ACTUALEND,
			'options' => array(
				'label' => 'Close Date',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Close Date',
				'value' => $now->format( 'm/d/Y' )
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::STATUS,
			'options' => array(
				'label' => 'Status',
				'label_attributes' => array(
					'class' => 'input-label required'
				),
				'empty_option' => 'Select...',
				'value_options' => $this->getValueOptions( $this->activityClazz )
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status',
				'value' => $this->initializeValue( $this->activityClazz )
			)
		) );
		$this->add( array(
			'type' => 'Submit',
			'name' => self::SUBMIT,
			'attributes' => array(
				'class' => 'button lfloat mrm mbm',
				'value' => 'OK',
				'title' => 'OK'
			)
		) );
	}

	/**
	 * @return array
	 * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
	 */
	public function getInputFilterSpecification() {
		$spec = array(
			self::ACTUALEND => array(
				'required' => false
			),
			self::STATUS => array(
				'required' => true
			)
		);
		return $spec;
	}

	/**
	 * Returns the value options for the specified entity status
	 *
	 * @param string $activityClazz
	 * @return array
	 */
	private function getValueOptions($activityClazz) {
		$result = array();
		switch ($activityClazz) {
			case 'CampaignActivity' :
				$result = CampaignActivityStatus::toArray();
				break;
			case 'CampaignResponse' :
				$result = CampaignResponseStatus::toArray();
				break;
			case 'EmailInteraction' :
				$result = EmailStatus::toArray();
				break;
			case 'FaxInteraction' :
				$result = FaxStatus::toArray();
				break;
			case 'LetterInteration' :
				$result = LetterStatus::toArray();
				break;
			case 'TelephoneInteraction' :
				$result = TelephoneStatus::toArray();
				break;
			case 'CallbackTask' :
			case 'FollowUpTask' :
			case 'OtherTask' :
				$result = TaskStatus::toArray();
				break;
			case 'BreakfastAppointment' :
			case 'LunchAppointment' :
			case 'DinnerAppointment' :
			case 'MeetingAppointment' :
				$result = AppointmentStatus::toArray();
				break;
		}
		return $result;
	}

	/**
	 * @param string $activityClazz
	 * @return string
	 */
	private function initializeValue($activityClazz) {
		$result = '';
		switch ($activityClazz) {
			case 'CampaignActivity' :
				$result = CampaignActivityStatus::CLOSED;
				break;
			case 'CampaignResponse' :
				$result = CampaignResponseStatus::CLOSED;
				break;
			case 'EmailInteraction' :
				$result = EmailStatus::SENT;
				break;
			case 'FaxInteraction' :
				$result = FaxStatus::COMPLETED;
				break;
			case 'LetterInteration' :
				$result = LetterStatus::SENT;
				break;
			case 'TelephoneInteraction' :
				$result = TelephoneStatus::MADE;
				break;
			case 'CallbackTask' :
			case 'FollowUpTask' :
			case 'OtherTask' :
				$result = TaskStatus::COMPLETED;
				break;
			case 'BreakfastAppointment' :
			case 'LunchAppointment' :
			case 'DinnerAppointment' :
			case 'MeetingAppointment' :
				$result = AppointmentStatus::COMPLETED;
				break;
		}
		return $result;
	}
}
?>