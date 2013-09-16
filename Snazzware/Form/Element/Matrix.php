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

class Snazzware_Form_Element_Matrix extends Snazzware_Form_Element_Xhtml {
	
	public $helper = "Form_Matrix";
		
	protected $mColumns = null;
	protected $mEntityClass = null;
	protected $mAddMethod = null;
	protected $mDeleteMethod = null;
	protected $mDeleteColumnName = '_deleted';	
	
	public function __construct($spec, $options = null) {
		parent::__construct($spec, $options);
		
		if ($options == null) $options = array();
		
		$options = array_merge(array('width'=>'400px','caption'=>''),$options);
		
		$this->setWidth($options['width']);
	}
	
	public function setAddMethod($value) { $this->mAddMethod = $value; }
	public function getAddMethod() { return $this->mAddMethod; }
	
	public function getOnClickUrl() { if (!isset($this->options['onClickUrl'])) $this->options['onClickUrl'] = ''; return $this->options['onClickUrl']; }
	public function setOnClickUrl($value) { $this->options['onClickUrl'] = $value; }
	
	public function setDeleteMethod($value) { $this->mDeleteMethod = $value; }
	public function getDeleteMethod() { return $this->mDeleteMethod; }
	
	public function setEntityClass($value) { $this->mEntityClass = $value; }
	public function getEntityClass() { return $this->mEntityClass; }
	
	public function setDeleteColumnName($value) { $this->mDeleteColumnName = $value; }
	public function getDeleteColumnName() { return $this->mDeleteColumnName; }
	
	public function getExcludeKeys() { return array('x'); }
	
	public function setOption($option, $value) { $this->options[$option] = $value; }
	
	public function getEditable() { if (!isset($this->options['editable'])) $this->options['editable'] = true; return $this->options['editable']; }
	public function setEditable($value) { $this->options['editable'] = $value; }
	
	public function getWidth() { return $this->options['width']; }
	public function setWidth($value) { $this->options['width'] = $value; }
	
	public function getCaption() { return $this->options['caption']; }
	public function setCaption($value) { $this->options['caption'] = $value; }
	
	public function getColumns() { if (!isset($this->options['columns'])) $this->options['columns'] = array(); return $this->options['columns']; }
	public function getColumn($name) {
		if (isset($this->options['columns'][$name])) return $this->options['columns'][$name];
		else return null;
	}
	public function addColumn(Snazzware_Form_Element_Matrix_Column $column) { $this->getColumns(); $this->options['columns'][$column->getName()] = $column; }
	
	public function getWidgets() { if (!isset($this->options['widgets'])) $this->options['widgets'] = array(); return $this->options['widgets']; }
	public function getWidget($name) {
		if (isset($this->options['widgets'][$name])) return $this->options['widgets'][$name];
		else return null;
	}
	public function addWidget(Snazzware_Form_Element_Matrix_Widget $widget) { $this->getWidgets(); $this->options['widgets'][$widget->getName()] = $widget; }
	
	public function isValid($value, $context = null)
    {
    	$valid = parent::isValid($value, $context);

    	foreach ($value as $rownumber=>$row) {    		
    		if (is_numeric($rownumber)) { // skip template row    			
		    	foreach ($this->getColumns() as $colname=>$column) {	    		
		    		if (isset($row[$colname])) {	    			
		    			$colvalid = $column->isValid($row[$colname]);
		    			if (!$colvalid) {
		    				$valid = false;
		    			}	    			
		    		}		
		    	}
    		}
    	}     

    	return $valid;
    }
	
    public function addFilter($filter, $options = array()) {
    	foreach ($this->getColumns() as $column) {
    		$column->addFilter($filter, $options);
    	}
    	
    	return $this;
    }
    
	public function setUppercase($value = true) {
    	if ($value == true) {
    		foreach ($this->getColumns() as $column) {
    			$options = $column->getOptions();    
    			if (isset($options['attribs']['style'])) {	
	    			$style = trim($options['attribs']['style']);
    			} else {
    				$style = '';
    			}
				if ((strlen($style)>0) && (substr($style,-1)!=';')) $style .= '; ';
				$style .= ' text-transform: uppercase; ';
				$options['attribs']['style'] = $style;
				$column->setOptions($options);
				$column->addFilter('StringToUpper');
    		}
    	} else {
    		// TODO : un-uppercase
    	}
    }
    
}