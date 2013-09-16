<?php 

class Snazzware_Widget_Navigation_Item_Form_Submit extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {	
		$form = $options['form'];
		
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'submit',
			'caption'=>'Submit',
			'events'=>array(
				'click'=>array(
					'script'=>"function() { $('#{$form->getId()}').submit(); }"
				)
			)
		),$options));	
	}
	
}

?>