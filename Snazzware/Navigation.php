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

class Snazzware_Navigation {
	
	protected $_view;	
	protected $mContainerClass = null;
	
	private $options = array();
	
	public function getItems() { if (!isset($this->options['items'])) $this->options['items'] = array(); return $this->options['items']; }
	public function addItem(Snazzware_Navigation_Item $item) { $item->setNavigation($this); $this->getItems(); $this->options['items'][$item->getName()] = $item; }
	public function setItems($value) { $this->options['items'] = $value; }
	
	public function getContainerClass() { return $this->mContainerClass; }
	public function setContainerClass($value) { $this->mContainerClass = $value; }
		
	public function build($request = null, $response = null) {
		//
	}
	
	public function __construct($request = null, $response = null) {
		$this->build($request, $response);
		$this->mContainerClass = __ZFEXT_PREFIX.'-navigation';
	}
	
	public function __get($name)
    {    	
    	$result = null;
    	
        if (isset($this->options['items'][$name])) {
            $result = $this->options['items'][$name];
        }

        return $result;
    }
	
	public function render(Zend_View_Interface $view = null)
    {    	
    	$xhtml = '';
    	
        if ($view !== null) {
            $this->setView($view);
        }
        
        $xhtml .= "<div id='{$this->getContainerClass()}' class='{$this->getContainerClass()}'>";
        
        foreach ($this->getItems() as $item) {        	
        	$xhtml .= $item->render();
        }
        
        $xhtml .= "</div>";
        
        return $xhtml;
    }
    
    public function setOrder($stereotypes) {
    	$unordered = $this->getItems();
    	$ordered = array();
    	 
    	// TODO : order items
    	$ordered = $unordered;
    	
    	$this->setItems($ordered);
    }
	
}