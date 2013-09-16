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

class Snazzware_Controller_Form_Stateful extends Snazzware_Controller_Form {
		
	private $statefulNamespace = '';
	private $statefulPrefix = '';
	
	public function formAction() {
    	parent::baseAction();        
    	if (!$this->canCreate()) $this->unauthorized();
    	
    	$form = $this->getForm();
    	
    	$form->build();
    	
    	$process = true;
    	if ($this->_request->getParam(__ZFEXT_PREFIX.'-form-stateful-reset',0)==1) {
    		$form->clearState();
    		$process = false;
    	}
    	
    	$form->populateFromState();
    	
    	$form->secure($this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName().'.'.$this->getRequest()->getActionName());
    	
    	if (!$this->handleWidgetCallback()) {
	    	if (($this->getRequest()->isPost()) && $process) {    		    		
	    		if ($form->isValid($this->_getAllParams())) {
	    			$success = $this->getSuccessMethodName();
	    			$form->saveToState();    			  
					$this->$success($form);	
	    		}    		
	    	}    	
    	
	    	$this->view->form = $form;    	
	
	    	$this->buildFormNavigation();
	    	
	    	$this->view->bottomNavigation = $this->getNavigation('bottom');
	    	$this->view->leftNavigation = $this->getNavigation('left');    	
    	}
    }
    
    public function buildFormNavigation() {
    	$nav = parent::buildFormNavigation();
    	 
    	if ($this->getCreateEnabled() && $this->canCreate($this->getRequest()->getActionName())) {
    		$nav->addItem($nav->createItem('Form_Stateful_Reset','reset',array(    				
    			'module'=>$this->getRequest()->getModuleName(),
    			'controller'=>$this->getRequest()->getControllerName(),
    			'action'=>$this->getFormActionName(),
    			'form'=>$this->getForm()
    		)));
    	}
    	
    	$nav->setWidgetOrder(array('reset','submit'));
    	 
    	return $nav;
    }
   
    protected function getForm() {
    	$this->form = parent::getForm();
    	
    	if ($this->form != null) {
	    	$this->form->setStatefulNamespace($this->getStatefulNamespace());
	    	$this->form->setStatefulPrefix($this->getStatefulPrefix());
    	}
    	
    	return $this->form;
    }
    
	public function getStatefulNamespace() {    	
    	return $this->statefulNamespace;
    }
    
    public function setStatefulNamespace($statefulNamespace) {
    	$this->statefulNamespace = $statefulNamespace;
    }
    
    public function getStatefulPrefix() {
    	return $this->statefulPrefix;
    }
    
    public function setStatefulPrefix($statefulPrefix) {
    	$this->statefulPrefix = $statefulPrefix;
    }
	
}

