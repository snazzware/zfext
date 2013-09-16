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

class Snazzware_Form_Element_Populator extends Snazzware_Form_Element_Xhtml {
	
	public $helper = "Form_Populator";	
		
	protected $mColumns = null;
	protected $mEntityClass = null;
	protected $mAddMethod = null;
	protected $mDeleteMethod = null;
	protected $mDeleteColumnName = '_deleted';	
	
	public function setOption($option, $value) { $this->options[$option] = $value; }	
	
	public function getCaption() { return $this->options['caption']; }
	public function setCaption($value) { $this->options['caption'] = $value; }
	
	public function getItems() { if (!isset($this->options['items'])) $this->options['items'] = array(); return $this->options['items']; }
	public function getItem($name) {
		if (isset($this->options['items'][$name])) return $this->options['items'][$name];
		else return null;
	}
	public function addItem(Snazzware_Form_Element_Populator_Item $item) { $this->getItems(); $this->options['items'][$item->getName()] = $item; }
	
}