<?php 

class Snazzware_Widget_Paginated_Pagination_Control extends Snazzware_Widget {
	
	protected $helper = 'Widget_Paginated_Pagination_Control';	
	
	public function __construct($options = array()) {
		parent::__construct($options);
	
		$this->removeDecorator('Container');
	}
	
}