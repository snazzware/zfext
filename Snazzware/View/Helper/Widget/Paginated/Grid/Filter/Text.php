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

class Snazzware_View_Helper_Widget_Paginated_Grid_Filter_Text extends Zend_View_Helper_FormElement {
	
	public function Widget_Paginated_Grid_Filter_Text($name, $value = null, $attribs = null, $options = null) {
		
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml = '';
		
		if (isset($options['id'])) $filterid = $options['id'];
		else $filterid = '';
		
		$xhtml .= "<input type=text id='$filterid' name='$name' class='{$zfext}-grid-filter' value='$value' {$zfext}_neverdirty=1 />";
		
		if (isset($options['mode'])) {
			switch ($options['mode']) {
				case 'autocomplete':
					$availableOptions = "'".implode("','",$options['options'])."'";
					$xhtml .= "
						<script>
							$(function() {
								var availableOptions = [
									{$availableOptions}
								];
								
								$('#{$filterid}').autocomplete({
									source: availableOptions,
									minLength: 0,
									select: function(event, ui) {
										if (ui.item) {
											$('#{$filterid}').val(ui.item.value);
										}
										{$zfext}_grid_apply_filters_{$options['grid']}();
									}
								});
							});
						</script>
					";	
				break;
			}
		}
		
		return $xhtml;
	}
	
}
