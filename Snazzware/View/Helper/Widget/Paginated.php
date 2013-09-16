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

class Snazzware_View_Helper_Widget_Paginated extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated($view, $widget) {
		$xhtml = '';
		
		$options = $widget->getOptions();
		
		if (isset($options['controls'])) $controls = $options['controls'];
		else $controls = array('top');		
		
		$paginationHelper = $widget->getPaginationHelper();
		$pageHelper = $widget->getPageHelper();
		
		$zfext = __ZFEXT_PREFIX;
		
		$loadingImage = ConfigUtils::get('global','image_loading');
		
		/*if ($options['renderContainer']) {
			$xhtml .= "<div class='{$zfext}-widget-paginated' id='{$zfext}_widget_paginated_{$widget->getName()}'>";
		}*/
		if (in_array('top',$controls)) $xhtml .= $view->$paginationHelper($view, $widget, 'top');		
		$xhtml .= $view->$pageHelper($view, $widget);
		if (in_array('bottom',$controls)) $xhtml .= $view->$paginationHelper($view, $widget, 'bottom');
		
		$xhtml .= "
			<div class='{$zfext}-widget-paginated-overlay' id='{$widget->getId()}_overlay'>
				<img src='{$loadingImage}' class='loading' />
			</div>
		";	
		
		if ($options['renderContainer']) {
		//	$xhtml .= "</div>";
			
			$xhtml .= "
			<script>
				function {$widget->getId()}_showLoading() {
					var overlay = $('#{$widget->getId()}_overlay');
					var widget = $('#{$widget->getId()}');
			
					var loading = overlay.find('.loading').first();
			
					loading.offset({top: Math.floor(widget.height() / 2),left: Math.floor(widget.width()/2)});
			
					overlay.show();
					overlay.offset(widget.offset());
					overlay.width(widget.width());
					overlay.height(widget.height());

				}
			</script>
			";
		}
		
		return $xhtml;
	}
	
}
