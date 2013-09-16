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

class Snazzware_State {
	public static function get($namespace, $key, $value=null) {
		if (isset($_SESSION[__ZFEXT_PREFIX.'-state'][$namespace][$key])) {
			return $_SESSION[__ZFEXT_PREFIX.'-state'][$namespace][$key];
		} else return $value;		
	}
	
	public static function set($namespace, $key, $value) {
		if (!isset($_SESSION[__ZFEXT_PREFIX.'-state'])) $_SESSION[__ZFEXT_PREFIX.'-state'] = array();
		if (!isset($_SESSION[__ZFEXT_PREFIX.'-state'][$namespace])) $_SESSION[__ZFEXT_PREFIX.'-state'][$namespace] = array();
		$_SESSION[__ZFEXT_PREFIX.'-state'][$namespace][$key] = $value;
	}
	
	public static function clear($namespace, $key = null) {
		if ($key != null) {
			if (isset($_SESSION[__ZFEXT_PREFIX.'-state'][$namespace][$key])) {
				unset($_SESSION[__ZFEXT_PREFIX.'-state'][$namespace][$key]);
			}
		} else {
			if (isset($_SESSION[__ZFEXT_PREFIX.'-state'][$namespace])) {
				unset($_SESSION[__ZFEXT_PREFIX.'-state'][$namespace]);
			}
		}
	}	
	
	public static function clearAll() {
		unset($_SESSION[__ZFEXT_PREFIX.'-state']);
	}
	
	public static function getNamespace($namespace,$value=array()) {
		if (!isset($_SESSION[__ZFEXT_PREFIX.'-state'][$namespace])) return $value;
		else return $_SESSION[__ZFEXT_PREFIX.'-state'][$namespace];
	}
	
	public static function setNamespace($namespace,$value) {
		$_SESSION[__ZFEXT_PREFIX.'-state'][$namespace] = $value;
	}
}

?>