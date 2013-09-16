<?php 

class Snazzware_Widget_Navigation_Item_Crud_History extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'history',
			'caption'=>'History',
			'events'=>array(
				'click'=>array(
					'script'=>"
						function() {
							window.open('{$options['historyUrl']}');
						}
					"
				)
			)
		),$options));
	}
	
}

?>