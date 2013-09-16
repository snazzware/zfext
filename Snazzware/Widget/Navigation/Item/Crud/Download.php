<?php 

class Snazzware_Widget_Navigation_Item_Crud_Download extends Snazzware_Widget_Navigation_Item {
	
	public function __construct($options = array()) {
		
		$form = $options['form'];
		
		$zfext = __ZFEXT_PREFIX;
		
		if (!$form->getReadOnly()) {
			$script = "
				if ({$zfext}_globalFormIsDirty) {
					{$zfext}_globalFormIsDirty = false;
					$('<input type=hidden id=\'{$zfext}-crud-postload-redirect\' name=\'{$zfext}-crud-postload-redirect\' value=\'{$options['downloadAction']}?id={id}\'>').appendTo('#crudForm');
					$('<input type=hidden id=\'{$zfext}-crud-postpersist-redirect\' name=\'{$zfext}-crud-postpersist-redirect\' value=\'{$options['detailsAction']}?id={id}\'>').appendTo('#crudForm');
					$('#{$form->getId()}').submit();
				} else {
					document.location = '{$options['downloadUrl']}';
				}
			";
		} else {
			$script = "
				document.location = '{$options['downloadUrl']}';
			";
		}
		
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'download',
			'caption'=>'Download',
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