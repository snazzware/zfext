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

class Snazzware_Controller_Action extends Zend_Controller_Action {
	
	private $widget = null;
	private $widgets = array();
	private $navigation = null;
	private $navigationLeft = null;
	private $navigations = null;
	private $navigationWidgetClassName = 'Snazzware_Widget_Navigation';
	private $defaultNavigationArea = 'left';
	
	public function addWidget($widget) {
		$this->widgets[$widget->getName()] = $widget;
	}
	
	public function getWidget($name) {
		if (isset($this->widgets[$name])) return $this->widgets[$name];
		else return null;
	}
	
	public function getWidgets() {
		return $this->widgets;
	}
	
	public function createWidget($type, $name, $options = array()) {
		if ($this->widget == null) $this->widget = new Snazzware_Widget;
		return $this->widget->createWidget($type, $name, $options);
	}
	
	public function init() {
		if (!isset($this->view->navigationIsBeingBuilt)) {
			$this->view->navigationIsBeingBuilt = true;								
			$this->view->navigation = NavigationFactory::create(array('stereotype'=>NavigationFactory::stereotype_navigation));		
			unset($this->view->navigationIsBeingBuilt);
		}
	}
	
	public function allow($role, $access, $path = '') {
		$realpath = $this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName();
		if ($path != '') $realpath .= '.'.$path;
		SecurityUtils::allow($role, $access, $realpath);
	}
	
	public function getMostRecentAction($actions = array()) {
		$actionStack = State::get('snazzware_controller_action','actionStack',array());
		if (count($actions)>0) {
			while (count($actionStack)>0) {
				$action = array_shift($actionStack);
				if (in_array($action, $actions)) return $action;
			}
			return '';
		} else if (count($actionStack)>0) return reset($actionStack);
		else return '';
	}
	
	public function baseAction() {		
		if (!$this->canExecute()) {			
			$this->unauthorized();
		} else {
			$actionStack = State::get('snazzware_controller_action','actionStack',array());
			$count = array_unshift($actionStack, $this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName().'.'.$this->getRequest()->getActionName());
			if ($count>ConfigUtils::get('snazzware_controller_action','actionStackMaxSize',10)) {
				array_pop($actionStack);
			}
			State::set('snazzware_controller_action','actionStack',$actionStack);
		}
	}
	
	public function unauthorized() {		
		$this->redirect(ConfigUtils::get('security','unauthorizedUrl'));
	}
	
	public function redirect($url, array $options = array()) {
		$redirector = $this->_helper->getHelper('Redirector');
		
		$redirector->setExit(true)->gotoUrl($url, $options);
	}
	
	/**
	 * Call this function from your widget ajax callback actions, once your widgets have been built, to see if any of them
	 * want to handle the request. If this function returns true, your action should not render anything.
	 *
	 * @return boolean
	 */
	public function handleWidgetCallback() {
		$this->preHandleWidgetCallback();
	
		$handled = false;
	
		$widgetName = $this->getRequest()->getParam('_widgetName',null);
	
		if ($widgetName != null) {
			$widget = $this->getWidget($widgetName);
			if ($widget != null) {
				$handled = $widget->handleRequest($this->getRequest(),$this->view);
			}
		}
	
		$this->postHandleWidgetCallback($handled);
	
		return $handled;
	}
	
	protected function preHandleWidgetCallback() {
		// stub
	}
	
	protected function postHandleWidgetCallback($handled) {
		if ($handled) {
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
		}
	}
	
	public function canRead($action = '') {
		if ($action == '') $action = $this->getRequest()->getActionName(); 		
		return SecurityUtils::canRead($this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName().'.'.$action);
	}
	
	public function canCreate($action = '') {
		if ($action == '') $action = $this->getRequest()->getActionName(); 		
		return SecurityUtils::canCreate($this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName().'.'.$action);
	}
	
	public function canUpdate($action = '') {
		if ($action == '') $action = $this->getRequest()->getActionName(); 		
		return SecurityUtils::canUpdate($this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName().'.'.$action);
	}
	
	public function canDelete($action = '') {
		if ($action == '') $action = $this->getRequest()->getActionName(); 		
		return SecurityUtils::canDelete($this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName().'.'.$action);
	}
	
	public function canExecute($action = '') {
		if ($action == '') $action = $this->getRequest()->getActionName(); 		
		return SecurityUtils::canExecute($this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName().'.'.$action);
	}
	
	protected function buildNavigation($name = 'bottom') {
		$nav = new Snazzware_Navigation();
	
		$nav->setContainerClass(__ZFEXT_PREFIX."-navigation-{$name}");
	
		return $nav;
	}
	
	protected function getNavigation($name = 'bottom') {
		$navigationWidgetClassName = $this->getNavigationWidgetClassName();
	
		return $navigationWidgetClassName::getNavigation($name);
	}
	
	protected function setNavigation($name, $value) {
		$this->navigation[$name] = $value;
	}
	
	protected function getDefaultNavigationArea() { return $this->defaultNavigationArea; }
	protected function setDefaultNavigationArea($value) { $this->defaultNavigationArea = $value; }
	
	protected function getNavigationWidgetClassName() { return $this->navigationWidgetClassName; }
	protected function setNavigationWidgetClassName($value) { $this->navigationWidgetClassName = $value; }
	
	
	
		
}


?>