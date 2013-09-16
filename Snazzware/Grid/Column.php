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

abstract class Snazzware_Grid_Column {

	/**
	 * @var Zend_View_Interface
	 */
	protected $_view;

	protected $mName = '';
	protected $mCaption = '';
	protected $mHelper = 'Grid_Content_Text';
	protected $mFilterHelper = 'Grid_Filter_Text';
	protected $mHeaderHelper = 'Grid_Column_Header';
	protected $mColHelper = 'Grid_Column_Col';
	protected $mDataHelper = 'Grid_Column_Data';
	protected $mValueHelper = 'Grid_Column_Value';
	protected $mDataClass = '';	
	protected $mGrid = null;
	
	protected $mOptions = array();

	public function __construct($name,$caption = null,$options=array()) {
		$this->setName($name);
		
		$this->setOptions($options);
		
		if ($caption == null) $this->setCaption($name);
		else $this->setCaption($caption);
	}
	
	public function handleRequest($request,$view) {
		$handled = false;
		 
		if ($request->getParam(__ZFEXT_PREFIX."_grid_{$this->getGrid()->getName()}_{$this->getName()}",0)==1) {
			$handled = true;
		}
		
		return $handled;
	}
	
	public function setGrid($grid) {
		$this->mGrid = $grid;
	}
	
	public function getGrid() {
		return $this->mGrid;
	}
	
	public function isCustomizable() {
		return true;
	}
	
	public function addHelper($type, $helper) {
		if (!isset($this->mOptions['helpers'][$type])) $this->mOptions['helpers'][$type] = array();
		$this->mOptions['helpers'][$type][] = $helper;
	}

	public function setOptions($options) {
		$this->mOptions = $options;
	}
	
	public function setOption($option, $value) {
		$this->mOptions[$option] = $value;
	}
	
	public function getOption($option, $default=null) {
		if (isset($this->mOptions[$option])) {
			return $this->mOptions[$option];
		} else return $default;
	}
	
	public function getOptions($row = array()) {
		$options = $this->mOptions;
		
		if (isset($this->mOptions['helpers']['options'])) {			
			foreach ($this->mOptions['helpers']['options'] as $helper) {				
				$options = array_merge($options, $helper->getOptions($this, $row));
			}
		}
		
		$options = array_merge($options,array(
			'row'=>$row,
			'callbackUrl'=>$this->getGrid()->getCallbackUrl(),
			'fullname'=>$this->getFullName(),
			'grid'=>$this->getGrid(),
			'column'=>$this
		));

		return $options;
	}
	
	public function setName($value) { $this->mName = $value; }
	public function getName() { return $this->mName; }
	
	public function getFullName() {
		$fullName = $this->getName();
		
		if (is_object($this->getGrid())) {
			$fullName = "{$this->getGrid()->getName()}_{$fullName}";	
		}
		
		return $fullName;
	}
	
	public function setCaption($value) { $this->mCaption = $value; }
	public function getCaption() { return $this->mCaption; }

	public function setHelper($value) { $this->mHelper = $value; }
	public function getHelper() { return $this->mHelper; }
	
	public function setFilterHelper($value) { $this->mFilterHelper = $value; }
	public function getFilterHelper() {	return $this->mFilterHelper; }
	
	public function setHeaderHelper($value) { $this->mHeaderHelper = $value; }
	public function getHeaderHelper() {	return $this->mHeaderHelper; }
	
	public function setColHelper($value) { $this->mColHelper = $value; }
	public function getColHelper() {	return $this->mColHelper; }
	
	public function setDataHelper($value) { $this->mDataHelper = $value; }
	public function getDataHelper() {	return $this->mDataHelper; }
	
	public function setValueHelper($value) { $this->mValueHelper = $value; }
	public function getValueHelper() {	return $this->mValueHelper; }

	public function setDataClass($value) { $this->mDataClass = $value; }
	public function getDataClass() { return $this->mDataClass; }
	
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