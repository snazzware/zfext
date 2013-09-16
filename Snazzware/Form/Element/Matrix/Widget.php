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

class Snazzware_Form_Element_Matrix_Widget {
	
	protected $mName;
	protected $mOptions = array();
	
	public function __construct($name, $options = array()) {		
		$this->setName($name);
		$this->setOptions(array_merge($this->getOptions(), $options));		
	}
	
	public function setName($value) { $this->mName = str_replace('.','_',$value); }
	public function getName() { return $this->mName; }
	
	public function setOptions($value) { $this->mOptions = $value; }
	public function getOptions() { return $this->mOptions; }
	
	public function render($view = null, $options = array()) {
		return '';
	}
	
}

?>