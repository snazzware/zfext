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
 * @copyright  Copyright (c) 2011-2012 Josh M. McKee
 * @license    http://www.snazzware.com/license/new-bsd     New BSD License
 */

class Snazzware_Form_Element_Adapter {
	
	protected $mElement = null;
	protected $options = array();
	
	public function __construct($options = array()) {
		$this->options = array_merge($this->options, $options);
	}
	
	public function setOption($option, $value) { $this->options[$option] = $value; }
	
	public function toString($value) {
		return $value;
	}
	
	public function fromString($value) {
		return $value;
	}
	
	public function getElement() { return $this->mElement; }
	public function setElement($value) { $this->mElement = $value; }
	
}


?>