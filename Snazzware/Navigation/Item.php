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

class Snazzware_Navigation_Item {
	
	private $name = '';
	private $caption = '';
	private $url = '';
	private $onclick = '';
	private $module = '';
	private $controller = '';
	private $action = '';
	private $security = '';
	private $navigation = null;
	private $icon = '';
	private $options = array();
	
	public function setName($value) { $this->name = $value; }
	public function getName() { return $this->name; }
	public function setCaption($value) { $this->caption = $value; }
	public function getCaption() { return $this->caption; }
	public function setUrl($value) { $this->url = $value; }
	public function getUrl() { return $this->url; }
	public function setOnclick($value) { $this->onclick = $value; }
	public function getOnclick() { return $this->onclick; }
	public function setModule($value) { $this->module = $value; }
	public function getModule() { return $this->module; }
	public function setController($value) { $this->controller = $value; }
	public function getController() { return $this->controller; }
	public function setAction($value) { $this->action = $value; }
	public function getAction() { return $this->action; }
	public function setSecurity($value) { $this->security = $value; }
	public function getSecurity() { return $this->security; }
	public function setNavigation($value) { $this->navigation = $value; }
	public function getNavigation() { return $this->navigation; }
	public function setIcon($value) { $this->icon = $value; }
	public function getIcon() { return $this->icon; }
	
	public function getOptions() { return $this->options; }	
	public function setOptions($value) { $this->options = $value; }
	
	public function __construct($options) {
		$this->setOptions($options);
		
		if (isset($options['name'])) $this->setName($options['name']);
		if (isset($options['caption'])) $this->setCaption($options['caption']);
		if (isset($options['url'])) $this->setUrl($options['url']);
		if (isset($options['onclick'])) $this->setOnclick($options['onclick']);
		if (isset($options['module'])) $this->setModule($options['module']);
		if (isset($options['controller'])) $this->setController($options['controller']);
		if (isset($options['action'])) $this->setAction($options['action']);
		if (isset($options['security'])) $this->setSecurity($options['security']);
		if (isset($options['icon'])) $this->setIcon($options['icon']);
		
		if ($this->getUrl() == '') {
			// TODO : use base url...
			$this->setUrl("/{$this->getModule()}/{$this->getController()}/{$this->getAction()}");
		}
		
		if ($this->getSecurity() == '') {
			$this->setSecurity($this->getModule().'.'.$this->getController().'.'.$this->getAction());
		}
	}
	
	public function canExecute() {		 	
		return SecurityUtils::canExecute($this->getSecurity());
	}
	
	public function render(Zend_View_Interface $view = null) {
		$options = $this->getOptions();
		
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml = '';
		
		$front = \Zend_Controller_Front::getInstance();		
		
		$selected = '';
		
		if (($front->getRequest()->getModuleName() == $this->getModule()) &&
			($front->getRequest()->getControllerName() == $this->getController()) /*&&
			((trim($this->getAction())=='') || ($front->getRequest()->getActionName() == $this->getAction()))*/) {
				$selected = "{$zfext}-navigation-item-selected";
		}
		
		if ($this->canExecute()) {
			$xhtml .= "<div class='{$zfext}-navigation-item {$selected} {$zfext}-navigation-item-{$this->getName()}' ";			
			if ($this->getOnclick() != '') {
				$navscript = $this->getOnclick();
			} else {
				$navscript = "window.location='{$this->getUrl()}';";
			}
			$xhtml .= ">";
			if ($this->getIcon() != '') {
				$xhtml .= "<img src='{$this->getIcon()}' />";
			}
			$xhtml .= $this->getCaption();
			
			$xhtml .= "
				<script>
					$('.{$zfext}-navigation-item-{$this->getName()}').click(function(event) {
						event.stopPropagation();
						{$navscript}						
					});
				</script>
			";
			
			// TODO: if has sub-menu
			
			if (isset($options['dropdown'])) {
				$xhtml .= "<ul class='{$zfext}-navigation-dropdown-menu' id='{$zfext}-navigation-dropdown-menu-{$this->getName()}'>";
				$dropdownNav = $options['dropdown'];
				foreach ($dropdownNav->getItems() as $dropdownItem) {
					$xhtml .= "<li>{$dropdownItem->render()}</li>";
				}
				$xhtml .= "</ul>";
				
				$xhtml .= "
					<script>
					$(function() {
						$('.{$zfext}-navigation-item-{$this->getName()}').hover(
							function() {
								$('#{$zfext}-navigation-dropdown-menu-{$this->getName()}').slideDown(50);
							},
							function() {
								$('#{$zfext}-navigation-dropdown-menu-{$this->getName()}').slideUp(50);
							}
						);
					});
					</script>
				";
			}
			
			$xhtml .= "</div>";			
		}
		return $xhtml;
	}
	
	public function __toString()
    {
    	$xhtml = '';
    	
        try {
            $xhtml = $this->render();            
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);            
        }
        
        return $xhtml;
    }
	
}