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

class Snazzware_Model_Entity {
	
	protected $mEntityClass = null;
	protected $options;
	
	private $filterLogicalOperator = 'and';
	
	public function getFilterLogicalOperator() { return $this->filterLogicalOperator; }
	public function setFilterLogicalOperator($value) { $this->filterLogicalOperator = $value; }
	
	public function setEntityClass($value) { $this->mEntityClass = $value; }
	public function getEntityClass() { return $this->mEntityClass; }
	
	public function __construct($entityClass = '', $options = array()) {
		$this->options = $options;		
		if ($entityClass != '') $this->setEntityClass($entityClass);
	}
	
	public function buildCriteria($filters, $sorts = array()) {		
		return array('filters'=>$filters, 'sorts'=>$sorts, 'options' => array());
	}
	
	public function getItems($offset, $max, $filters = array(), $sorts = array()) {
		$criteria = $this->buildCriteria($filters, $sorts);
		
		$criteria['options'] = array_merge(array('offset'=>$offset, 'max'=>$max, 'filterLogicalOperator'=>$this->getFilterLogicalOperator()), $criteria['options']);		
		
		return EntityUtils::get($this->getEntityClass(),$criteria);		
	}
	
	public function getCount($filters = array()) {		
		$criteria = $this->buildCriteria($filters);
		
		$criteria['options'] = array_merge(array('count'=>true), $criteria['options']);
		
		return EntityUtils::get($this->getEntityClass(),$criteria);		
	}
	
	public function iterateBegin($offset, $max, $filters = array(), $sorts = array()) {
		
		$criteria = $this->buildCriteria($filters, $sorts);
		
		$criteria['options'] = array_merge(array('offset'=>$offset, 'max'=>$max, 'filterLogicalOperator'=>$this->getFilterLogicalOperator()), $criteria['options']);
		
		return EntityUtils::iterateBegin($this->getEntityClass(), $criteria);
	}

	public function iterateNext($iterable, $previous = null) {
		return EntityUtils::iterateNext($iterable, $previous);
	}
	
	public function setOption($option, $value) {
		$this->options[$option] = $value;
	}
	
	public function setOptions($options, $merge = true) {
		if ($merge) {
			$this->options = array_merge($this->options, $options);
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
	
}
