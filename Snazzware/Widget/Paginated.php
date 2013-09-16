<?php 

class Snazzware_Widget_Paginated extends Snazzware_Widget {
		
	/*
	 * Options
	* -------
	* defaultSort			Array of field names and directiosn to use by default for sorting the paginated items
	* adapter				Descendant of Snazzware_Widget_Paginated_Adapter_Abstract, widget will call this for access to items
	* paginationControls	Array of pagination control widgets
	* controls				Array of which areas controls should appear, can contain 'top', 'bottom', or both.
	* stickyControls		If true, paginaton controls will "stick" at the top of the window when the user scrolls down
	*
	*/
	
	protected $helper = 'Widget_Paginated';
	protected $paginationHelper = 'Widget_Paginated_Pagination';
	protected $paginationControlsHelper = 'Widget_Paginated_Pagination_Controls';	
	protected $pageHelper = 'Widget_Paginated_Page';
	protected $emptyHelper = 'Widget_Paginated_Empty';
	protected $itemHelper = 'Widget_Paginated_Item';
	protected $defaultPageSize = 10;	
	protected $caption = '';		
	
	public function setCaption($value) { $this->caption = $value; }
	public function getCaption() { return $this->caption; }
	
	public function setPageSize($value) { if (is_numeric($value)) State::set(__ZFEXT_PREFIX.'_Paginated',$this->getName().'-pagesize',$value); }
	public function getPageSize() { return State::get(__ZFEXT_PREFIX.'_Paginated',$this->getName().'-pagesize',$this->defaultPageSize); }
	
	public function setPage($value) { if (is_numeric($value)) State::set(__ZFEXT_PREFIX.'_Paginated',$this->getName().'-page',$value); }
	public function getPage() { return State::get(__ZFEXT_PREFIX.'_Paginated',$this->getName().'-page',1); }
	
	public function setAdapter($value) { $this->setOption('adapter',$value); }
	public function getAdapter() { return $this->getOption('adapter',null); }
	
	public function getPaginationHelper() { return $this->paginationHelper; }
	public function getPaginationControlsHelper() { return $this->paginationControlsHelper; }	
	public function getPageHelper() { return $this->pageHelper; }
	public function getItemHelper() { return $this->itemHelper; }
	public function getEmptyHelper() { return $this->emptyHelper; }
	
	public function setFilters($value) { State::set(__ZFEXT_PREFIX.'_Widget_Paginated-Filters',$this->getName(),$value); }
	public function getFilters() { return State::get(__ZFEXT_PREFIX.'_Widget_Paginated-Filters',$this->getName(),array()); }
	public function getFilter($name) { $filters = $this->getFilters(); if (isset($filters[$name])) return $filters[$name]; else return null; }
	
	public function setDefaultFilters($value) { $this->setOption('defaultFilters',$value); }
	public function getDefaultFilters() { return $this->getOption('defaultFilters'); }
	
	public function setSorts($value) { Preferences::set(__ZFEXT_PREFIX.'_Widget_Paginated-Sorts',$this->getName(),$value); }
	public function getSorts() { return Preferences::get(__ZFEXT_PREFIX.'_Widget_Paginated-Sorts',$this->getName(),$this->getDefaultSort());  }
	public function getSort($name) { $sorts = $this->getSorts(); if (isset($sorts[$name])) return $sorts[$name]; else return null; }
	
	public function setDefaultSort($fieldname, $direction) { $this->setOption('defaultSort', array($fieldname=>array('field'=>$fieldname,'dir'=>$direction))); }
	public function getDefaultSort() { return $this->getOption('defaultSort'); }
	
	public function setPaginationControls($value) { $this->setOption('paginationControls',$value); }
	public function getPaginationControls() { return $this->getOption('paginationControls'); }
	
	public function __construct($options = array()) {			
		parent::__construct(array_merge_recursive_distinct(array(
			'defaultSort'=>'',
			'class'=>__ZFEXT_PREFIX.'-widget-paginated',
			'paginationControls'=>array(
				'firstPage'=>new Snazzware_Widget_Paginated_Pagination_Control(array(
					'name'=>'firstPage',
					'parent'=>$this,
					'caption'=>'First Page',
					'icon'=>ConfigUtils::get('pagination','icon_first_page'),
					'events'=>array(
						'click'=>array(
							'helper'=>'Widget_Paginated_Pagination_Control_Event_FirstPageClick'
						)
					)
				)),
				'prevPage'=>new Snazzware_Widget_Paginated_Pagination_Control(array(
					'name'=>'prevPage',
					'parent'=>$this,
					'caption'=>'Prev Page',
					'icon'=>ConfigUtils::get('pagination','icon_prev_page'),
					'events'=>array(
						'click'=>array(
							'helper'=>'Widget_Paginated_Pagination_Control_Event_PrevPageClick'
						)
					)
				)),
				'nextPage'=>new Snazzware_Widget_Paginated_Pagination_Control(array(
					'name'=>'nextPage',
					'parent'=>$this,
					'caption'=>'Next Page',
					'icon'=>ConfigUtils::get('pagination','icon_next_page'),
					'events'=>array(
						'click'=>array(
							'helper'=>'Widget_Paginated_Pagination_Control_Event_NextPageClick'
						)
					)
				)),
				'lastPage'=>new Snazzware_Widget_Paginated_Pagination_Control(array(
					'name'=>'lastPage',
					'parent'=>$this,
					'caption'=>'Last Page',
					'icon'=>ConfigUtils::get('pagination','icon_last_page'),
					'events'=>array(
						'click'=>array(
							'helper'=>'Widget_Paginated_Pagination_Control_Event_LastPageClick'
						)
					)					
				))
			)
		),$options));		
	}
	
	public function render(Zend_View_Interface $view = null)
	{
		$this->getAdapter()->setFilters($this->translateFilters($this->getFilters()));
		$this->getAdapter()->setSorts($this->getSorts());
	
		$itemcount = $this->getAdapter()->getCount();
	
		if (!is_numeric($itemcount)) $itemcount = 0;
	
		$pages = ceil($itemcount / $this->getPageSize());
	
		if ($this->getPage()>$pages) $this->setPage(1);
		$page = $this->getPage();
	
		$items = $this->getAdapter()->getItems(($this->getPage()-1)*$this->getPageSize(),$this->getPageSize());
	
		$itemstart = (($this->getPage()-1) * $this->getPageSize()) + 1;
		$itemend = $this->getPage() * $this->getPageSize();
	
		if ($itemend > $itemcount) $itemend = $itemcount;
	
		if ($page == $pages) $nextpage = $page;
		else $nextpage = $page+1;
	
		if ($page==1) $prevpage = $page;
		else $prevpage = $page-1;		
		
		$this->setOptions(array(
			'itemcount' => $itemcount,
			'items' => $items,
			'pagesize' => $this->getPageSize(),
			'pages' => $pages,
			'page' => $page,
			'itemstart' => $itemstart,
			'itemend' => $itemend,
			'nextpage' => $nextpage,
			'prevpage' => $prevpage
		));		
			
		return parent::render($view);
	}
	
	public function refreshAction() {		
		foreach ($this->getRequest()->getParams() as $k=>$v) {
			if ($k=='pagesize') { // set rows per page
				$this->setPageSize($v);				
			} else
				if ($k=='page') { // set page number
				$this->setPage($v);				
			}
		}
		
		$this->setOption('renderContainer',false);		
		$this->setRenderPending(true);		
	}
	
	public function translateFilters($filters) {
		// stub
		return $filters;
	}
}