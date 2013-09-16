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

/**
 * Snazzware_Config_Utils (aliased as ConfigUtils by default)
 * 
 * The purpose of this class is to provide access to configuration
 * loaded by Zend_Application_Bootstrap_Bootstrap for the current environment.
 * This class focuses on values which are prefixed with "namespace." by
 * default this is "snazzware.". E.g. given an application.ini with the following
 * entries:
 * 
 * whatever.somekey.subkey = "hello"
 * snazzware.somekey.subkey = "world"
 * 
 * ConfigUtils::get('somekey','subkey') will return "world". 
 * 
 * @author jmc
 *
 */
class Snazzware_Config_Utils {

	private static $options = null;
	private static $optiongroups = array(__ZFEXT_PREFIX);
	
	public static function getOptionGroups() {
		return self::$optiongroups;
	}
	
	public static function setOptionGroups($value) {
		self::$optiongroups = $value;
	}
	
	public static function get($namespace, $key, $default = null) {		
		$options = self::getOptions();	
		$optiongroups = self::getOptionGroups();
		
		foreach ($optiongroups as $optiongroup) {				
			if (isset($options[$optiongroup][$namespace][$key])) {
				return $options[$optiongroup][$namespace][$key];
			} 
		}
		
		return $default;
	}
	
	public static function setOptions($options) {
		self::$options = $options;
		if (isset(self::$options[__ZFEXT_PREFIX]['options']['optiongroups'])) {
			self::$optiongroups = self::$options[__ZFEXT_PREFIX]['options']['optiongroups'];
		}    	
	}
	
	public static function getOptions() {
		if (self::$options == null) {
			$bootstrap = \Zend_Controller_Front::getInstance()->getParam('bootstrap');			
    		self::$options = $bootstrap->getOptions();
		}
		return self::$options;
	}
	
}
