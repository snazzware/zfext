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


class Snazzware_View_Helper_Widget_Paginated_Grid_Content_Checkbox extends Zend_View_Helper_FormElement {
	
	public function Widget_Paginated_Grid_Content_Checkbox($name, $value = null, $attribs = null, $options = null) {
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml = '';

		$grid = $options['grid'];		
		$fullname = $options['fullname'];
		$loadingUrl = ConfigUtils::get(__ZFEXT_PREFIX,'image_loading_small','/images/loader-small.gif');
		$checkboxId = "{$zfext}_grid_checkbox_{$fullname}_{$options['row'][$name]}";
		$selectedId = $grid->getName().'-checkbox_selected_'.$name;
		$checkboxLoadingId = "{$zfext}_grid_checkbox_{$checkboxId}_loading";
		
		if (Snazzware_Grid_Column_Checkbox::isChecked($fullname,$options['row'][$name])) {		
			$checked = ' checked ';
		} else {
			$checked = '';
		}
		
		$xhtml .= "<input type='checkbox' {$zfext}_neverdirty='1' id='{$checkboxId}' style='cursor: pointer' $checked></input><img src='{$loadingUrl}' id='{$checkboxLoadingId}' style='display: none' />";
		
		$xhtml .= "
			<script>
				// TODO : This can probably be made generic, and just have one of these functions per grid. Something to look in to. Maybe add a static class method to render column support scripting.
				function {$zfext}_grid_checkbox_{$checkboxId}_click(event,external) {
					var cb = $('#{$checkboxId}');
				
					if (external) {						
						cb.attr('checked', !cb.attr('checked'));
					}
					
					$('#{$checkboxId}').hide();
					$('#{$checkboxLoadingId}').show();
		
					var parameters = new Object();
					var checked = cb.attr('checked');
					
					if (checked!='checked') checked = 'unchecked';
					
					$(parameters).attr('{$zfext}_grid_{$fullname}',1);
					$(parameters).attr('{$zfext}_grid_{$fullname}_{$options['row'][$name]}',checked);					
					
					// dispatch and update     
					$.get({$grid->getCallbackUrl()}, parameters, function(data) { 
						if (data) {
							$('#{$selectedId}').html(data.selected + ' selected');
						}

						// TODO : show an error symbol or uncheck the checkbox if ajax request fails
						
						$('#{$checkboxLoadingId}').hide();
						$('#{$checkboxId}').show();			
					});
					
				}
				
				$('#{$checkboxId}').click(function(event) {
					event.stopPropagation();
					{$zfext}_grid_checkbox_{$checkboxId}_click(event,false);
				});
				
				$('#{$checkboxId}').parent().click(function(event) {
					event.stopPropagation();
					{$zfext}_grid_checkbox_{$checkboxId}_click(event,true);
				});
				
			</script>
		";
		
		
		return $xhtml;
	}
	
}
