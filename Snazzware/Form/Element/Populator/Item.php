<?php 

class Snazzware_Form_Element_Populator_Item {
	
	protected $mName;		
	protected $mOptions = array();
	
	public function __construct($name, $options = null) {
		$this->setName($name);
		$this->setOptions($options);
	}
	
	public function setName($value) { $this->mName = $value; }
	public function getName() { return $this->mName; }
	
	public function setOptions($value) { $this->mOptions = $value; }
	public function getOptions() { return $this->mOptions; }
	
	public function renderScripting($view = null, $options = array()) {		
		$script = '';
		
		$options = array_merge($this->getOptions(), $options);
		
		if (isset($options['source'])) {
			if (is_array($options['source'])) {
				if (isset($options['delimiter'])) $delimiter = $options['delimiter'];
				else $delimiter = ' ';
				
				$sources = array();
				foreach ($options['source'] as $source) {
					$sources[] = "entity.{$source}";
				}
				
				$value = implode("+' '+",$sources);
	
				$script = "$('#{$this->getName()}').val({$value});\r\n";
			} else {
				$script = "$('#{$this->getName()}').val(entity.{$options['source']});\r\n";
			}
		} else {
			if (isset($options['value'])) {
				$script = "$('#{$this->getName()}').val('{$options['value']}');\r\n";
			}
		}
		
		return $script;
	} 
}

?>