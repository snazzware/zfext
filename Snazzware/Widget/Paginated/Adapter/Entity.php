<?php 

class Snazzware_Widget_Paginated_Adapter_Entity extends Snazzware_Widget_Paginated_Adapter_Abstract {
	
	private $model = null;
	private $widget = null;
	
	private $filters = array();
	private $sorts = array();
	private $filterLogicalOperator = 'and';
	
	public function getFilterLogicalOperator() { return $this->filterLogicalOperator; }
	public function setFilterLogicalOperator($value) { $this->filterLogicalOperator = $value; }
	
	public function setFilters($value) { $this->filters = $value; }
	public function getFilters() { return $this->filters; }
	
	public function setSorts($value) { $this->sorts = $value; }
	public function getSorts() { return $this->sorts; }
	
	public function setModel(Snazzware_Model_Entity $value) { $this->model = $value; }
	public function getModel() { return $this->model; }	
	
	public function __construct($model) {
		$this->setModel($model);
	}
	
	public function getCompleteFilters() {
		// override this method to include any forced filters.
		return $this->getFilters();
	}
	
	public function getCompleteSorts() {
		// override this method to include any forced sorts.
		//return array(array('field'=>'lastname','dir'=>'ASC'));
		return $this->getSorts();
	}
	
	public function getItems($offset, $max) {
		$this->getModel()->setFilterLogicalOperator($this->getFilterLogicalOperator());
		return $this->getModel()->getItems($offset, $max, $this->getCompleteFilters(), $this->getCompleteSorts());		
	}
	
	public function getCount() {
		$this->getModel()->setFilterLogicalOperator($this->getFilterLogicalOperator());
		return $this->getModel()->getCount($this->getCompleteFilters());
	}
	
	public function iterateBegin($offset, $max) {
		$this->getModel()->setFilterLogicalOperator($this->getFilterLogicalOperator());
		return $this->getModel()->iterateBegin($offset, $max, $this->getCompleteFilters(), $this->getCompleteSorts());
	}
	
	public function iterateNext($iterable, $previous = null) {
		return $this->getModel()->iterateNext($iterable, $previous);
	}
	
}

?>