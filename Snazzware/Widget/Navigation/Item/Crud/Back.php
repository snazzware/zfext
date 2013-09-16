<?php 

class Snazzware_Widget_Navigation_Item_Crud_Back extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {	
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'back',
			'caption'=>'Back'			
		),$options));
	}
	
}

?>