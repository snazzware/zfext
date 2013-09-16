<?php 

abstract class Snazzware_Widget_Decorator_Abstract {
	
	protected $options;
	
	public function __construct($options = array()) {		
		$this->options = $options;
	}
	
	public function setOption($option, $value) {
		$this->options[$option] = $value;
	}
	
	public function setOptions($options, $merge = true) {
		if ($merge) {
			$this->options = array_merge_recursive_distinct($this->options, $options);
		} else {
			$this->options = $options;
		}
	}
	
	public function getOption($option, $default=null) {
		if (isset($this->options[$option])) {
			return $this->options[$option];
		} else return $default;
	}
	
	public function getOptions() {
		return $this->options;
	}
	
	public function render($view,$widget,&$content) {
		// STUB
	}
	
}

?>