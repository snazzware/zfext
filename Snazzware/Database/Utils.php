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

class Snazzware_Database_Utils {
	
	private static $defaultDb = null;
	
	/**
	 * 
	 * This method is tightly coupled with the doctrine configuration, and is here just as a point
	 * of abstraction for when I eventually flesh out the database utilities class.
	 * 
	 * @return Zend_Db
	 * 
	 */
	public static function getDefaultDatabase() {		
		if (self::$defaultDb == null) {
			$options = ConfigUtils::getOptions();
			
			self::$defaultDb = Zend_Db::factory('PDO_MYSQL',array(
				'host'=>$options['resources']['doctrine']['dbal']['connections']['default']['parameters']['host'],
				'dbname'=>$options['resources']['doctrine']['dbal']['connections']['default']['parameters']['dbname'],
				'username'=>$options['resources']['doctrine']['dbal']['connections']['default']['parameters']['user'],
				'password'=>$options['resources']['doctrine']['dbal']['connections']['default']['parameters']['password'],
				'port'=>$options['resources']['doctrine']['dbal']['connections']['default']['parameters']['port']
			));
		}
		
		return self::$defaultDb;
	}
}

