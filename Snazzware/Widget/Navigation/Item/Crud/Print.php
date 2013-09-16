<?php 

class Snazzware_Widget_Navigation_Item_Crud_Print extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {
	
		$form = $options['form'];
	
		$zfext = __ZFEXT_PREFIX;
	
		if (!$form->getReadOnly()) {
			$script = "
				if ({$zfext}_globalFormIsDirty) {
					{$zfext}_globalFormIsDirty = false;
					$('<input type=hidden id=\'{$zfext}-crud-postload-redirect\' name=\'{$zfext}-crud-postload-redirect\' value=\'{$options['printAction']}?id={id}\'>').appendTo('#crudForm');
					$('<input type=hidden id=\'{$zfext}-crud-postpersist-redirect\' name=\'{$zfext}-crud-postpersist-redirect\' value=\'{$options['detailsAction']}?id={id}\'>').appendTo('#crudForm');
					$('<input type=hidden id=\'{$zfext}-crud-postload-redirect-target\' name=\'{$zfext}-crud-postload-redirect-target\' value=\'_blank\'>').appendTo('#crudForm');
					$('#{$form->getId()}').submit();
				} else {
					window.open('{$options['printUrl']}','_blank');
				}
			";
		} else {
			$script = "
				window.open('{$options['printUrl']}','_blank');
			";
		}
	
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'print',
			'caption'=>'Print',
			'events'=>array(
				'click'=>array(
					'script'=>"function() {
						$script
					}"
				)
			)
		),$options));
	}
	
}

?>