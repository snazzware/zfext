<?
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

class Snazzware_View_Helper_Widget_Navigation_Item extends Zend_View_Helper_Abstract {
	
	public function Widget_Navigation_Item($view, $widget) {				
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml = '';
		
		$front = Zend_Controller_Front::getInstance();
		
		$selected = '';
		
		if (($front->getRequest()->getModuleName() == $widget->getOption('module')) &&
			($front->getRequest()->getControllerName() == $widget->getOption('controller'))) {
				$selected = "{$zfext}-widget-navigation-item-selected";
		}
		
		if ($widget->canExecute()) {
			$xhtml .= "<div id='{$widget->getId()}' class='{$zfext}-widget-navigation-item {$selected} {$zfext}-widget-navigation-item-{$widget->getName()}' >";			
			
			$icon = $widget->getOption('icon','');			
			
			if ($icon != '') {
				$xhtml .= "<img src='{$icon}' />";
			}
			$xhtml .= $widget->getOption('caption','');
			
			$xhtml .= "</div>";
		}
		return $xhtml;
	}
	
}
