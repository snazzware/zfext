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

/**
 * This class serves as a generic wrapper for including Snazzware widgets embedded in
 * Zend forms.
 * 
 * @author jmc
 *
 */
class Snazzware_Form_Element_Widget extends Snazzware_Form_Element_Xhtml {
	
	public $helper = 'Form_Widget';
	
	protected $viewHelperDecoratorName = 'WidgetViewHelper';
	protected $widget;
	
	public function getWidget() { return $this->widget; }
	public function setWidget($widget) { 
		$this->widget = $widget;
		$this->widget->setOption('name',$this->getName()); 
	}
	
	public function setValue($value) {
		parent::setValue($value);
		
		$this->getWidget()->setOption('value',$value);
	}
}


?>