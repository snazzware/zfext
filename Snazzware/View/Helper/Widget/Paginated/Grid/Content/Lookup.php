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

class Snazzware_View_Helper_Widget_Paginated_Grid_Content_Lookup extends Zend_View_Helper_FormElement {
	
	public function Widget_Paginated_Grid_Content_Lookup($name, $value = null, $attribs = null, $options = null) {
		
		$xhtml = '';
		
		if (!is_object($value)) {
			if (isset($options['values'])) {				
				if (isset($options['values'][trim($value)])) {
					$value = $options['values'][$value]; 		
				}
			}
					
			$xhtml .= $value;
		}
		
		return $xhtml;
	}
	
}
