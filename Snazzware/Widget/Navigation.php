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

require_once('Snazzware/UtilityFunctions.php');

class Snazzware_Widget_Navigation extends Snazzware_Widget {
		
	const NAVIGATION_ITEM = 'NAVIGATION_ITEM';
	
	protected $helper = 'Widget_Navigation';	
	
	protected static $navigations = array();
	protected static $navigationClassName = 'Snazzware_Widget_Navigation';		
		
	public function createItem($type, $name, $options = array()) {
		$class = $this->getFactory()->getPluginLoader(self::NAVIGATION_ITEM)->load($type);		
		return new $class(array_merge(array('name'=>$name),$options));
	}
	
	public function build($request = null, $response = null) {
		//
	}
	
	public function __construct($options = array()) {	
		parent::__construct(array_merge_recursive_distinct(array(
			'items'=>array()
		),$options));

		self::getFactory()->addType(self::NAVIGATION_ITEM,array(
    		'prefixSegment' => 'Navigation_Item',
    		'pathSegment' => 'Navigation/Item'
    	));
		
		self::getFactory()->addPrefixPath('Snazzware_Widget_Navigation_Item', 'Snazzware/Widget/Navigation/Item/', self::NAVIGATION_ITEM);
		
		$this->removeDecorator('Container');
	}
	
	public function __get($name)
    {    	
    	$result = null;
    	
        if (isset($this->options['items'][$name])) {
            $result = $this->options['items'][$name];
        }

        return $result;
    }
	
    public static function getNavigation($name) {
    	if (!isset(self::$navigations[$name])) {
    		$classname = self::getNavigationClassName();
    		
    		$nav = new $classname(array('name'=>$name));
    		
    		self::$navigations[$name] = $nav;
    	}
    	
    	return self::$navigations[$name];
    }
    
    public static function getNavigationClassName() { return self::$navigationClassName; }
    public static function setNavigationClassName($value) { self::$navigationClassName = $value; }
    
    public function addItem($item) { parent::addWidget($item); }
	
}