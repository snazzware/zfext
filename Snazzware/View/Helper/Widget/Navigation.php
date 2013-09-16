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

class Snazzware_View_Helper_Widget_Navigation extends Zend_View_Helper_Abstract {
	
	public function Widget_Navigation($view, $widget) {
		$xhtml = '';
		
		$options = $widget->getOptions();
		
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml .= "<div id='{$zfext}_widget_navigation_{$widget->getName()}' class='{$zfext}-widget-navigation {$zfext}-widget-navigation-{$widget->getName()}'>";
        
        foreach ($widget->getWidgets() as $child) {
        	$xhtml .= $child->render($view);
        }
        
        $xhtml .= "</div>";
		
		return $xhtml;
	}
	
}
