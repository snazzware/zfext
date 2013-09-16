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

abstract class Snazzware_Widget_Paginated_Grid_Column {

	/**
	 * @var Zend_View_Interface
	 */
	protected $_view;

	protected $name = '';	
	protected $helper = 'Widget_Paginated_Grid_Content_Text';
	protected $filterHelper = 'Widget_Paginated_Grid_Filter_Text';
	protected $headerHelper = 'Widget_Paginated_Grid_Column_Header';
	protected $colHelper = 'Widget_Paginated_Grid_Column_Col';
	protected $dataHelper = 'Widget_Paginated_Grid_Column_Data';
	protected $valueHelper = 'Widget_Paginated_Grid_Column_Value';
	protected $dataClass = '';	
	protected $grid = null;
	
	protected $options = array();

	public function __construct($name,$options=array()) {
		$this->setName($name);		
		$this->setOptions($options);		
	}
	
	public function handleRequest($request,$view) {
		$handled = false;
		 
		if ($request->getParam(__ZFEXT_PREFIX."_grid_{$this->getGrid()->getName()}_{$this->getName()}",0)==1) {
			$handled = true;
		}
		
		return $handled;
	}
	
	public function setGrid($grid) {
		$this->grid = $grid;
	}
	
	public function getGrid() {
		return $this->grid;
	}
	
	public function isCustomizable() {
		return true;
	}
	
	public function addHelper($type, $helper) {
		if (!isset($this->options['helpers'][$type])) $this->options['helpers'][$type] = array();
		$this->options['helpers'][$type][] = $helper;
	}

	public function setOptions($options) {
		$this->options = $options;
	}
	
	public function setOption($option, $value) {
		$this->options[$option] = $value;
	}
	
	public function getOption($option, $default=null) {
		if (isset($this->options[$option])) {
			return $this->options[$option];
		} else return $default;
	}
	
	public function getOptions($row = array()) {
		$options = $this->options;
		
		if (isset($this->options['helpers']['options'])) {			
			foreach ($this->options['helpers']['options'] as $helper) {				
				$options = array_merge_recursive_distinct($options, $helper->getOptions($this, $row));
			}
		}
		
		$options = array_merge_recursive_distinct($options,array(
			'row'=>$row,
			'callbackUrl'=>$this->getGrid()->getCallbackUrl(),
			'fullname'=>$this->getFullName(),
			'grid'=>$this->getGrid(),
			'column'=>$this
		));

		return $options;
	}
	
	public function setName($value) { $this->name = $value; }
	public function getName() { return $this->name; }
	
	public function getFullName() {
		$fullName = $this->getName();
		
		if (is_object($this->getGrid())) {
			$fullName = "{$this->getGrid()->getName()}_{$fullName}";	
		}
		
		return $fullName;
	}
	
	public function setCaption($value) { $this->setOption('caption',$value); }
	public function getCaption() { return $this->getOption('caption'); }

	public function setHelper($value) { $this->helper = $value; }
	public function getHelper() { return $this->helper; }
	
	public function setFilterHelper($value) { $this->filterHelper = $value; }
	public function getFilterHelper() {	return $this->filterHelper; }
	
	public function setHeaderHelper($value) { $this->headerHelper = $value; }
	public function getHeaderHelper() {	return $this->headerHelper; }
	
	public function setColHelper($value) { $this->colHelper = $value; }
	public function getColHelper() {	return $this->colHelper; }
	
	public function setDataHelper($value) { $this->dataHelper = $value; }
	public function getDataHelper() {	return $this->dataHelper; }
	
	public function setValueHelper($value) { $this->valueHelper = $value; }
	public function getValueHelper() {	return $this->valueHelper; }

	public function setDataClass($value) { $this->dataClass = $value; }
	public function getDataClass() { return $this->dataClass; }
	
	public function renderHeader($grid) {		
		$helper = $this->getHeaderHelper();
		return $this->getView()->$helper($grid,$this);		
	}
	
	public function renderCol($grid) {		
		$helper = $this->getColHelper();
		return $this->getView()->$helper($grid,$this);		
	}	
	
	public function renderData($row) {
		$helper = $this->getDataHelper();
		return $this->getView()->$helper($row,$this);		
	}
	
	public function renderValue($row) {
		$helper = $this->getValueHelper();
		return $this->getView()->$helper($row,$this);
	}

	/**
	 * Set view object
	 *
	 * @param  Zend_View_Interface $view
	 * @return Zend_Form
	 */
	public function setView(Zend_View_Interface $view = null)
	{
		$this->_view = $view;
		return $this;
	}

	/**
	 * Retrieve view object
	 *
	 * If none registered, attempts to pull from ViewRenderer.
	 *
	 * @return Zend_View_Interface|null
	 */
	public function getView()
	{
		if (null === $this->_view) {
			require_once 'Zend/Controller/Action/HelperBroker.php';
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			$this->setView($viewRenderer->view);
		}

		return $this->_view;
	}
	
	public function translateFilter(&$filter) {
		$filterOptions = $this->getOption('filter',null);
		if (is_array($filterOptions) && ($filterOptions['exactmatch']==true)) {
			if ($filter['op']=='like') $filter['op'] = '=';
		}
	}

}

?>