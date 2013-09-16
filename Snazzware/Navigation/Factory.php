<?php 

class Snazzware_Navigation_Factory extends Snazzware_Factory {
	
	const stereotype_navigation = 'navigation';
	const stereotype_new = 'new';
	const stereotype_save = 'save';
	const stereotype_delete = 'delete';
	const stereotype_print = 'print';
	const stereotype_history = 'history';
	const stereotype_download = 'download';
	const stereotype_back = 'back';
	
	public static function getStereotypes() {		
		if (self::$stereotypes == null) self::$stereotypes = array('default'=>'Snazzware_Navigation_Item');
		return self::$stereotypes;
	}
	
}

?>