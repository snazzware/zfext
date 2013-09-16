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

class Snazzware_Form extends Zend_Form {

	private $mReadOnly = false;
	private $mUppercase = false;	
	protected $id = '';
	protected $widget = null;
	
	public function init()
    {
    	parent::init();
    	
    	$this->setId(__ZFEXT_PREFIX.'Form'); // default global form id
    	
    	$this->addPrefixPath('Snazzware_Form', 'Snazzware/Form/');
    	$this->addPrefixPath('Snazzware_Form_Element', 'Snazzware/Element/', self::ELEMENT);    	
    }	
    
    public function createWidget($type, $name, $options = array()) {
    	if ($this->widget == null) $this->widget = new Snazzware_Widget;
    	return $this->widget->createWidget($type, $name, $options);
    }
    
    public function secure($basePath = '') {
    	$allReadOnly = true;
    	foreach ($this->getElements() as $element) {
    		if ($element instanceof Snazzware_Form_Element) {
	    		if (!SecurityUtils::canUpdate($basePath.'.'.strtolower($element->getName()))) {	    			
	    			$element->setEditable(false);
	    		} else {
	    			$allReadOnly = false;
	    		}
    		}
    	}
    	if ($allReadOnly) $this->mReadOnly = true;
    }
    
	public function setReadOnly($value) {		
		foreach ($this->getElements() as $element) {			
			if ($element instanceof Snazzware_Form_Element) {
				$element->setReadOnly($value);
			}			
		}
		$this->mReadOnly = $value;
	}
	
	public function getReadOnly() {
		return $this->mReadOnly;
	}
 
	public function setUppercase($value=true,$options=array()) {			
		foreach ($this->getElements() as $element) {			
			if ($element instanceof Snazzware_Form_Element) {										
				if ((!isset($options['exclude'])) || (!in_array($element->getName(),$options['exclude']))) {					
					$element->setUppercase($value);
				}
			}			
		}		
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	// This is temporary - eventually forms themselves will be widgets, along with elements, etc.
	public function handleWidgetCallback($request, $view) {	
		$handled = false;
		
		$widgetName = $request->getParam('_widgetName',null);
	
		if ($widgetName != null) {
			foreach ($this->getElements() as $element) {
				if ($element instanceof Snazzware_Form_Element_Widget) {			
					$widget = $element->getWidget();
		
					if (($widget != null) && ($widget->getName()==$widgetName)) {
						$handled = $widget->handleRequest($request, $view);
					}
				}
			}
		}	
	
		return $handled;
	}
	
}


?>