<?php 

class Snazzware_Widget_Navigation_Item_Crud_PrintListing extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'printlisting',
			'caption'=>'Print',
			'events'=>array(
				'click'=>array(
					'script'=>"
						function() {
							window.open('{$options['printListingUrl']}');
						}					
					"
				)
			)
		),$options));
	}
	
}

?>