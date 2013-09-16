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

class Snazzware_Widget_Paginated_Grid_Column_Hidden extends Snazzware_Widget_Paginated_Grid_Column {
	
	public function __construct($name,$options=array()) {
		if (!isset($options['export'])) $options['export'] = false;
		if (!isset($options['display'])) $options['display'] = false;
		parent::__construct($name,$options);
	}
	
	public function isCustomizable() {
		return false;
	}
	
	public function renderHeader($grid) {
		return '';
	}
	
	public function renderData($row) {
		return '';
	}	
	
	public function renderCol($grid) {		
		return '';
	}
	
	public function renderValue($grid) {
		return '';
	}
	
}

