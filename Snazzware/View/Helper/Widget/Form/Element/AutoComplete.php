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

class Snazzware_View_Helper_Widget_Form_Element_Autocomplete extends Zend_View_Helper_Abstract {
	
	public function Widget_Form_Element_Autocomplete($view, $widget) {
		$xhtml = '';

		$zfext = __ZFEXT_PREFIX;
		
		$hiddenName = $widget->getName();
		$hiddenId = $widget->getId().'_value';
		$autocompleteName = $widget->getName().'_autocompleter';
		$autocompleteId = $widget->getId().'_autocompleter';		
		$caption = $widget->getOption('caption','');
		$value = $widget->getOption('value',$widget->getOption('defaultValue'));
		$displayValue = $widget->getDisplayValue();
		$valueProperty = $widget->getOption('valueProperty','value');
		
		
		if ($caption != '') {
			$xhtml .= "<label for='{$autocompleteId}'>{$caption}</label>";
		}
		$xhtml .= "<input type=hidden name='{$hiddenName}' id='{$hiddenId}' value='{$value}'>";		
		$xhtml .= "<input type=text id='{$autocompleteId}' name='{$autocompleteName}' value='{$displayValue}' />";
		
		$xhtml .= "
			<script>
				$(function() {
					$('#{$autocompleteId}').autocomplete({
						source: function(request,response) {
							$('#{$widget->getId()}').{$zfext}WidgetCallback('search',{term: request.term},function(data) {
								console.log(data);													
								response($.parseJSON(data));
							});							
						},
						select: function(event, ui) {
							$('#{$hiddenId}').val($(ui.item).attr('{$valueProperty}'));
							$('#{$hiddenId}').trigger('change');
						}
					});
				});
			</script>
		";
		
		return $xhtml;
	}
	
}
