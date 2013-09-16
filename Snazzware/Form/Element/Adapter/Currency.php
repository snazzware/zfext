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

class Snazzware_Form_Element_Adapter_Currency extends Snazzware_Form_Element_Adapter {
	
	public function toString($value) {		
		$value = preg_replace('/[^0-9\.]/Uis','', $value);
		
		if (!is_numeric($value)) $value = 0;
		
		return number_format($value, 2, '.', '');		
	}
	
	public function fromString($value) {
		$value = preg_replace('/[^0-9\.]/Uis','', $value);
		
		return $value;
	}
	
}


?>