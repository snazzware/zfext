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

class Snazzware_Grid_Adapter_Entity extends Snazzware_Grid_Adapter_Abstract {
	
	private $mModel = null;
	private $mGrid = null;
	
	private $mFilters = array();
	private $mSorts = array();
	
	public function setFilters($value) { $this->mFilters = $value; }
	public function getFilters() { return $this->mFilters; }
	
	public function setSorts($value) { $this->mSorts = $value; }
	public function getSorts() { return $this->mSorts; }
	
	public function setModel(Snazzware_Model_Entity $value) { $this->mModel = $value; }
	public function getModel() { return $this->mModel; }
	
	public function setGrid(Snazzware_Grid $value) { $this->mGrid = $value; }
	public function getGrid() { return $this->mGrid; }
	
	public function __construct(Snazzware_Model_Entity $model) {
		$this->setModel($model);
	}
	
	public function getCompleteFilters() {
		// override this method to include any forced filters.
		return $this->getFilters();
	}
	
	public function getCompleteSorts() {
		// override this method to include any forced sorts.
		//return array(array('field'=>'lastname','dir'=>'ASC'));
		return $this->getSorts();
	}
	
	public function getRows($offset, $max) {		
		$rows = array();
		$entities = $this->getModel()->getItems($offset, $max, $this->getCompleteFilters(), $this->getCompleteSorts());
		
		foreach ($entities as $entity) {			
			$row = array();
			
			foreach ($this->getGrid()->getColumns() as $column) {							
				$tokens = explode('.',$column->getName());
				$value = $entity;
				
				foreach ($tokens as $token) {						
					$getter = 'get'.ucfirst($token);
					if (is_object($value)) {
						$value = $value->$getter();
					}					
				}
								
				if ($value instanceof \Snazzware\Entity) {
					$value = $value->getId();
				}
				
				$row[$column->getName()] = $value;							
			}	
			
			$rows[] = $row;	
		}		
		return $rows;
	}
	
	public function getCount() {
		return $this->getModel()->getCount($this->getCompleteFilters());
	}
	
	public function iterateBegin($offset, $max) {		
		return $this->getModel()->iterateBegin($offset, $max, $this->getCompleteFilters(), $this->getCompleteSorts());
	}
	
	public function iterateNext($iterable, $previous = null) {
		$entity = $this->getModel()->iterateNext($iterable, $previous);
		
		if ($entity != null) {			
			$row = array();

			$row ['_iterable_reference_'] = $entity;
			
			foreach ($this->getGrid()->getColumns() as $column) {
				$tokens = explode('.',$column->getName());
				$value = $entity;
			
				foreach ($tokens as $token) {
					$getter = 'get'.ucfirst($token);
					if (is_object($value)) {
						$value = $value->$getter();
					}
				}
			
				if ($value instanceof \Snazzware\Entity) {
					$value = $value->getId();
				}
			
				$row[$column->getName()] = $value;
			}
				
			return $row;
		} else return null;
	}
	
}

?>