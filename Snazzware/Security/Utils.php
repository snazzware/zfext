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

class Snazzware_Security_Utils {

	const flag_execute = 1;
	const flag_create = 2;
	const flag_read = 4;
	const flag_update = 8;
	const flag_delete = 16;

	const flag_none = 0;
	const flag_listing = 5; // read and execute	
	const flag_all = 31;
	const flag_nodelete = 15; // all except delete

	const all_users = 'all_users';
	
	private static $user = null;
	private static $userClass = 'Snazzware\Entity\User';
	private static $roleClass = 'Snazzware\Entity\Role';
	private static $loggedIn = false;

	private static $roles = null;

	public static function allow($role, $access, $path = '') {
		if (self::$roles == null) self::$roles = array();
		if (!isset(self::$roles[$role])) self::$roles[$role] = array();
		self::$roles[$role][$path] = $access;
	}

	// Originally from php.net claudiu at cnixs dot com 22-Apr-2007 02:52
	// Added hostname lookup
	protected static function ipCIDRCheck ($IP, $CIDR) {
		$result = false;
		
		if (strpos($CIDR, '/')===false) { // if no slash, do exact match with hostname lookup
			$remoteip = trim($IP);
			$resolvedhost = gethostbyname($CIDR);
			
			// Test for exact match and special ipv4 to ipv6 cases
			if ($remoteip == $resolvedhost) $result = true;
			else if (($remoteip=='::1' && $resolvedhost=='127.0.0.1') || ($remoteip=='127.0.0.1' && $resolvedhost=='::1')) $result = true;
			else if (($remoteip=='0:0:0:0:0:0:0:1' && $resolvedhost=='127.0.0.1') || ($remoteip=='127.0.0.1' && $resolvedhost=='0:0:0:0:0:0:0:1')) $result = true;
			else if (("0:0:0:0:0:0:$remoteip"==$resolvedhost) || ("0:0:0:0:0:0:$resolvedhost"==$remoteip)) $result = true;
			else if (("::$remoteip"==$resolvedhost) || ("::$resolvedhost"==$remoteip)) $result = true;
			else if (("0:0:0:0:0:FFFF:$remoteip"==$resolvedhost) || ("0:0:0:0:0:FFFF:$resolvedhost"==$remoteip)) $result = true;
			else if (("::FFFF:$remoteip"==$resolvedhost) || ("::FFFF:$resolvedhost"==$remoteip)) $result = true;
			
		} else {
			list ($net, $mask) = split ("/", $CIDR);
	
			$ip_net = ip2long ($net);
			$ip_mask = ~((1 << (32 - $mask)) - 1);
	
			$ip_ip = ip2long ($IP);
	
			$ip_ip_net = $ip_ip & $ip_mask;
	
			$result = ($ip_ip_net == $ip_net);
		}
		
		return $result;
	}

	public static function hashPassword($password) {
		// TODO : strategy
		return sha1($password);
	}
	
	public static function getIpAddress() {
		return $_SERVER['REMOTE_ADDR'];
	}
	
	public static function login($username, $password) {
		$result = "An error occured while logging in.";

		$results = EntityUtils::get(
			self::getUserClass(),
			array(
				'filters'=>array(
					array('field'=>'username','op'=>'=','value'=>$username),
					array('field'=>'password','op'=>'=','value'=>$password)
				)
			)
		);
			
		if (count($results)==1) {
			$user = $results[0];
			if ($user->getLocked()==1) {
				$result = "The account '{$username}' has been locked.";
			} else {
				$ip = self::getIpAddress();
				 
				$allowed = false;
				if (($user->getRestrictions() === null) || (count($user->getRestrictions())==0)) {
					$allowed = true;
				} else {
					$allows = 0;
					$denies = 0;
					foreach ($user->getRestrictions() as $restriction) {
						if ($restriction->getAllowed()==1) $allows++;
						else $denies++;
					}

					if ($allows>0) { // if any records are "allow", only allow if a match is found
						foreach ($user->getRestrictions() as $restriction) {
							if (self::ipCIDRCheck($ip,$restriction->getCidr())) {
								if ($allowed == false) {
									Log::info('Snazzware\Security\Utils','login',"{$username} allowed by rule {$restriction->getCidr()}");
								}
								$allowed = true;
							}
						}
					} else { // if only records are "deny" (allowed=0), allow unless a match is found
						$allowed = true;
						foreach ($user->getRestrictions() as $restriction) {
							if (self::ipCIDRCheck($ip,$restriction->getCidr())) {
								$allowed = false;
								Log::warning('Snazzware\Security\Utils','login',"{$username} denied by rule {$restriction->getCidr()}");
							}
						}
					}
				}
				 
				if (!$allowed) {
					$result = "Your IP address {$ip} is not allowed to log in to the account '{$username}'";
					Log::warning('Snazzware\Security\Utils','login',"{$username} denied login from {$ip}");
				} else {
					self::setUser($user);
					self::setLoggedIn(true);
					$result = true;
				}
			}
		} else {
			Log::warning('Snazzware\Security\Utils','login',"{$username} invalid username or password");
			$result = "Invalid username or password.";
		}
		 
		return $result;
	}

	public static function logout() {
		State::clearAll();
	}

	public static function isLoggedIn() {
		return State::get(__ZFEXT_PREFIX.'-security','loggedIn',false);
	}

	public static function setLoggedIn($value) {
		State::set(__ZFEXT_PREFIX.'-security','loggedIn',true);
	}

	public static function setUser($user) {
		if ($user != null) {
			State::set(__ZFEXT_PREFIX.'-security','userId',$user->getId());
		} else {
			State::clear(__ZFEXT_PREFIX.'-security','userId');
		}
		self::$user = $user;
	}

	public static function getUser() {
		if (self::$user == null) {
			if (State::get(__ZFEXT_PREFIX.'-security','userId',0)>0) {
				self::$user = EntityUtils::get(self::getUserClass(),State::get(__ZFEXT_PREFIX.'-security','userId'));
			} else {
				self::$user = EntityUtils::get(self::getUserClass());
				if (self::$user != null) {
					$guestrole = EntityUtils::getOne(self::getRoleClass(),array('filters'=>array(array('field'=>'rolename','op'=>'=','value'=>'guest'))));
					if ($guestrole != null) {
						self::$user->getRoles()->add($guestrole);
					}
				}
			}
		}
		return self::$user;
	}

	public static function getUserClass() {
		return self::$userClass;
	}

	public static function setUserClass($class) {
		self::$userClass = $class;
	}

	public static function getRoleClass() {
		return self::$roleClass;
	}

	public static function setRoleClass($class) {
		self::$roleClass = $class;
	}

	/**
	 * e.g. $path =
	 * "invoice" - top level invoice and all
	 *
	 * Enter description here ...
	 * @param unknown_type $path
	 */
	public static function canExecute($path, $user = null) {
		return self::can($path, $user, self::flag_execute);
	}

	public static function canCreate($path, $user = null) {
		return self::can($path, $user, self::flag_create);
	}

	public static function canRead($path, $user = null) {
		return self::can($path, $user, self::flag_read);
	}

	public static function canUpdate($path, $user = null) {
		return self::can($path, $user, self::flag_update);
	}

	public static function canDelete($path, $user = null) {
		return self::can($path, $user, self::flag_delete);
	}

	protected static function can($path, $user, $flags) {
		$allowed = false;
		$userclass = self::getUserClass();

		if ($user == null) $user = self::getUser();
		if ($user instanceof $userclass) {
			$names = explode('.',$path);
			$found = false;
			while ((!$found) && ($names !== null) && (count($names)>=0)) {
				$currpath = implode('.',$names);

				foreach ($user->getRoles() as $role) {
					if (!$found) {
						if (isset(self::$roles[$role->getRolename()][$currpath])) {
							$found = true;
							if ((self::$roles[$role->getRolename()][$currpath] & $flags) == $flags) $allowed = true;
						}
					}
				}

				if (count($names)==0) {
					$names = null;
				} else {
					array_pop($names);
				}
			}
		}

		if (!$allowed) {
			Log::debug('Security\Utils','can',"{$user->getUsername()} denied access to $path for $flags");
		}
		
		return $allowed;
	}


}

