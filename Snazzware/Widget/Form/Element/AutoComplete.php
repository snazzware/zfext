<?php 

class Snazzware_Widget_Form_Element_AutoComplete extends Snazzware_Widget {
	
	protected $helper = 'Widget_Form_Element_AutoComplete';
	
	protected $values = array();
	
	public function __construct($options) {
		parent::__construct($options);
		
		$values = $this->getOption('values',array());
		foreach ($values as $key=>$value) {
			if (is_array($value)) {
				$this->values[] = $value;
			} else {
				$this->values[] = array('value'=>$value);
			}
		}
	}
	
	public function searchAction() {
		$term = strtolower($this->getRequest()->getParam('term',''));
		
		$results = array();
		
		if ($term != '') {
			foreach ($this->values as $value) {
				if (strpos(strtolower($value['value']),$term)!==false) $results[] = $value;
			}		
		}
		
		return json_encode($results);
	}
	
	public function getDisplayValue() {
		$result = '';
		
		$value = $this->getOption('value',$this->getOption('defaultValue'));
		
		$valueAttribute = $this->getOption('valueAttribute','value');
		
		$values = $this->values;
		
		if ($valueAttribute=='value') $result = $value;
		else {
			$current = reset($values);
			while (($current !== false) && (isset($current[$valueAttribute])) && ($current[$valueAttribute] != $value)) $current = next($values);
			if ($current !== false) $result = $current['value'];			
		}

		return $result;
	}
	
}

?>