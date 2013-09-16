<?php 

class Snazzware_Widget_Navigation_Item_Crud_Save extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {
		$form = $options['form'];
	
		$zfext = __ZFEXT_PREFIX;
		
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'save',
			'caption'=>'Save',
			'events'=>array(
				'click'=>array(
					'script'=>"function() {
						{$zfext}_globalFormIsDirty = false; 
						$('#{$form->getId()}').submit();
					}"
				)
			)
		),$options));
	}
	
}

?>