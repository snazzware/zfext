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

class Snazzware_Log {
	
	const emergency = 'emergency';
	const alert = 'alert';
	const critical = 'critical';
	const error = 'error';
	const warning = 'warning';
	const notice = 'notice';
	const info = 'info';
	const debug = 'debug';
	
	protected static function log($namespace,$key,$message,$severity) {
		$username = SecurityUtils::getUser()->getUsername();
		$ip = $_SERVER['REMOTE_ADDR'];
		error_log("Snazzware\Log: $severity - {$ip} {$username} {$namespace}.{$key} $message");
	}
	
	public static function emergency($namespace,$key,$message) {
		self::log($namespace,$key,$message,self::emergency);
	}
	
	public static function alert($namespace,$key,$message) {
		self::log($namespace,$key,$message,self::alert);
	}
	
	public static function critical($namespace,$key,$message) {
		self::log($namespace,$key,$message,self::critical);
	}
	
	public static function error($namespace,$key,$message) {
		self::log($namespace,$key,$message,self::error);
	}
	
	public static function warning($namespace,$key,$message) {
		self::log($namespace,$key,$message,self::warning);
	}
	
	public static function notice($namespace,$key,$message) {
		self::log($namespace,$key,$message,self::notice);
	}
	
	public static function info($namespace,$key,$message) {
		self::log($namespace,$key,$message,self::info);
	}
	
	public static function debug($namespace,$key,$message) {
		//self::log($namespace,$key,$message,self::debug);
	}
	
}

?>