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

class Snazzware_View_Helper_Grid_Filter_Lookup extends Zend_View_Helper_FormElement {
	
	public function Grid_Filter_Lookup($name, $value = null, $attribs = null, $options = null) {
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml = '';
		
		if (isset($options['values']) && count($options['values'])>0) {
			if (isset($options['id'])) $filterid = $options['id'];
			else $filterid = '';
			
			$xhtml .= "<select id='$filterid' name='$name' class='{$zfext}-grid-filter' {$zfext}_neverdirty=1>";
			
			foreach ($options['values'] as $optvalue=>$optcaption) {
				$xhtml .= "<option value='$optvalue' ";
				
				$left = preg_replace("/[^A-Za-z0-9 ]/", '',trim($optvalue));
				$right = preg_replace("/[^A-Za-z0-9 ]/", '',trim($value));
				
				if ($left==$right) {
					$xhtml .= " selected='selected' ";
				}
				$xhtml .= " >$optcaption</option>";
			}
			$xhtml .= "</select>";
			
			$xhtml .= "
				<script>
					$('#{$filterid}').change(function() {
						{$zfext}_grid_apply_filters_{$options['grid']}();
					});
				</script>
			";
		}
		
		return $xhtml;
	}
	
}
