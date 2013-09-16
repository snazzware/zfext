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

/**
 * Options:
 * 
 * 
 * 
 * 
 * 
 * @author jmc
 *
 */
class Snazzware_Grid {
	
	/**
     * @var Zend_View_Interface
     */
    protected $_view;
	
    protected $mOptions = array();
    
	protected $mName = '';
	protected $mCallbackUrl = '';
	protected $mAjaxGetUrl = '';
	protected $mViewScript = 'Grid/default.phtml';
	
	protected $mColumns = null;
	
	protected $mAdapter = null;
	
	protected $mOnClickUrl = null;
	protected $mOnClick = null;
	
	protected $mDefaultFilters = true;
	protected $mAutoResize = true;
	protected $mMouseWheel = false;
	protected $mPopup = false;
	protected $mExportable = false;
	protected $mCustomizable = false;
	protected $mCustomizeUrl = '';
	
	protected $mPageSize = null;
	protected $mPage = 1;
	
	protected $mCaption = '';
	
	protected $mDefaultSort = array();
	
	protected $mHighlightedRows = array();
	
	protected $mCustomizer = null;
	protected $mCustomizerClassName = 'Snazzware_Grid_Customizer';
	
	public function getCustomizable() { return $this->mCustomizable; }
	public function setCustomizable($value) { $this->mCustomizable = $value; }
	
	public function getExportable() { return $this->mExportable; }
	public function setExportable($value) { $this->mExportable = $value; }
	
	public function getCustomizeUrl() { return $this->mCustomizeUrl; }
	public function setCustomizeUrl($value) { $this->mCustomizeUrl = $value; }
	
	public function getCustomizerClassName() { return $this->mCustomizerClassName; }
	public function setCustomizerClassName($value) { $this->mCustomizerClassName = $value; }
	
	public function getCustomizer() { 
		if ($this->mCustomizer == null) {
			$classname = $this->getCustomizerClassName();		 
			$this->mCustomizer = new $classname();
		}
		return $this->mCustomizer;
	}
	
	public function setCustomizer($value) {
		$this->mCustomizer = $value;
	}
	
	public function setCaption($value) { $this->mCaption = $value; }
	public function getCaption() { return $this->mCaption; }
	
	public function setViewScript($value) { $this->mViewScript = $value; }
	public function getViewScript() { return $this->mViewScript; }
	
	public function setDefaultFilters($value) { $this->mDefaultFilters = $value; }
	public function getDefaultFilters() { return $this->mDefaultFilters; }
	
	public function setOnClickUrl($value) { $this->mOnClickUrl = $value; }
	public function getOnClickUrl() { return $this->mOnClickUrl; }
	
	public function setAutoResize($value) { $this->mAutoResize = $value; }
	public function getAutoResize() { return $this->mAutoResize; }
	
	public function setMouseWheel($value) { $this->mMouseWheel = $value; }
	public function getMouseWheel() { return $this->mMouseWheel; }
	
	public function getOption($name,$default) {
		if (!isset($this->mOptions[$name])) return $default;
		else return $this->mOptions[$name];
	}
	
	public function setOption($name,$value) {
		$this->mOptions[$name] = $value;
	}
	
	public function getOptions() {
		return $this->mOptions;
	}
	
	public function setOptions($options) {
		$this->mOptions = $options;
	}
	
	/**
	 * Allows rows in the grid to have a highlight applied by a key/value pair. E.g. if the grid contains a column
	 * named "id" and you want to highlight any row where id=123, you would call addRowHighlight('id',123). The
	 * $class property is optional, and defaults to the class 'highlighted'. The class string is injected in to the class
	 * attribute of the <tr> tag by the default table render script, so multiple classes can be speciifed (space delimited).
	 * 
	 * @param string $column Name of the column to compare
	 * @param string $value If column contains this value, it will be highlighted
	 * @param string $class Css class(es) to add to the <tr> if highlighted
	 */
	public function addRowHighlight($column,$value,$class='highlighted') {
		if (!isset($this->mHighlightedRows[$column])) $this->mHighlightedRows[$column] = array();
		$this->mHighlightedRows[$column][$value] = $class;
	}
	
	/**
	 * Checks a given row to see if it matches any previously specified highlight criteria. If a match is found, the corresponding
	 * highlight class string is returned.
	 * 
	 * @param array $row
	 * @return string
	 */
	public function getRowHighlight($row) {
		$result = '';
		
		foreach ($this->mHighlightedRows as $column=>$values) {
			if (isset($values[$row[$column]])) $result = $values[$row[$column]];
		}
		
		return $result;
	}
	
	public function setOnClick($value) { $this->mOnClick = $value; }
	public function getOnClick() { return $this->mOnClick; }
	
	public function setPageSize($value) { if (is_numeric($value)) State::set(__ZFEXT_PREFIX.'_Grid',$this->getName().'-pagesize',$value); }
	public function getPageSize() { return State::get(__ZFEXT_PREFIX.'_Grid',$this->getName().'-pagesize',10); }
	
	public function setPage($value) { if (is_numeric($value)) State::set(__ZFEXT_PREFIX.'_Grid',$this->getName().'-page',$value); }
	public function getPage() { return State::get(__ZFEXT_PREFIX.'_Grid',$this->getName().'-page',1); }
	
	public function setAdapter($value) { $this->mAdapter = $value; }
	public function getAdapter() { return $this->mAdapter; }
	
	public function setFilters($value) { State::set(__ZFEXT_PREFIX.'_Grid',$this->getName().'-filters',$value); }
	public function getFilters() { return State::get(__ZFEXT_PREFIX.'_Grid',$this->getName().'-filters',array()); }
	public function getFilter($name) { $filters = $this->getFilters(); if (isset($filters[$name])) return $filters[$name]; else return null; }
	
	
	public function setSorts($value) { Preferences::set(__ZFEXT_PREFIX.'_Grid-Sorts',$this->getName(),$value); }
	public function getSorts() { return Preferences::get(__ZFEXT_PREFIX.'_Grid-Sorts',$this->getName(),$this->getDefaultSort());  }
	public function getSort($name) { $sorts = $this->getSorts(); if (isset($sorts[$name])) return $sorts[$name]; else return null; }
	
	public function getColumns() { if (!isset($this->options['columns'])) $this->options['columns'] = array(); return $this->options['columns']; }
	
	public function addColumn(Snazzware_Grid_Column $column) { 
		$this->getColumns(); // this is just to trigger init if the columns array 
		if ($column->getOption('rank',0)==0) $column->setOption('rank',count($this->options['columns'])); 
		$column->setGrid($this);
		$this->options['columns'][$column->getName()] = $column; 
	}
	
	public function getColumn($name) {
		if (isset($this->options['columns'][$name])) return ($this->options['columns'][$name]);
		else return null;
	}
	public function setColumn($name,$value) {
		$this->options['columns'][$name] = $value;
	}
	public function setColumns($value) {
		$this->options['columns'] = $value;
	}
	
	public function setName($value) { $this->mName = $value; }
	public function getName() { if ($this->mName=='') $this->setName(get_class($this)); return $this->mName; }
	
	public function setCallbackUrl($value) { $this->mCallbackUrl = $value; }
	public function getCallbackUrl() {
		if ($this->mCallbackUrl=='') {
			$url = 'window.location.pathname';
		} else {
			$url = "'{$this->mCallbackUrl}'";
		} 
		return $url; 
	}
	
	public function setAjaxGetUrl($value) { $this->mAjaxGetUrl = $value; }
	public function getAjaxGetUrl() { return $this->mAjaxGetUrl; }
	
	public function setDefaultSort($fieldname, $direction) { $this->mDefaultSort = array($fieldname=>array('field'=>$fieldname,'dir'=>$direction)); }
	public function getDefaultSort() { return $this->mDefaultSort; }
	
	function __construct() {
		$this->build();
	}

	/**
	 * NOTE: this is a work-in-progress and may be refactored later...
	 * the goal is to provide next/prev record IDs and total record count
	 * so that details action can allow next/prev navigation between records.
	 * 
	 * @param int $id current record id
	 */
	public function getNavigationInformation($id) {
		$navinfo = array();
				
    	$this->getAdapter()->setGrid($this);
    	
    	$filters = $this->translateFilters($this->getFilters());
    	$this->getAdapter()->setFilters($filters);
    	$this->getAdapter()->setSorts($this->getSorts());    	    	    	
    	
    	// Get total row count
        $rowcount = $this->getAdapter()->getCount();
        
        // Get current page of rows
        $rows = $this->getAdapter()->getRows(($this->getPage()-1)*$this->getPageSize(),$this->getPageSize());
        
        $found = false;
        $previous = null;
        $next = null;
        
        while (count($rows) && (!$found)) {
        	$row = array_shift($rows);
        	if ($row['id']==$id) {
        		$found = true;
        		$next = array_shift($rows);
        	} else $previous = $row;
        }
        
        if (($previous == null) && ($this->getPage()>1)) {
        	$rows = $this->getAdapter()->getRows(($this->getPage()-2)*$this->getPageSize()+($this->getPageSize()-1),1);
        	$previous = array_pop($rows);
        }
        
		if (($next == null) && ($this->getPage()+$this->getPageSize()<$rowcount)) {
        	$rows = $this->getAdapter()->getRows(($this->getPage())*$this->getPageSize(),1);
        	$next = array_shift($rows);
        }
        
        if ($previous !== null) $navinfo['previous'] = $previous['id'];        
        if ($next !== null) $navinfo['next'] = $next['id'];        
        
        // TODO : calculate position of current record
        // TODO : actually turn grid prev/next on navigate to prev/next when on a different page - pass this fact in to $navinfo
                
        return $navinfo;
	}
	
	public function getRows($offset = 0, $max = 0) {		
		$this->getAdapter()->setGrid($this);
		
		$this->getAdapter()->setFilters($this->translateFilters($this->getFilters()));
		$this->getAdapter()->setSorts($this->getSorts());
		
		$rowcount = $this->getAdapter()->getCount();
		
		if (($max!=0) && ($rowcount-$offset>$max)) $rowcount = $max;
		
		return $this->getAdapter()->getRows($offset, $rowcount);
	}
	
	// TODO : refactor a lot of the calculation and adapter init code in to separate functions
	public function render(Zend_View_Interface $view = null, $renderContainer = true)
    {
    	// Set this grid's view to the view which was passed in, if any
    	if ($view !== null) {
    		$this->setView($view);
    	}
    	
    	// Overlay any customizations for the current user
    	$this->customize();
    	
        // Send this grid, current filtering, and sorting options to adapter
        $this->getAdapter()->setGrid($this);
        $this->getAdapter()->setFilters($this->translateFilters($this->getFilters()));
        $this->getAdapter()->setSorts($this->getSorts());
        
        // Obtain total number of rows
        $rowcount = $this->getAdapter()->getCount();
        
        // Calculate total number of pages
        $pages = ceil($rowcount / $this->getPageSize());
        
        // If the current page exceeds the total number of pages, set current page to 1.
        if ($this->getPage()>$pages) $this->setPage(1);
        $page = $this->getPage();

        // Obtain rows for current page
        $rows = $this->getAdapter()->getRows(($this->getPage()-1)*$this->getPageSize(),$this->getPageSize());
        
        // Calculate the row number of first and last row being rendered
        $rowstart = (($this->getPage()-1) * $this->getPageSize()) + 1;
        $rowend = $this->getPage() * $this->getPageSize();
        
        // If last row being displayed exceeds total number of rows, set end row to number of rows.
        if ($rowend > $rowcount) $rowend = $rowcount;
        
        // Calculate page number for the "next page" function
        if ($page == $pages) $nextpage = $page;
        else $nextpage = $page+1;
        
        // Calculate page number for the "previous page" function
        if ($page==1) $prevpage = $page; 
        else $prevpage = $page-1;

        // Determine callback url for ajax refreshes
        $ajaxUrl = $this->getCallbackUrl();
        
        // Adjust column widths
        $this->fixColumnWidths();
        
        // Create array of variables for view scripts to utilize
        $vars = array(
        	'rowcount' => $rowcount,
       		'rows' => $rows,
        	'pages' => $pages,
        	'page' => $page,
        	'rowstart' => $rowstart,
        	'rowend' => $rowend,
        	'nextpage' => $nextpage,
        	'prevpage' => $prevpage,
        	'renderContainer' => $renderContainer,
        	'ajaxUrl' => $ajaxUrl
        );
        
        // render
        $view = $this->getView();
        if (isset($view->grid)) $oldgrid = $view->grid;
        else unset($oldgrid);
        $view->grid = $this;
        
        if (isset($view->vars)) $oldvars = $view->vars;
        else unset($oldvars);
        $view->vars = $vars;
        
        $xhtml = $view->render($this->getViewScript());       
        
        if (isset($oldgrid)) $view->grid = $oldgrid;
        if (isset($oldvars)) $view->vars = $oldvars;
        
		return $xhtml;
	}
	
	public function fixColumnWidths() {
		$totalWidth = 0;
		$zeroWidthColumns = array();
		
		$columns = $this->getColumns();		
		
		// Loop over each column, determine the current width from column default and/or user preferences.
		// Total up all of the widths. Width is meant to be a percentage, but this may add up to more or less
		// than 100.
		foreach ($columns as $column) {
			if ($column->getOption('display',true)==true) {
				$name = str_replace('.','_',$column->getName());
				
				$width = Preferences::get(__ZFEXT_PREFIX.'_Grid-ColumnWidths',"{$this->getName()}_{$name}",$column->getOption('width','0'));
				
				$width = preg_replace("/[^0-9,.]/", '', $width);
				
				if (floor($width) == 0) {
					$column->setOption('width',10);
					$width = 10; // default 10 "percent"... maybe make this dynamic, still feeling it out		
				}
				
				$totalWidth += $width;
			}
		}		
		unset($column);
		
		// Loop over each column, and set its actual width to a percentage based on the total widths
		// calculated in the first loop, so that the new sum total of all column widths ends up being
		// close to 100.
		foreach ($columns as $column) {	
			if ($column->getOption('display',true)==true) {		
				$name = str_replace('.','_',$column->getName());
				
				$width = Preferences::get(__ZFEXT_PREFIX.'_Grid-ColumnWidths',"{$this->getName()}_{$name}",$column->getOption('width','0'));
				$width = preg_replace("/[^0-9,.]/", '', $width);
				
				$calcWidth = number_format((($width / $totalWidth) * 100),2,'.','');
				
				Preferences::set(__ZFEXT_PREFIX.'_Grid-ColumnWidths',"{$this->getName()}_{$name}",$calcWidth);
			}
		}
				
	}
	
	public function export(Zend_View_Interface $view = null, $exportFormat = 'csv')
	{		
		$this->customize();
		$this->getAdapter()->setGrid($this);
		
		if ($view !== null) {
			$this->setView($view);
		}
	
		$this->getAdapter()->setFilters($this->translateFilters($this->getFilters()));
		$this->getAdapter()->setSorts($this->getSorts());
	
		$iterable = $this->getAdapter()->iterateBegin(0,ConfigUtils::get('grid','export_max_rows',10000));
		
		header('Content-type: text/csv');		
		header('Content-Disposition: attachment; filename="'.$this->getExportFilename().'.csv"');		
		
		$row = array('_iterable_reference_' => null);
		
		// headers
		////////////
		
		foreach ($this->getColumns() as $column) {
			if ($column->getOption('export',true)==true) {
				$captions[] = str_replace('"','\"',$column->getCaption());
			}
		}
		echo '"'.implode('","',$captions).'"'."\r\n";
		
		// data
		/////////
		while (($row = $this->getAdapter()->iterateNext($iterable, $row['_iterable_reference_'])) != null) {
			$formatted = array();
			foreach ($this->getColumns() as $column) {
				if ($column->getOption('export',true)==true) {
					$formatted[] = str_replace('"','\"',$column->renderValue($row));
				}
			}
			
			echo '"'.implode('","',$formatted).'"'."\r\n";
		}		
	}
	
	
	public function build() {
		// Stub, override this function to initialize columns.
	}
	
	/**
	 * sortByRank
	 * 
	 * Used by the customize() method to sort columns by rank.
	 * 
	 * @param unknown $a
	 * @param unknown $b
	 * @return number
	 */
	protected function sortByRank($a, $b) {
		if ($a->getOption('rank',0) == $b->getOption('rank',0)) return 0;
		if ($a->getOption('rank',0) < $b->getOption('rank',0)) return -1;
		if ($a->getOption('rank',0) > $b->getOption('rank',0)) return 1;	
	}
	
	/*
	 * customize
	 * 
	 * If this grid is customizable, attempts to load in customizations for the grid based on class
	 * name. Customizations include whether or not to display columns, export columns, and what order
	 * columns are rendered in. 
	 * 
	 */
	public function customize() {
		if ($this->getCustomizable()) {
			$customized = $this->getCustomizer()->getCustomization(get_class($this));
			
			if (is_object($customized) && EntityUtils::oneof($customized,$this->getCustomizer()->getPreferenceClassName())) {
				foreach ($customized->getColumns() as $customColumn) {
					$column = $this->getColumn($customColumn->getName());
					if ($column != null) {
						if ($column->isCustomizable()) {							
							$column->setOption('display',$customColumn->getDisplay());
							$column->setOption('export',$customColumn->getExport());
							$column->setOption('rank',$customColumn->getRank());
							$column->setCaption($customColumn->getCaption());
							
							$this->setColumn($customColumn->getName(),$column);
						}
					}
				}
			}
		}
		
		$columns = $this->getColumns();
		uasort($columns, array($this, 'sortByRank'));
		$this->setColumns($columns);
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
	
	/**
     * 
     * Takes a dot-delimited path and tries to go through mutators of an entity and its composite objects
     * to get the final value.
     * 
     * @param unknown_type $lStrSteps
     * @param unknown_type $Entity
     */
    public function getStringValueFromEntity($lStrSteps,$Entity) {
        if (is_array($Entity)) {
            if (isset($Entity[$lStrSteps])) {
                $lObjTarget = $Entity[$lStrSteps];
            } else $lObjTarget = '';
        } else {
            $lArySteps = explode('.',$lStrSteps);
            $lObjTarget = $Entity;
            foreach ($lArySteps as $lStrStep) {
                if (is_object($lObjTarget)) {
                    $lStrMethodName = 'get'.$lStrStep;
                    if (method_exists($lObjTarget,$lStrMethodName)) {
                        $lObjTarget = $lObjTarget->$lStrMethodName();
                    } else $lObjTarget = '';
                }
            }
            
            if ($lObjTarget instanceof DateTime) {
                $lObjTarget = $lObjTarget->format(ConfigUtils::get('global','phpDateFormat','Y-m-d'));
            } else 
            if (is_object($lObjTarget)) {
               $lObjTarget = '[object]';
            }
        }
        
        return $lObjTarget;
    }
    
    /**
     * 
     * Finds all values inside curly brackets and processes them.
     * 
     * @param unknown_type $lStrValue
     * @param unknown_type $Entity
     */
    public function processTokens($lStrValue,$row) {                
        $lAryMutators = array();
        
        $len = strlen($lStrValue);
        $tok = '';
        $bracketed = false;
        for ($i=0;$i<$len;$i++) {
            if (!$bracketed) {
                if ($lStrValue[$i] == '{') {
                    $bracketed = true;
                }    
            } else {
                if ($lStrValue[$i] == '}') {
                    $bracketed = false;
                    if (!empty($tok)) {
                        $lAryMutators[$tok] = true;
                        $tok = '';
                    }
                } else $tok .= $lStrValue[$i];
            }
        }
        if (!empty($tok)) $lAryMutators[$tok] = true;
        
        foreach (array_keys($lAryMutators) as $lStrMutator) {  
        	if (isset($row[$lStrMutator])) {         
            	$lStrValue = str_replace('{'.$lStrMutator.'}',$row[$lStrMutator],$lStrValue);
        	}            
        }
        
        return $lStrValue;
    }
    
    /**
     * handleRequest
     * 
     * Determines whether or not the current request was meant for this grid, e.g. from an ajax call.
     * The Snazzware CRUD controller's listingAction will call this method as a way of passing thru
     * ajax callbacks from the grid.
     * 
     * @param unknown $request
     * @param unknown $view
     * @return boolean
     */
    public function handleRequest($request,$view) {    	
    	$handled = false;
    	
    	if ($request->getParam(__ZFEXT_PREFIX."_grid_{$this->getName()}",0)==1) {    		
    		$filters = array();
    		$sorts = array();
    		$widths = array();      	
    		$export = '';	
    		foreach ($request->getParams() as $k=>$v) {
    			if ($k=='export') {
    				$export = $v;
    			} else
    			if ($k=='pagesize') { // set rows per page
    				$this->setPageSize($v);
    			} else
    			if ($k=='page') { // set page number
    				$this->setPage($v);	
    			} else
    			if (substr($k,0,9)=='colwidth_') { // set column width in percent     				
    				$name = substr($k,9,strlen($k)-9);
    				Preferences::set(__ZFEXT_PREFIX.'_Grid-ColumnWidths',"{$this->getName()}_{$name}",number_format($v, 3, '.', ''));
    				$widths[$name] = number_format($v, 2, '.', '');
    			} else
    			if (substr($k,0,7)=='filter_') { // filter column by value
    				if (strlen(trim($v))>0) {
    					$name = substr($k,7,strlen($k)-7);
    					
    					$name = str_replace('_','.',$name);
    					
    					$operator = 'like';
    					
    					$firstone = substr($v,0,1);
    					$firsttwo = substr($v,0,2);
    					
    					// Pull operator out of value, if any
    					if ($firstone=='=') {
    						$operator='=';
    						$v = substr($v,1);
    					} else
    					if ($firsttwo=='!=') {
    						$operator='!=';
    						$v = substr($v,2);
    					} else
    					if ($firsttwo=='>=') {
    						$operator='>=';
    						$v = substr($v,2);
    					} else
    					if ($firsttwo=='<=') {
    						$operator='<=';
    						$v = substr($v,2);
    					} else
    					if ($firstone=='>') {
    						$operator='>';
    						$v = substr($v,1);
    					} else
    					if ($firstone=='<') {
    						$operator='<';
    						$v = substr($v,1);
    					}    					
    					
    					$filter = array('field'=>$name,'op'=>$operator,'value'=>"{$v}");
    					$filters[$name] = $filter;
    				}
    			} else    		
    			if (substr($k,0,5)=='sort_') { // sort by column with direction
    				if (strlen(trim($v))>0) {
    					$name = substr($k,5,strlen($k)-5);
    					
    					$name = str_replace('_','.',$name);
    					
    					$sort = array('field'=>$name,'dir'=>"{$v}");
    					$sorts[$name] = $sort;
    				}
    			}    		
    		}    		
    		$this->setFilters($filters);
    		$this->setSorts($sorts);
    		if ($export != '') {
    			$this->export($view,$export);	
    		} else {
    			echo $this->render($view,false);
    		}
    		
    		$handled = true;
    	}
    	
    	// Check for column parameters
    	if (!$handled) {    		
	    	$columns = $this->getColumns();	 
	    	
	    	while ((!$handled) && (count($columns)>0)) {
	    		$column = array_shift($columns);
	    		
	    		if ($request->getParam(__ZFEXT_PREFIX."_grid_{$this->getName()}_{$column->getName()}",0)==1) {
	    			$handled = $column->handleRequest($request,$view);	    			
	    		}
	    	}
    	}
    	
    	return $handled;
    }
    
    /*
     * Returns filter array with key 'decorated' containing filter value with prepended operator. 
     */
    public function getDecoratedFilter($name) {
    	$result = $this->getFilter($name);
    	
    	if ($result !== null) {    	
    		$value = $result['value'];
    		
    		// Insert operator back in to value, if any
    		if (isset($result['op'])) {
    			if ($result['op']=='!=') {
    				$value = "!={$value}";
    			} else
    			if ($result['op']=='>=') {
    				$value = ">={$value}";
    			} else
    			if ($result['op']=='<=') {
    				$value = "<={$value}";
    			} else
    			if ($result['op']=='>') {
    				$value = ">{$value}";
    			} else
    			if ($result['op']=='<') {
    				$value = "<{$value}";
    			} else
    			if ($result['op']=='=') {
    				$value = "={$value}";
    			}
    		}
    		
    		$result['decorated'] = $value;
    	}
    	 
    	return $result;
    }
	
    protected function translateFilters($filters) {
    	foreach ($filters as &$filter) {
    		if (isset($this->options['columns'][$filter['field']])) {
    			$column = $this->options['columns'][$filter['field']];
    			$column->translateFilter($filter);
    		}
    	}
    	
    	return $filters;
    }
    
    public function getExportFilename() {
    	return $this->getCaption().' export '.date('Ymd His');
    }
    
}

?>