<?php 

class Snazzware_Widget_Paginated_Adapter_EntityToArray extends Snazzware_Widget_Paginated_Adapter_Entity {
	
	private $keys = array();
	
	public function setKeys($value) { $this->keys = $value; }
	public function getKeys() { return $this->keys; }
	
	public function getItems($offset, $max) {				
		$entities = parent::getItems($offset, $max); 

		$rows = array();
		
		foreach ($entities as $entity) {
			$row = array();
				
			foreach ($this->getKeys() as $key) {
				$tokens = explode('.',$key);
				$value = $entity;
		
				foreach ($tokens as $token) {
					$getter = 'get'.ucfirst($token);
					if (is_object($value)) {
						$value = $value->$getter();
					}
				}
		
				if (is_object($value)) {
					$value = $value->getId();
				}
		
				$row[$key] = $value;
			}
				
			$rows[] = $row;
		}
		return $rows;		
	}
	
	public function iterateNext($iterable, $previous = null) {
		$entity = parent::iternateNext($iterable, $previous);
	
		if ($entity != null) {
			$row = array();
		
			$row ['_iterable_reference_'] = $entity;
				
			foreach ($this->getKeys() as $key) {
				$tokens = explode('.',$key);
				$value = $entity;
					
				foreach ($tokens as $token) {
					$getter = 'get'.ucfirst($token);
					if (is_object($value)) {
						$value = $value->$getter();
					}
				}
					
				if (is_object($value)) {
					$value = $value->getId();
				}
					
				$row[$key] = $value;
			}
		
			return $row;
		} else return null;	
	}
	
}

?>