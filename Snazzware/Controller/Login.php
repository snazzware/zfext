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

class Snazzware_Controller_Login extends Snazzware_Controller_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction() {
    	
    }
    
    public function passwordAction() {
    	
    }
    
    protected function loadUserPreferences() {
    	$user = SecurityUtils::getUser();
    	
    	if ($user != null) {
    		$preferences = $user->getPreferences();
    		
    		if ($preferences != null)  {    			    		
    			foreach ($preferences as $preference) {
    				State::set('userprefs',$preference->getKey(),$preference->getValue());
    			}
    		}
    	}    	
    }
    
    /**
     * Copies user preferences from state in to user, persists
     */
    public function flushUserPreferences() {
    	// TODO
    }
    
	public function loginAction() {
    	if (SecurityUtils::isLoggedIn()) {
    		$this->redirect('/index/listing');
    	} else {
    		$form = new Snazzware_Form_Login();
    		$form->build();
    		if ($this->getRequest()->isPost()) {
    			if ($form->isValid($this->_getAllParams())) {
    				$result = SecurityUtils::login($form->username->getValue(),SecurityUtils::hashPassword($form->password->getValue()));
    				if ($result === true) {
    					$this->loadUserPreferences();
    					$this->postLogin();
    					$this->redirect('/index/listing');
    				} else {
    					$form->password->addError($result);
    				}
    			}
    		}
    		$this->view->form = $form;
    	}    	
    }
    
    public function postLogin() {
    	// stub
    }
    
    public function logoutAction() {
    	SecurityUtils::logout();
    	$this->redirect('/index');    	
    }

    public function listingAction() {
    	
    }
    
}

