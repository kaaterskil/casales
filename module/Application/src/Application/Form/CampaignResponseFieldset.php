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
 * @version     SVN $Id: CampaignResponseFieldset.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Form;

use Application\Form\BaseActivityFieldset;
use Application\Model\ActivityPriority;
use Application\Model\CampaignActivity;
use Application\Model\CampaignActivityType;
use Application\Model\CampaignResponseStatus;
use Application\Model\ChannelType;
use Application\Model\ResponseCode;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\ORM\EntityManager;
use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * Campaign Response fieldset for forms
 *
 * Provides additional form fields over the basic activity fieldset
 *
 * @package		Application\Form
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: CampaignResponseFieldset.php 13 2013-08-05 22:53:55Z  $
 * @see			BaseActivityFieldset
 */
class CampaignResponseFieldset extends BaseActivityFieldset {
	const CAMPAIGNACTIVITY = 'campaignActivity';
	const CHANNEL = 'channelType';
	const FROM = 'from';
	const PRIORITY = 'priority';
	const RECEIVEDON = 'receivedOn';
	const RESPONSECODE = 'responseCode';
	const SCHEDULEDEND = 'scheduledEnd';
	const STATUS = 'status';
	
	/* ---------- Constructor ---------- */
	
	/**
     * Constructor
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
		
		$this->remove( self::STATUS );
		
		$this->add( array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => self::CAMPAIGNACTIVITY,
			'options' => array(
				'empty_option' => 'Select...',
				'label' => 'Original Activity',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'object_manager' => $this->em,
				'target_class' => 'Application\Model\CampaignActivity',
				'property' => 'subject',
				'find_method' => array(
					'name' => 'findBy',
					'params' => array(
						'criteria' => array(),
						'orderBy' => array(
							'subject' => 'asc'
						)
					)
				)
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::CHANNEL,
			'options' => array(
				'label' => 'Channel',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => ChannelType::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Channel'
			)
		) );
		$this->add( array(
			'type' => 'Text',
			'name' => self::FROM,
			'options' => array(
				'label' => 'From',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 153,
				'title' => 'From'
			)
		) );
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
		$this->add( array(
			'type' => 'Date',
			'name' => self::RECEIVEDON,
			'options' => array(
				'label' => 'Received On',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Received On'
			)
		) );
		$this->add( array(
			'type' => 'Select',
			'name' => self::RESPONSECODE,
			'options' => array(
				'label' => 'Response Code',
				'label_attributes' => array(
					'class' => 'input-label'
				),
				'empty_option' => 'Select...',
				'value_options' => ResponseCode::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Response Code'
			)
		) );
		$this->add( array(
			'type' => 'Date',
			'name' => self::SCHEDULEDEND,
			'options' => array(
				'label' => 'Close By',
				'label_attributes' => array(
					'class' => 'input-label'
				)
			),
			'attributes' => array(
				'class' => 'input-text',
				'size' => 20,
				'step' => 1,
				'title' => 'Close By'
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
				'value_options' => CampaignResponseStatus::toArray()
			),
			'attributes' => array(
				'class' => 'input-select',
				'title' => 'Status'
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
			self::CAMPAIGNACTIVITY => array(
				'required' => false
			),
			self::CHANNEL => array(
				'required' => false
			),
			self::FROM => array(
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
			self::PRIORITY => array(
				'required' => false
			),
			self::RECEIVEDON => array(
				'required' => false
			),
			self::RESPONSECODE => array(
				'required' => false
			),
			self::SCHEDULEDEND => array(
				'required' => false
			),
			self::STATUS => array(
				'required' => true
			)
		);
		
		return array_merge( $array1, $array2 );
	}
}
?>