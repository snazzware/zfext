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


class Snazzware_View_Helper_Grid_Column_Value extends Zend_View_Helper_Abstract {
	
	public function Grid_Column_Value($row, $column) {
		$xhtml = '';
		
		$options = $column->getOptions($row);
		$attribstr = '';
		if (isset($options['attribs'])) {
			$attribs = array();
			foreach ($options['attribs'] as $name=>$value) {
				$escaped = str_replace('\'','\\\'',$value);
				$attribs[] = "$name='$escaped'";
			}
			$attribstr = implode(' ',$attribs);
		}
		
		$helper = $column->getHelper();
		$xhtml .= $column->getView()->$helper($column->getName(),$row[$column->getName()],null,$options);		
		
		return $xhtml;
	}
	
}
