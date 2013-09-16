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

class Snazzware_Preferences {	
	private static $_GeneralPreferenceClassName = '';
	
	public static function get($namespace, $key, $value=null) {
		if (!isset($_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace])) {
			$_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace] = self::loadPreferencesByNamespace($namespace);
		}
		
		if (isset($_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace][$key])) {
			return $_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace][$key];
		} else return $value;		
	}
	
	public static function set($namespace, $key, $value) {
		if (!isset($_SESSION[__ZFEXT_PREFIX.'-preferences'])) $_SESSION[__ZFEXT_PREFIX.'-preferences'] = array();
		if (!isset($_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace])) $_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace] = array();
		$_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace][$key] = $value;
		self::persistPreference($namespace,$key,$value);
	}
	
	public static function clear($namespace, $key=null) {
		if ($key==null) {
			if (isset($_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace])) {
				unset($_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace]);				
			}
		} else {
			if (isset($_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace][$key])) {
				unset($_SESSION[__ZFEXT_PREFIX.'-preferences'][$namespace][$key]);				
			}
		}
		self::deletePreference($namespace,$key);
	}
	
	public static function clearAll() {
		unset($_SESSION[__ZFEXT_PREFIX.'-preferences']);
	}
	
	// persistence of preferences
	///////////////////////////////
	
	public static function setGeneralPreferenceClassName($value) {
		self::$_GeneralPreferenceClassName = $value;
	}
	
	public static function getGeneralPreferenceClassName() {
		return self::$_GeneralPreferenceClassName;
	}
	
	public static function loadPreferencesByNamespace($namespace) {
		$preferences = array();
		
		$classname = self::getGeneralPreferenceClassName(); 
		
		if ($classname != '') {
			$options = array(
				'filters'=>array(
					'owner'=>array(
						'field'=>'owner',
						'op'=>'=',
						'value'=>SecurityUtils::getUser()->getId()
					),
					'namespace'=>array(
						'field'=>'namespace',
						'op'=>'=',
						'value'=>$namespace
					)
				)
			);
			$entities = EntityUtils::get($classname,$options);
			
			foreach ($entities as $entity) {
				$preferences[$entity->getKey()] = unserialize($entity->getValue());
			}
		}
		
		return $preferences;
	}
	
	public static function persistPreference($namespace,$key,$value) {
		$classname = self::getGeneralPreferenceClassName();
		
		if ($classname != '') {
			$options = array(
					'filters'=>array(
						'owner'=>array(
							'field'=>'owner',
							'op'=>'=',
							'value'=>SecurityUtils::getUser()->getId()
						),
						'namespace'=>array(
							'field'=>'namespace',
							'op'=>'=',
							'value'=>$namespace
						),
						'key'=>array(
							'field'=>'key',
							'op'=>'=',
							'value'=>$key
						)
					)
			);
			
			$entities = EntityUtils::get($classname,$options);
			
			if (count($entities)>0) {
				$entity = reset($entities);
				$entity->setValue(serialize($value));
				EntityUtils::persist($entity);
			} else {
				$entity = EntityUtils::get($classname,0);
				$entity->setOwner(SecurityUtils::getUser());
				$entity->setNamespace($namespace);
				$entity->setKey($key);
				$entity->setValue(serialize($value));
				EntityUtils::persist($entity);
			}
		}
	}
	
	public static function deletePreference($namespace,$key=null) {
		$classname = self::getGeneralPreferenceClassName();
		
		if ($classname != '') {
			$options = array(
					'filters'=>array(
							'owner'=>array(
									'field'=>'owner',
									'op'=>'=',
									'value'=>SecurityUtils::getUser()->getId()
							),
							'namespace'=>array(
									'field'=>'namespace',
									'op'=>'=',
									'value'=>$namespace
							)
					)
			);
			
			if ($key != null) {
				$options['filters']['key']=array('field'=>'key','op'=>'=','value'=>$key);
			}
			
			$entities = EntityUtils::get($classname,$options);
				
			foreach ($entities as $entity) {
				EntityUtils::delete($entity);
			}
		}
	}
}

?>