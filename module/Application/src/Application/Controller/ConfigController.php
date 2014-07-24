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
 * @package     Application\Controller
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: ConfigController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Model\AccountSource;
use Application\Model\AccountType;
use Application\Model\ActivityStatus;
use Application\Model\ActivityType;
use Application\Model\InitialContact;
use Application\Model\Rating;
use Application\Model\Region;
use Application\Model\Salutation;
use Application\Model\Stage;
use Application\Model\Status;

use Application\Form\AccountSourceForm;
use Application\Form\ActivityStatusForm;
use Application\Form\AccountTypeForm;
use Application\Form\ActivityTypeForm;
use Application\Form\InitialContactForm;
use Application\Form\RatingForm;
use Application\Form\RegionForm;
use Application\Form\SalutationForm;
use Application\Form\StageForm;
use Application\Form\StatusForm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;

use Zend\Http\Request;
use Zend\View\Model\ViewModel;

/**
 * Configuration action controller
 *
 * @package		Application\Controller
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: ConfigController.php 13 2013-08-05 22:53:55Z  $
 */
class ConfigController extends AbstractApplicationController {

	public function indexAction() {
		/* @var $em EntityManager */
		$em = $this->getEntityManager();

		$slug = $this->params('slug');
		switch($slug) {
			case 'source':
				$pageTitle = 'Account Sources';
				$recordSet = $em->getRepository('\Application\Model\AccountSource')->findAll();
				break;
			case 'accountType':
				$pageTitle = 'Account Types';
				$recordSet = $em->getRepository('\Application\Model\AccountType')->findBy(array(), array('name' => 'asc'));
				break;
			case 'activityStatus':
				$pageTitle = 'Activity Status Codes';
				$recordSet = $em->getRepository('\Application\Model\ActivityStatus')->findAll();
				break;
			case 'activityType':
				$pageTitle = 'Activity Types';
				$recordSet = $em->getRepository('\Application\Model\ActivityType')->findBy(array(), array('name' => 'asc'));
				break;
			case 'initialContact':
				$pageTitle = 'Initial Contact Status';
				$recordSet = $em->getRepository('\Application\Model\InitialContact')->findAll();
				break;
			case 'rating':
				$pageTitle = 'Opportunity Rating Levels';
				$recordSet = $em->getRepository('\Application\Model\Rating')->findAll();
				break;
			case 'stage':
				$pageTitle = 'Opportunity Stages';
				$recordSet = $em->getRepository('\Application\Model\Stage')->findBy(array(), array('sequence' => 'asc'));
				break;
			case 'opportunityStatus':
				$pageTitle = 'Opportunity Status Codes';
				$recordSet = $em->getRepository('\Application\Model\Status')->findAll();
				break;
			case 'region':
				$pageTitle = 'Regions';
				$recordSet = $em->getRepository('\Application\Model\Region')->findBy(array(), array('name' => 'asc'));
				break;
			case 'salutation':
				$pageTitle = 'Salutations';
				$recordSet = $em->getRepository('\Application\Model\Salutation')->findAll();
				break;
			default:
				$pageTitle = 'Configurations';
		}

		$view = new ViewModel(array(
			'recordSet' => $recordSet,
			'slug' => $slug,
			'pageTitle' => $pageTitle,
		));
		if(!empty($slug)) {
			$view->setTemplate('application/config/' . $slug . '/index');
		} else {
			$view->setTemplate('application/config/index');
		}
		return $view;
	}

	public function editAction() {
		/* @var $em EntityManager */
		$em = $this->getEntityManager();

		$id = $this->params('id', 0);
		$slug = $this->params('slug');
		switch($slug) {
			case 'source':
				$title = 'Edit Account Source';
				$form = new AccountSourceForm();
				$obj = $em->getRepository('\Application\Model\AccountSource')->find($id);
				if($obj instanceof AccountSource) {
					$form->get(AccountSourceForm::ID)->setValue($obj->getId());
					$form->get(AccountSourceForm::NAME)->setValue($obj->getName());
				}
				break;
			case 'accountType':
				$title = 'Edit Account Type';
				$form = new AccountTypeForm();
				$obj = $em->getRepository('\Application\Model\AccountType')->find($id);
				if($obj instanceof AccountType) {
					$form->get(AccountTypeForm::ID)->setValue($obj->getId());
					$form->get(AccountTypeForm::NAME)->setValue($obj->getName());
				}
				break;
			case 'activityStatus':
				$title = 'Edit Activity Status';
				$form = new ActivityStatusForm();
				$obj = $em->getRepository('\Application\Model\ActivityStatus')->find($id);
				if($obj instanceof ActivityStatus) {
					$form->get(ActivityStatusForm::ID)->setValue($obj->getId());
					$form->get(ActivityStatusForm::NAME)->setValue($obj->getName());
					$form->get(ActivityStatusForm::NOTES)->setValue($obj->getNotes());
				}
				break;
			case 'activityType':
				$title = 'Edit Activity Type';
				$form = new ActivityTypeForm();
				$obj = $em->getRepository('\Application\Model\ActivityType')->find($id);
				if($obj instanceof ActivityType) {
					$form->get(ActivityTypeForm::ID)->setValue($obj->getId());
					$form->get(ActivityTypeForm::NAME)->setValue($obj->getName());
					$form->get(ActivityTypeForm::NOTES)->setValue($obj->getNotes());
				}
				break;
			case 'initialContact':
				$title = 'Edit Initial Contact';
				$form = new InitialContactForm();
				$obj = $em->getRepository('\Application\Model\InitialContact')->find($id);
				if($obj instanceof InitialContact) {
					$form->get(InitialContactForm::ID)->setValue($obj->getId());
					$form->get(InitialContactForm::NAME)->setValue($obj->getName());
					$form->get(InitialContactForm::NOTES)->setValue($obj->getNotes());
				}
				break;
			case 'rating':
				$title = 'Edit Opportunity Rating';
				$form = new RatingForm();
				$obj = $em->getRepository('\Application\Model\Rating')->find($id);
				if($obj instanceof Rating) {
					$form->get(RatingForm::ID)->setValue($obj->getId());
					$form->get(RatingForm::NAME)->setValue($obj->getName());
					$form->get(RatingForm::NOTES)->setValue($obj->getNotes());
				}
				break;
			case 'stage':
				$title = 'Edit Opportunity Stage';
				$form = new StageForm();
				$obj = $em->getRepository('\Application\Model\Stage')->find($id);
				if($obj instanceof Stage) {
					$form->get(StageForm::ID)->setValue($obj->getId());
					$form->get(StageForm::NAME)->setValue($obj->getName());
					$form->get(StageForm::SEQUENCE)->setValue($obj->getSequence());
					$form->get(StageForm::NOTES)->setValue($obj->getNotes());
				}
				break;
			case 'opportunityStatus':
				$title = 'Edit Opportunity Status';
				$form = new StatusForm();
				$obj = $em->getRepository('\Application\Model\Status')->find($id);
				if($obj instanceof Status) {
					$form->get(StatusForm::ID)->setValue($obj->getId());
					$form->get(StatusForm::NAME)->setValue($obj->getName());
					$form->get(StatusForm::NOTES)->setValue($obj->getNotes());
				}
				break;
			case 'region':
				$title = 'Edit Region';
				$form = new RegionForm();
				$obj = $em->getRepository('\Application\Model\Region')->find($id);
				if($obj instanceof Region) {
					$form->get(RegionForm::ID)->setValue($obj->getId());
					$form->get(RegionForm::NAME)->setValue($obj->getName());
					$form->get(RegionForm::ABBREVIATION)->setValue($obj->getAbbreviation());
				}
				break;
			case 'salutation':
				$title = 'Edit Salutation';
				$form = new SalutationForm();
				$obj = $em->getRepository('\Application\Model\Salutation')->find($id);
				if($obj instanceof Salutation) {
					$form->get(SalutationForm::ID)->setValue($obj->getId());
					$form->get(SalutationForm::NAME)->setValue($obj->getSalutation());
				}
				break;
			default:
		}

		$view = new ViewModel(array(
			'form' => $form,
			'pageTitle' => $title,
			'slug' => $slug,
		));
		if(!empty($slug)) {
			$view->setTemplate('application/config/update');
		} else {
			$view->setTemplate('application/config/index');
		}
		return $view;

	}

	public function createAction() {
		$slug = $this->params('slug');
		switch($slug) {
			case 'source':
				$title = 'New Account Source';
				$form = new AccountSourceForm();
				break;
			case 'accountType':
				$title = 'New Account Type';
				$form = new AccountTypeForm();
				break;
			case 'activityStatus':
				$title = 'New Activity Status';
				$form = new ActivityStatusForm();
				break;
			case 'activityType':
				$title = 'New Activity Type';
				$form = new ActivityTypeForm();
				break;
			case 'initialContact':
				$title = 'New Initial Contact';
				$form = new InitialContactForm();
				break;
			case 'rating':
				$title = 'New Opportunity Rating';
				$form = new RatingForm();
				break;
			case 'stage':
				$title = 'New Opportunity Stage';
				$form = new StageForm();
				break;
			case 'opportunityStatus':
				$title = 'New Opportunity Status';
				$form = new StatusForm();
				break;
			case 'region':
				$title = 'New Region';
				$form = new RegionForm();
				break;
			case 'salutation':
				$title = 'New Salutation';
				$form = new SalutationForm();
				break;
			default:
		}

		$view = new ViewModel(array(
			'form' => $form,
			'pageTitle' => $title,
			'slug' => $slug,
		));
		if(!empty($slug)) {
			$view->setTemplate('application/config/create');
		} else {
			$view->setTemplate('application/config/index');
		}
		return $view;
	}

	public function updateAction() {
		/* @var $request Request */
		/* @var $em EntityManager */
		$em = $this->getEntityManager();

		$slug = $this->params('slug');
		switch($slug) {
			case 'source':
				$title = 'Edit Account Source';
				$form = new AccountSourceForm();
				break;
			case 'accountType':
				$title = 'Edit Account Type';
				$form = new AccountTypeForm();
				break;
			case 'activityStatus':
				$title = 'Edit Activity Status';
				$form = new ActivityStatusForm();
				break;
			case 'activityType':
				$title = 'Edit Activity Type';
				$form = new ActivityTypeForm();
				break;
			case 'initialContact':
				$title = 'Edit Initial Contact';
				$form = new InitialContactForm();
				break;
			case 'rating':
				$title = 'Edit Opportunity Rating';
				$form = new RatingForm();
				break;
			case 'stage':
				$title = 'Edit Opportunity Stage';
				$form = new StageForm();
				break;
			case 'opportunityStatus':
				$title = 'Edit Opportunity Status';
				$form = new StatusForm();
				break;
			case 'region':
				$title = 'Edit Region';
				$form = new RegionForm();
				break;
			case 'salutation':
				$title = 'Edit Salutation';
				$form = new SalutationForm();
				break;
			default:
		}

		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				$now = new \DateTime();

				// Create new entity and set values
				switch($slug) {
					case 'accountType':
						if($form->get(AccountTypeForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\AccountType')->find($form->get(AccountTypeForm::ID)->getValue());
						} else {
							$obj = new AccountType();
						}
						$obj->setName($form->get(AccountTypeForm::NAME)->getValue());
						break;
					case 'activityStatus':
						if($form->get(ActivityStatusForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\ActivityStatus')->find($form->get(ActivityStatusForm::ID)->getValue());
						} else {
							$obj = new ActivityStatus();
						}
						$obj->setName($form->get(ActivityStatusForm::NAME)->getValue());
						$obj->setNotes($form->get(ActivityStatusForm::NOTES)->getValue());
						break;
					case 'activityType':
						if($form->get(ActivityTypeForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\ActivityType')->find($form->get(ActivityTypeForm::ID)->getValue());
						} else {
							$obj = new ActivityType();
						}
						$obj->setName($form->get(ActivityTypeForm::NAME)->getValue());
						$obj->setNotes($form->get(ActivityTypeForm::NOTES)->getValue());
						break;
					case 'initialContact':
						if($form->get(InitialContactForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\InitialContact')->find($form->get(InitialContactForm::ID)->getValue());
						} else {
							$obj = new InitialContact();
						}
						$obj->setName($form->get(InitialContactForm::NAME)->getValue());
						$obj->setNotes($form->get(InitialContactForm::NOTES)->getValue());
						break;
					case 'opportunityStatus':
						if($form->get(StatusForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\Status')->find($form->get(StatusForm::ID)->getValue());
						} else {
							$obj =  new Status();
						}
						$obj->setName($form->get(StatusForm::NAME)->getValue());
						$obj->setNotes($form->get(StatusForm::NOTES)->getValue());
						break;
					case 'rating':
						if($form->get(RatingForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\Rating')->find($form->get(RatingForm::ID)->getValue());
						} else {
							$obj = new Rating();
						}
						$obj->setName($form->get(RatingForm::NAME)->getValue());
						$obj->setNotes($form->get(RatingForm::NOTES)->getValue());
						break;
					case 'region':
						if($form->get(RegionForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\Region')->find($form->get(RegionForm::ID)->getValue());
						} else {
							$obj = new Region();
						}
						$obj->setName($form->get(RegionForm::NAME)->getValue());
						$obj->setAbbreviation($form->get(RegionForm::ABBREVIATION)->getValue());
						break;
					case 'salutation':
						if($form->get(SalutationForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\Salutation')->find($form->get(SalutationForm::ID)->getValue());
						} else {
							$obj = new Salutation();
						}
						$obj->setSalutation($form->get(SalutationForm::NAME)->getValue());
						break;
					case 'source':
						if($form->get(AccountSourceForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\AccountSource')->find($form->get(AccountSourceForm::ID)->getValue());
						} else {
							$obj = new AccountSource();
						}
						$obj->setName($form->get(AccountSourceForm::NAME)->getValue());
						break;
					case 'stage':
						if($form->get(StageForm::ID)->getValue() > 0) {
							$obj = $em->getRepository('\Application\Model\Stage')->find($form->get(StageForm::ID)->getValue());
						} else {
							$obj = new Stage();
						}
						$obj->setName($form->get(StageForm::NAME)->getValue());
						$obj->setSequence($form->get(StageForm::SEQUENCE)->getValue());
						$obj->setNotes($form->get(StageForm::NOTES)->getValue());
						break;
					default:
				}

				// Set the creation date
				if($obj->getId() == null || $obj->getId() == 0) {
					if(method_exists($obj, 'setCreationDate')) {
						$obj->setCreationDate($now);
					}
				}

				// Set the modification date
				if(method_exists($obj, 'setLastUpdateDate')) {
					$obj->setLastUpdateDate($now);
				}

				// Persist the entity
				$em->persist($obj);
				$em->flush();

				return $this->redirect()->toRoute('config', array('slug' => $slug, 'action' => 'index', 'id' => $obj->getId()));
			}
		}

		$view = new ViewModel(array(
			'form' => $form,
			'pageTitle' => $title,
			'slug' => $slug,
		));
		if(!empty($slug)) {
			$view->setTemplate('application/config/update');
		} else {
			$view->setTemplate('application/config/index');
		}
		return $view;
	}

	public function deleteAction() {
		/* @var $em EntityManager */
		$em = $this->getEntityManager();

		// Fetch the entity
		$id = $this->params('id', 0);
		if($id > 0) {
			$slug = $this->params('slug');
			switch($slug) {
				case 'source':
					$obj = $em->getRepository('\Application\Model\AccountSource')->find($id);
					break;
				case 'accountType':
					$obj = $em->getRepository('\Application\Model\AccountType')->find($id);
					break;
				case 'activityStatus':
					$obj = $em->getRepository('\Application\Model\ActivityStatus')->find($id);
					break;
				case 'activityType':
					$obj = $em->getRepository('\Application\Model\ActivityType')->find($id);
					break;
				case 'initialContact':
					$obj = $em->getRepository('\Application\Model\InitialContact')->find($id);
					break;
				case 'rating':
					$obj = $em->getRepository('\Application\Model\Rating')->find($id);
					break;
				case 'stage':
					$obj = $em->getRepository('\Application\Model\Stage')->find($id);
					break;
				case 'opportunityStatus':
					$obj = $em->getRepository('\Application\Model\Status')->find($id);
					break;
				case 'region':
					$obj = $em->getRepository('\Application\Model\Region')->find($id);
					break;
				case 'salutation':
					$obj = $em->getRepository('\Application\Model\Salutation')->find($id);
					break;
			}
		} else {
			$obj = null;
		}

		// Remove the entity
		if($obj != null) {
			$em->remove($obj);
			$em->flush();
		}

		return $this->redirect()->toRoute('config', array('slug' => $slug, 'action' => 'index'));
	}
}
?>