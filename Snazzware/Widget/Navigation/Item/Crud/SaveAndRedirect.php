<?php 

class Snazzware_Widget_Navigation_Item_Crud_SaveAndRedirect extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {
		$form = $options['form'];
		$redirectUrl = $options['redirectUrl'];
	
		$zfext = __ZFEXT_PREFIX;
		
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'save',
			'caption'=>'Save',
			'events'=>array(
				'click'=>array(
					'script'=>"function() {
						{$zfext}_globalFormIsDirty = false;
	    				$('<input type=hidden name=\'snazzware-crud-postpersist-redirect\' value=\'{$redirectUrl}\'>').appendTo('#{$form->getId()}');
	    				$('#{$form->getId()}').submit();
					}"
				)
			)
		),$options));
	}
	
}

?>