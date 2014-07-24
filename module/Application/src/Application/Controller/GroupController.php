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
 * @version     SVN $Id: GroupController.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Controller;

use Application\Controller\AbstractApplicationController;
use Application\Model\AccountGroup;
use Application\Form\AccountGroupForm;

use Doctrine\ORM\EntityManager;

use Zend\Http\Request;
use Zend\View\Model\ViewModel;

/**
 * Account Group action controller
 *
 * @package Application\Controller
 * @author Blair <blair@kaaterskil.com>
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN: $Rev: 13 $
 */
class GroupController extends AbstractApplicationController {
	
	public function indexAction() {
		/* @var $em EntityManager */
		$em = $this->getEntityManager();
		
		$recordSet = $em->getRepository('Application\Model\AccountGroup')
						->findBy(array(), array('description' => 'asc'));

		$view = new ViewModel(array(
				"pageTitle" => 'AccountGroups',
				"recordSet" => $recordSet,
		));
		$view->setTemplate('application/group/index');
		return $view;
	}
	
	public function createAction() {
		/* @var $em EntityManager */
		/* @var $request Request */
		/* @var $object AccountGroup */
		
		$em = $this->getEntityManager();
		
		// Create form
		$form = new AccountGroupForm($em);
		
		// Create empty entity and bind it to the form
		$object = new AccountGroup();
		$form->bind($object);
		
		// Process a form request
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				if($object->getParent()) {
					$em->persist($object->getParent());
				}
				$now = new \DateTime();
				$object->setCreationDate($now);
				$object->setLastUpdateDate($now);
				
				$em->persist($object);
				$em->flush();

				return $this->redirect()->toRoute('group', array('action' => 'index'));
			}
		}
		
		$view = new ViewModel(array(
			'form' => $form,
			'pageTitle' => 'New Account Group',
		));
		$view->setTemplate('application/group/create');
		return $view;
	}
	
	public function editAction() {
		/* @var $em EntityManager */
		/* @var $request Request */
		/* @var $object AccountGroup */
		
		$em = $this->getEntityManager();
		
		// Create form
		$form = new AccountGroupForm($em);
		
		// Fetch specified entity and bind it to the form
		$id = $this->params('id', 0);
		if(!$id) {
			return $this->redirect()->toRoute('group', array('action' => 'create'));
		}
		$object = $em->getRepository('Application\Model\AccountGroup')->find($id);
		$form->bind($object);
		$form->get(AccountGroupForm::SUBMIT)->setAttribute('value', 'Update');
		
		$parentId = $object->getParent() ? $object->getParent()->getId() : '';
		
		// Process a form request
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				if($object->getParent()) {
					$em->persist($object->getParent());
				}
				$now = new \DateTime();
				$object->setLastUpdateDate($now);
				
				$em->persist($object);
				$em->flush();

				return $this->redirect()->toRoute('group', array('action' => 'index'));
			}
		}
		
		$view = new ViewModel(array(
			'id' => $id,
			'form' => $form,
			'parentId' => $parentId,
			'pageTitle' => 'Edit Account Group',
		));
		$view->setTemplate('application/group/edit');
		return $view;
	}
	
	public function deleteAction() {
		/* @var $em EntityManager */
		/* @var $object AccountGroup */
		
		$em = $this->getEntityManager();
		
		$id = $this->params('id', 0);
		if(!$id) {
			return $this->redirect()->toRoute('group', array('action' => 'index'));
		}
		$object = $em->getRepository('Application\Model\AccountGroup')->find($id);
		$em->remove($object);
		$em->flush();

		return $this->redirect()->toRoute('group', array('action' => 'index'));
	}
}
?>