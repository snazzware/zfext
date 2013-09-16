<?php 

class Snazzware_Widget_Navigation_Item_Crud_New extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {		
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'new',
			'caption'=>'New'
		),$options));
	}
	
}

?>