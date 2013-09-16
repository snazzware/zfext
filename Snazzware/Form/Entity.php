<?php 
/**
 * Snazzware Extensions for the Zend Framework 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.snazzware.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to josh@snazzware.com so we can send you a copy immediately.
 *
 * @category   Snazzware
 * @copyright  Copyright (c) 2011-2012 Josh M. McKee
 * @license    http://www.snazzware.com/license/new-bsd     New BSD License
 */

class Snazzware_Form_Entity extends Snazzware_Form {
	
	/**
	 * Override this method to build the form based on current entity, if any.
	 * 
	 * @param unknown_type $entity
	 */
	public function build($entity=null) {
		// 
	}	
	
	// TODO : Allow specification of composite members e.g. this.address.city
	public function populateFromEntity($entity) {
		$defaults = array();
		foreach ($this->getElements() as $key => $element) {
			$getter = 'get'.ucfirst($key);
			if (is_object($entity)) {					
				$value = $entity->$getter();
				
				if ($value !== null) {
					if ($value instanceof Doctrine\Common\Collections\Collection) {
						$value = $this->doctrineArrayCollectionToArray($element,$value);	
					}
					
					if ($element instanceof Snazzware_Form_Element) {
						if ($element->getPropertyAdapter() != null) $value = $element->getPropertyAdapter()->toString($value);
					}
					
					$defaults[$key] = $value;					
				}
			}
		}		
		$this->setDefaults($defaults);
	}
	
	// TODO : Allow specification of composite members e.g. this.address.city
	public function saveToEntity($entity) {
		foreach ($this->getElements() as $key => $element) {
			if (!$element->getReadOnly()) {
				$callSetter = true;			
				$value = $element->getValue();
				
				$getter = 'get'.ucfirst($key);					
				$origvalue = $entity->$getter();
				
				if ($origvalue != null) {
					if ($origvalue instanceof Doctrine\Common\Collections\Collection) {					
						$value = $this->saveToDoctrineArrayCollection($element, $entity, $origvalue, $value);
						$callSetter = false;
					} 
				}
				
				if ($callSetter) {
					$setter = 'set'.ucfirst($key);
					
					if ($element instanceof Snazzware_Form_Element) {
						if ($element->getPropertyAdapter() != null) $value = $element->getPropertyAdapter()->fromString($value);
					}
										
					$entity->$setter($value);					
				}
			}
		}
	}

	// TODO : refactor this in to a strategy
	// TODO : interface for collection-managing elements incl getAddMethod, getRemoveMethod, etc.
	protected function saveToDoctrineArrayCollection($element, $entity, $collection, $value) {				
		$doctrineContainer = Zend_Registry::get('doctrine');	
	    $em = $doctrineContainer->getEntityManager();
	    $cmf = $em->getMetadataFactory();
	    
	    $addMethod = $element->getAddMethod();
	    $deleteMethod = $element->getDeleteMethod();
	    $excludeKeys = $element->getExcludeKeys();
	    
	    $deleteColumnName = $element->getDeleteColumnName();
	    
	    $classname = $element->getEntityClass();
	    if ($classname != null) {
		    $class = $cmf->getMetadataFor($classname);
		    $id = null;
		    // TODO : don't assume single-identifiers, even though i don't plan on using composite
		    foreach ($class->fieldMappings as $fieldMapping) {	    	
				if (isset($fieldMapping['id']) && ($fieldMapping['id'])==1) $id = $fieldMapping['fieldName'];
		    }
		    
		    $deleteList = array();
		    
		   	if ($id != null) {
		   		$idgetter = 'get'.ucfirst($id);			   		
		   		
				foreach ($value as $key=>$row) {					
					if (!in_array($key,$excludeKeys,true)) {
						$match = false;					
						if (strlen(trim($row[$id]))>0) {
							foreach ($collection as $childentity) {									
								if ($childentity->$idgetter() == $row[$id]) {
									$match = true;
									if (isset($row[$deleteColumnName]) && ($row[$deleteColumnName]=='delete')) {
										$deleteList[] = $childentity;
									} else {										
										foreach ($row as $k=>$v) {										
											if ($k != $id) {
												$setter = 'set'.ucfirst($k);
												
												$column = $element->getColumn($k);
												if (($column != null) && ($column->getPropertyAdapter() != null)) $v = $column->getPropertyAdapter()->fromString($v);
												
												$childentity->$setter($v);
											}
										}						
									}
								}	
							}
						}
		
						if (!$match && $addMethod != null) {						
							if (!(isset($row[$deleteColumnName]) && ($row[$deleteColumnName]=='delete'))) {
								$newentity = new $classname();
								$entity->$addMethod($newentity);
								foreach ($row as $k=>$v) {
									if ($k != $id) {
										$setter = 'set'.ucfirst($k);
										// TODO : recursively call to handle values by type...
										$column = $element->getColumn($k);
										if (($column != null) && ($column->getPropertyAdapter() != null)) $v = $column->getPropertyAdapter()->fromString($v);
						
										$newentity->$setter($v);
									}
								}																
							}
						}
					}						
				}				
				
				foreach ($deleteList as $deleted) {
					$entity->$deleteMethod($deleted);
				}
		   	}
	    }
	}
	
	// TODO : refactor this in to a strategy
	protected function doctrineArrayCollectionToArray($element, Doctrine\Common\Collections\Collection $value) {		
		$result = array();
		
		$doctrineContainer = Zend_Registry::get('doctrine');	
	    $em = $doctrineContainer->getEntityManager();
	    $cmf = $em->getMetadataFactory();	    
	    
		$i = 0;
		foreach ($value as $entity) {
			/*$class = $cmf->getMetadataFor(get_class($entity));
			$row = array();
			$id = null;
			foreach ($class->fieldMappings as $fieldMapping) {				
				$getter = 'get'.ucfirst($fieldMapping['fieldName']);					
				$value = $entity->$getter();
				
				$column = $element->getColumn($fieldMapping['fieldName']);
				if (($column != null) && ($column->getPropertyAdapter() != null)) $value = $column->getPropertyAdapter()->toString($value);
				
				// TODO : recursive call to take care of various value types by strategy, so we can have nested collections
				$row[$fieldMapping['fieldName']] = $value;
				if (isset($fieldMapping['id']) && ($fieldMapping['id'])==1) $id = $value;
			}
			*/
			
			$row = array();
			foreach ($element->getColumns() as $colname=>$column) {
				$getter = 'get'.ucfirst($colname);
				$value = $entity->$getter();				
				if ($column->getPropertyAdapter() != null) $value = $column->getPropertyAdapter()->toString($value);				
				$row[$colname] = $value;
			}
			
			$result[$i++] = $row;			
		}
		
		return $result;
	}
}
