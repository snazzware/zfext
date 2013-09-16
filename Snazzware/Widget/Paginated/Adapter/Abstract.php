<?php 

class Snazzware_Widget_Paginated_Adapter_Abstract {
	
	public function getItems($offset, $max) {
		return array();
	}
	
	public function getCount() {
		return 0;
	}
	
}

?>