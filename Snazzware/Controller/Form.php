<?php
/**
 * Snazzware Extensions for the Zend Framework 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.snazzware.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to josh@snazzware.com so we can send you a copy immediately.
 *
 * @category   Snazzware
 * @copyright  Copyright (c) 2011-2012 Josh M. McKee
 * @license    http://www.snazzware.com/license/new-bsd     New BSD License
 */

class Snazzware_Controller_Form extends Snazzware_Controller_Action {
		
	private $formClassName = '';	
	private $modelClassName = 'Snazzware_Model_Entity';		
	private $form = null;
	private $model = null;
	private $createEnabled = true;	
	private $formActionName = 'form';
	private $successMethodName = 'success';
	
	public function init() {
		parent::init();
	}
		
	public function indexAction()
    {   
    	parent::baseAction();    	
    	$this->_redirect($this->getRequest()->getControllerName().'/form');
    }
	
	public function formAction() {
    	parent::baseAction();        
    	if (!$this->canCreate()) $this->unauthorized();
    	
    	$form = $this->getForm();
    	
    	$form->build();
    	
    	$form->secure($this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName().'.'.$this->getRequest()->getActionName());
    	
    	if (!$this->handleWidgetCallback()) {
	    	if ($this->getRequest()->isPost()) {
	    		if ($form->isValid($this->_getAllParams())) {
	    			$success = $this->getSuccessMethodName();    			  
					$this->$success($form);	
	    		} else {
	    			
	    		}
	    	}
    	
	    	$this->view->form = $form;    	
	
	    	$this->buildFormNavigation();
	    	
	    	$this->view->bottomNavigation = $this->getNavigation('bottom');
	    	$this->view->leftNavigation = $this->getNavigation('left');
    	}
    }
    
    protected function postHandleWidgetCallback($handled) {
    	if (!$handled) {
    		$form = $this->getForm();
    		if ($form != null) {
    			$handled = $form->handleWidgetCallback($this->getRequest(),$this->view);
    		}
    	}
    	 
    	parent::postHandleWidgetCallback($handled);
    }
    
    protected function success($form) {
    	//
    }
    
	public function buildFormNavigation() {
    	$nav = $this->getNavigation($this->getDefaultNavigationArea());
    	
    	if ($this->getCreateEnabled() && $this->canCreate($this->getRequest()->getActionName())) {
    		$nav->addItem($nav->createItem('Form_Submit','submit',array(
    			'stereotype'=>NavigationFactory::stereotype_save,    			
    			'module'=>$this->getRequest()->getModuleName(),
    			'controller'=>$this->getRequest()->getControllerName(),
    			'action'=>$this->getFormActionName(),
    			'form'=>$this->getForm()
    		)));    		
    	}
    	
    	return $nav;
    }
	
	protected function getForm() {
		if ($this->form == null) {
			$classname = $this->getFormClassName();
			if ($classname != '') {
				$this->form = new $classname;	
				$this->form->setId('crudForm');
			}
		}
		return $this->form;
	}
	
	protected function setForm($value) {
		$this->form = $value;
	}
	
	protected function getModel() {
		if ($this->model == null) {
			$classname = $this->getModelClassName();
			$this->model = new $classname($this->getPrimaryEntityClassName());	
		}
		return $this->model;
	}
	
	protected function setModel($value) {
		$this->model = $value;
	}
	
	protected function getFormClassName() {
		return $this->formClassName;
	}
		
	protected function setFormClassName($classname) {
		$this->formClassName = $classname;
	}
		
	protected function getModelClassName() {
		return $this->modelClassName;
	}
		
	protected function setModelClassName($classname) {
		$this->modelClassName = $classname;
	}
	
	protected function getFormActionName() { return $this->formActionName; }
	protected function setFormActionName($value) { $this->formActionName = $value; }
	
	protected function getSuccessMethodName() { return $this->successMethodName; }	
	protected function setSuccessMethodName($value) { $this->successMethodName = $value; }
	
	protected function getCreateEnabled() {
		return $this->createEnabled;
	}
	
	protected function setCreateEnabled($value) {
		$this->createEnabled = $value;
	}
	
}

