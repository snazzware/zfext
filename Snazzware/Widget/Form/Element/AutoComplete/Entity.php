<?php 

class Snazzware_Widget_Form_Element_AutoComplete_Entity extends Snazzware_Widget_Form_Element_AutoComplete {
		
	public function searchAction() {		
		$results = array();
		
		$adapter = $this->getOption('adapter',null);
		
		if ($adapter !== null) {
			// Get and trim search term 
			$term = trim($this->getRequest()->getParam('term',''));
			
			// Get options
			$labelFormat = $this->getOption('labelFormat','');
			$valueFormat = $this->getOption('valueFormat','');
			$valueProperty = $this->getOption('valueProperty','');			
			$searchProperties = $this->getOption('search','');
			
			// Build up a list of all identifiers that we are interested in
			$identifiers = array_filter(array_unique(array_merge(
				$searchProperties,
				getIdentifiers($labelFormat), 
				getIdentifiers($valueFormat), 
				array($valueProperty)
			)));
			
			// Build filters based on search properties
			$filters = array();
			foreach ($searchProperties as $searchProperty) {
				$filters[$searchProperty] = array(
					'field'=>$searchProperty,
					'op'=>'like',
					'value'=>$term
				);
			}
			
			// Configure adapter
			$adapter->setFilterLogicalOperator('or');
			$adapter->setFilters($filters);			
			$adapter->setKeys($identifiers);
			
			// Get matches from adapter
			$matches = $adapter->getItems(0,$this->getOption('maximumResults',10));
			
			// Format matches in to results
			foreach ($matches as $match) {				
				$results[] = array_merge(array(
					'label'=>replaceIdentifiers($labelFormat,$match),
					'value'=>replaceIdentifiers($valueFormat,$match),
				),$match);
			}		
		}
		
		return json_encode($results);
	}
	
	public function getDisplayValue() {			
		$value = trim($this->getOption('value',$this->getOption('defaultValue','')));
		
		$valueProperty = $this->getOption('valueProperty','value');
		
		if ((!empty($value)) && ($valueProperty != 'value')) {
			$adapter = $this->getOption('adapter',null);
			if ($adapter !== null) {
				// Get options			
				$valueFormat = $this->getOption('valueFormat','');						
				
				$filters[$valueProperty] = array(
					'field'=>$valueProperty,
					'op'=>'=',
					'value'=>$value
				);
				
				// Configure adapter
				$adapter->setFilters($filters);
				$adapter->setKeys(getIdentifiers($valueFormat));
				
				// Get match
				$matches = $adapter->getItems(0,1);
				
				if (is_array($matches) && is_array(reset($matches))) {
					$value = replaceIdentifiers($valueFormat,reset($matches));	
				}
				
			}
		}
		
		return $value;
	}
	
	
	
}

?>