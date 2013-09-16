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

class Snazzware_View_Helper_Grid_Column_Col extends Zend_View_Helper_Abstract {
	
	public function Grid_Column_Col($grid, $column) {
		$xhtml = '';
		
		$styles = array();
		
		$name = str_replace('.','_',$column->getName());
		
		$width = Preferences::get(__ZFEXT_PREFIX.'_Grid-ColumnWidths',"{$grid->getName()}_{$name}",$column->getOption('width','0'));
		
		if (strpos($width,'%')===false) {
			$width .= '%';
		}
		$styles[] = "width: {$width}";
		
		if (count($styles)>0) {
			$style = " style='".implode('; ',$styles)."' ";
		} else $style = '';
		
		$xhtml .= "<col $style name='{$column->getName()}' />";
		
		return $xhtml;
	}
	
}
