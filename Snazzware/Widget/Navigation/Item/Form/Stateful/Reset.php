<?php 

class Snazzware_Widget_Navigation_Item_Form_Stateful_Reset extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {	
		$form = $options['form'];
		
		$zfext = __ZFEXT_PREFIX;
		
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'reset',
			'caption'=>'Reset',
			'events'=>array(
				'click'=>array(
					'script'=>"
						function() {
							$('<input type=hidden name=\'{$zfext}-form-stateful-reset\' value=\'1\'>').appendTo('#{$form->getId()}');
							$('#{$form->getId()}').submit(); 
						}
					"
				)
			)
		),$options));	
	}
	
}

?>