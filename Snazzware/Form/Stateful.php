<?php 

class Snazzware_Form_Stateful extends Snazzware_Form {
	
	private $statefulNamespace = '';
	private $statefulPrefix = '';
	
	public function populateFromState() {
		$defaults = array();
		foreach ($this->getElements() as $key => $element) {
			$value = State::get($this->getStatefulNamespace(),"{$this->getStatefulPrefix()}{$key}",null);
			if ($value != null) $defaults[$key] = $value;
		}
		$this->setDefaults($defaults);
	}
	
	public function saveToState() {
		foreach ($this->getElements() as $key => $element) {
			State::set($this->getStatefulNamespace(),"{$this->getStatefulPrefix()}{$key}",$element->getValue());			
		}
	}
	
	public function clearState() {
		$values = State::getNamespace($this->getStatefulNamespace());
		$cleaned = array();
		if ($this->getStatefulPrefix()!='') {
			foreach ($values as $k=>$v) {			
				if (strpos($k,$this->getStatefulPrefix())!==0) {
					$cleaned[$k] = $v;
				}			
			}
		}
		State::setNamespace($this->getStatefulNamespace(),$cleaned);
	}
	
 	public function getStatefulNamespace() {
    	if ($this->statefulNamespace == '') $this->statefulNamespace = get_class($this);
    	return $this->statefulNamespace;
    }
    
    public function setStatefulNamespace($statefulNamespace) {
    	$this->statefulNamespace = $statefulNamespace;
    }
    
    public function getStatefulPrefix() {
    	return $this->statefulPrefix;
    }
    
    public function setStatefulPrefix($statefulPrefix) {
    	$this->statefulPrefix = $statefulPrefix;
    }
	
}

?>