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
class Snazzware_Widget_Paginated_Grid extends Snazzware_Widget_Paginated {
	
	const PAGINATED_GRID_COLUMN = 'PAGINATED_GRID_COLUMN';
	
	protected $loaders = array();	
	protected $callbackUrl = '';
	protected $ajaxGetUrl = '';
	protected $columns = null;		
	protected $defaultFilters = true;
	protected $pageHelper = 'Widget_Paginated_Grid_Page';
	protected $itemHelper = 'Widget_Paginated_Grid_Item';
	protected $popup = false;		
	protected $defaultSort = array();	
	protected $highlightedRows = array();	
	protected $customizer = null;	
	protected $hasBeenBuilt = false;
	
	function __construct($options = array()) {
		parent::__construct(array_merge_recursive_distinct(array(
			'caption'=>'',
			'class'=>__ZFEXT_PREFIX.'-widget-paginated-grid',
			'customizeUrl'=>'',
			'paginationControls'=>array(
				'resetFilters'=>new Snazzware_Widget_Paginated_Pagination_Control(array(
					'name'=>'resetFilters',
					'parent'=>$this,
					'caption'=>'Reset',
					'placement'=>'right',
					'icon'=>ConfigUtils::get('pagination','icon_reset_filters',''),
					'iconPlacement'=>'left',
					'events'=>array(
						'click'=>array(
							'helper'=>'Widget_Paginated_Grid_Control_Event_ResetFiltersClick'
						)
					)
				)),
				'applyFilters'=>new Snazzware_Widget_Paginated_Pagination_Control(array(
					'name'=>'applyFilters',
					'parent'=>$this,
					'caption'=>'Apply',
					'placement'=>'right',
					'icon'=>ConfigUtils::get('pagination','icon_apply_filters',''),
					'iconPlacement'=>'left',
					'events'=>array(
						'click'=>array(
							'helper'=>'Widget_Paginated_Pagination_Control_Event_PageChangeClick'
						)
					)
				)),
				'customize'=>new Snazzware_Widget_Paginated_Pagination_Control(array(
					'name'=>'customize',
					'parent'=>$this,
					'caption'=>'Customize',
					'placement'=>'right',
					'icon'=>ConfigUtils::get('pagination','icon_customize',''),
					'iconPlacement'=>'left',
					'events'=>array(
						'click'=>array(
							'helper'=>'Widget_Paginated_Grid_Control_Event_CustomizeClick'
						)
					)
				)),				
				'export'=>new Snazzware_Widget_Paginated_Pagination_Control(array(
					'name'=>'export',
					'parent'=>$this,
					'caption'=>'Export',
					'placement'=>'right',
					'icon'=>ConfigUtils::get('pagination','icon_export',''),
					'iconPlacement'=>'left',
					'events'=>array(
						'click'=>array(
							'helper'=>'Widget_Paginated_Grid_Control_Event_ExportClick'
						)
					)
				))
			)
		),$options));	

		self::getFactory()->addType(self::PAGINATED_GRID_COLUMN,array(
			'prefixSegment' => 'Grid_Column',
			'pathSegment' => 'Grid/Column'
		));
		
		self::getFactory()->addPrefixPath('Snazzware_Widget_Paginated_Grid_Column', 'Snazzware/Widget/Paginated/Grid/Column/', self::PAGINATED_GRID_COLUMN);		
	}
	
	public function setCaption($value) { $this->setOption('caption',$value); }
	public function getCaption() { return $this->getOption('caption'); }
	
	public function setCustomizable($value) { $this->setOption('customizable',$value); }
	public function getCustomizable() { return $this->getOption('customizable'); }
	
	public function setExportable($value) { $this->setOption('exportable',$value); }
	public function getExportable() { return $this->getOption('exportable'); }
	
	public function setCustomizeUrl($value) { $this->setOption('customizeUrl',$value); }
	public function getCustomizeUrl() { return $this->getOption('customizeUrl'); }
	
	public function setCustomizerClassName($value) { $this->setOption('customizerClassName',$value); }
	public function getCustomizerClassName() { return $this->getOption('customizerClassName','Snazzware_Widget_Paginated_Grid_Customizer'); }
	
	public function setViewScript($value) { $this->setOption('viewScript',$value); }
	public function getViewScript() { return $this->getOption('viewScript','Grid/default.phtml'); }
	
	public function setAutoResize($value) { $this->setOption('autoResize',$value); }
	public function getAutoResize() { return $this->getOption('autoResize'); }
	
	public function setMouseWheel($value) { $this->setOption('mouseWheel',$value); }
	public function getMouseWheel() { return $this->getOption('mouseWheel'); }
	
	public function setOnClick($value) { $this->setOption('onClick',$value); }
	public function getOnClick() { return $this->getOption('onClick'); }
	
	public function setOnClickUrl($value) { $this->setOption('onClickUrl',$value); }
	public function getOnClickUrl() { return $this->getOption('onClickUrl'); }
	
	public function getCustomizer() {
		if ($this->customizer == null) {
			$classname = $this->getCustomizerClassName();			
			$this->customizer = new $classname();
		}
		return $this->customizer;
	}
	
	public function setCustomizer($value) {
		$this->customizer = $value;
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
		if (!isset($this->highlightedRows[$column])) $this->highlightedRows[$column] = array();
		$this->highlightedRows[$column][$value] = $class;
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
		
		foreach ($this->highlightedRows as $column=>$values) {
			if (isset($values[$row[$column]])) $result = $values[$row[$column]];
		}
		
		return $result;
	}
	
	// Columns
	
	public function getColumns() { if (!isset($this->options['columns'])) $this->options['columns'] = array(); return $this->options['columns']; }
	
	public function createColumn($type, $name, $options = array()) {
		$class = $this->getFactory()->getPluginLoader(self::PAGINATED_GRID_COLUMN)->load($type);		
		return new $class($name, $options);
	}
	
	public function addColumn(Snazzware_Widget_Paginated_Grid_Column $column) { 
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
	
	public function setCallbackUrl($value) { $this->callbackUrl = $value; }
	public function getCallbackUrl() {
		if ($this->callbackUrl=='') {
			$url = 'window.location.pathname';
		} else {
			$url = "'{$this->callbackUrl}'";
		} 
		return $url; 
	}
	
	public function setAjaxGetUrl($value) { $this->ajaxGetUrl = $value; }
	public function getAjaxGetUrl() { return $this->ajaxGetUrl; }
	
	
	
	

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
	
	public function render(Zend_View_Interface $view = null)
    {
    	$this->doBuild();
    	
    	// Overlay any customizations for the current user
    	$this->customize();
        
    	// Adjust column widths
    	$this->fixColumnWidths();
    	
    	// Build up list of keys that we are interested in
    	$keys = array();
    	foreach ($this->getColumns() as $column) {
    		$keys[] = $column->getName();
    	}
    	unset($column);
    	$this->getAdapter()->setKeys($keys);
    	
    	// inherited render
    	return parent::render($view);
    	
        /*$this->getAdapter()->setFilters($this->translateFilters($this->getFilters()));
        $this->getAdapter()->setSorts($this->getSorts());
        
        // Obtain total number of rows
        $rowcount = $this->getAdapter()->getCount();
        
        // Calculate total number of pages
        $pages = ceil($rowcount / $this->getPageSize());
        
        // If the current page exceeds the total number of pages, set current page to 1.
        if ($this->getPage()>$pages) $this->setPage(1);
        $page = $this->getPage();

        // Obtain rows for current page
        $rows = $this->getAdapter()->getItems(($this->getPage()-1)*$this->getPageSize(),$this->getPageSize());
        
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
        if (isset($view->grid)) $oldgrid = $view->grid;
        else unset($oldgrid);
        $view->grid = $this;
        
        if (isset($view->vars)) $oldvars = $view->vars;
        else unset($oldvars);
        $view->vars = $vars;
        
        $xhtml = $view->render($this->getViewScript());       
        
        if (isset($oldgrid)) $view->grid = $oldgrid;
        if (isset($oldvars)) $view->vars = $oldvars;
        
		return $xhtml;*/
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
		$this->doBuild();
		
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
	
	public function doBuild() {
		if (!$this->hasBeenBuilt) {
			$this->hasBeenBuilt = true;
			$this->build();			
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
    
    public function columnresizeAction() {
    	$widths = array();
    	foreach ($this->getRequest()->getParams() as $k=>$v) {    		
    		if (substr($k,0,9)=='colwidth_') { // set column width in percent
    			$name = substr($k,9,strlen($k)-9);
    			Preferences::set(__ZFEXT_PREFIX.'_Grid-ColumnWidths',"{$this->getName()}_{$name}",number_format($v, 3, '.', ''));
    			$widths[$name] = number_format($v, 2, '.', '');
    		}
    	}
    }
    
    public function columnorderAction() {
    	$columns = explode(',',$this->getRequest()->getParam('order',''));
    	
    	$rank = 1;
    	$ranks = array();
    	foreach ($columns as $column) {
    		$ranks[$column] = $rank++;
    	}
    	
    	$this->getCustomizer()->setColumnOrder(get_class($this),$ranks);
    }
    
    public function refreshAction() {
    	parent::refreshAction();
    	
    	$filters = array();
    	$sorts = array();    	
    	$export = '';
    	
    	foreach ($this->getRequest()->getParams() as $k=>$v) {    		
    		if ($k=='export') {
    			$export = $v;
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
    	
    	// check for flag to reset all filters
    	if ($this->getRequest()->getParam('resetFilters',0)==1) {    	
    		$filters = array();
    	}
    	
    	// apply filters and sorts
    	$this->setFilters($filters);
    	$this->setSorts($sorts);
    	
    	// export if set
    	if ($export != '') {
    		$this->export($view,$export);
    		$this->setRenderPending(false);
    	}
    }
    
    public function handleRequest($request,$view) {
    	$this->doBuild();
    	return parent::handleRequest($request,$view);
    	
    	/*$handled = false;
    	
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
    	
    	return $handled;*/
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
	
    public function translateFilters($filters) {
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