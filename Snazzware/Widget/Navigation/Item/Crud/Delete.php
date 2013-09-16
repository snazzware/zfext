<?php 

class Snazzware_Widget_Navigation_Item_Crud_Delete extends Snazzware_Widget_Navigation_Item {

	public function __construct($options = array()) {
		$form = $options['form'];
	
		$zfext = __ZFEXT_PREFIX;
	
		parent::__construct(array_merge_recursive_distinct(array(
			'name'=>'delete',
			'caption'=>'Delete',
			'events'=>array(
				'click'=>array(
					'script'=>"
						function() {
							if (confirm('Are you certain you want to DELETE this record?')) {
								window.location.href = '{$options['deleteUrl']}'
							}
						}
					"
				)
			)
		),$options));
	}
	
}

?>