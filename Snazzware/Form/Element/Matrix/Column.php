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

class Snazzware_Form_Element_Matrix_Column extends Zend_Form_Element {
	
	protected $mName;
	protected $mFormHelper = 'Form_Text';	
	protected $mOptions = array('editable'=>true);
	
    protected $mValue = '';
    
	public function __construct($name, $options = array()) {
		parent::__construct($name, $options);
		
		$this->setName($name);
		$this->setOptions(array_merge($this->getOptions(), $options));		
	}
	
	public function setName($value) { $this->mName = str_replace('.','_',$value); }
	public function getName() { return $this->mName; }
	
	public function setOptions(array $options) { $this->mOptions = $options; }
	public function getOptions() { return $this->mOptions; }
	
	public function getPropertyAdapter() { if (!isset($this->mOptions['property_adapter'])) $this->mOptions['property_adapter'] = null; return $this->mOptions['property_adapter']; }
	public function setPropertyAdapter($value) { $this->mOptions['property_adapter'] = $value; }
	
	public function setFormHelper($value) { $this->mFormHelper = $value; }
	public function getFormHelper() { return $this->mFormHelper; }	
	
	public function setValue($value) { $this->mValue = $value; }
	public function getValue() { return $this->mValue; }
	
	public function getCaption() {
		if (isset($this->mOptions['caption'])) $result = $this->mOptions['caption'];
		else $result = $this->getName();
		
		return $result;
	}
	public function setCaption($value) { $this->mOptions['caption'] = $value; }
	
	
	
	public function renderFieldScript($view = null, $options = array()) {
		return '';
	}
	
	public function renderHeader($view = null, $options = array()) {
		$options = array_merge($this->getOptions(), $options);
		if (isset($options['header-style'])) $style = $options['header-style'];
		else $style = '';
		if (isset($options['width'])) $width = $options['width'];
		else $width = 0;
		return "<th style='{$style}; width: {$width}%;'>{$this->getCaption()}</th>";
	}
	
	public function renderCell($view = null, $options = array()) {		
		$options = array_merge($this->getOptions(), array('readonly'=>false, 'editable'=>true, 'value'=>''), $options); // default options		
		
		$zfext = __ZFEXT_PREFIX;
		
		if (isset($options['cell-style'])) $style = $options['cell-style'];
		else $style = '';
		
		if (isset($options['errors'])) {
			$style .= ' background-color: red; color: white; ';	
		}

		if ((isset($options['editable'])) && ($options['editable']!==true)) {
			$options['readonly'] = true;
		}
		
		$helper = $this->getFormHelper();
		
		$xhtml = '';		
		
		if (isset($options['attribs'])) $attribs = $options['attribs'];
		else $attribs = array();
		
		$xhtml .= "<td class='{$zfext}-form-element-matrix-$helper' style='{$style}'>";
		
		$xhtml .= $view->$helper(
			$options['id'],
			$options['value'],
			array_merge($attribs,array('colname'=>$this->getName())),
			$options
		);
		
		$xhtml .= '</td>';
		
		return $xhtml;
	}
	
	public function renderScripting($view = null, $options = array()) { return ''; }

	
}

?>