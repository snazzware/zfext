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

class DatabaseUtils extends Snazzware_Database_Utils {}
class EntityUtils extends Snazzware_Entity_Utils {}
class SecurityUtils extends Snazzware_Security_Utils {}
class ConfigUtils extends Snazzware_Config_Utils {}
class State extends Snazzware_State {}
class Preferences extends Snazzware_Preferences {}
class Alert extends Snazzware_Alert {}
class Log extends Snazzware_Log {}
class Printer extends Snazzware_Printer {}
class NavigationFactory extends Snazzware_Navigation_Factory {}

define('__ZFEXT_PREFIX','snazzware');

class Snazzware_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected function _initModuleAutoload() {
		$autoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Application',
			'basePath' => APPLICATION_PATH
		));
		
		$autoloader->addResourceType('grid','grids','Grid');
		$autoloader->addResourceType('navigation','navigations','Navigation');
		$autoloader->addResourceType('controller','controllers','Controller');
		
		return $autoloader;
	}
	
	protected function setPermissions() {
		SecurityUtils::allow('admin', SecurityUtils::flag_all);
	}
	
	protected function setDependencyInjections() {
		// stub
	}
	
	protected function preRun() {
		// stub
	}
	
	protected function postRun() {
		// stub
	}
	
	public function run() {
		$start = microtime(true);
		
		session_start();
		
		$dc = Zend_Registry::get('doctrine'); 
		EntityUtils::setEntityManager($dc->getEntityManager());
		
		ConfigUtils::setOptions($this->getOptions());
		
		SecurityUtils::setUserClass(ConfigUtils::get('security','userclass','\Snazzware\Entity\User'));
		SecurityUtils::setRoleClass(ConfigUtils::get('security','roleclass','\Snazzware\Entity\Role'));
		
		$this->setPermissions();
		$this->setDependencyInjections();
		
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		
		// add default snazzware script path
		$view->addScriptPath(APPLICATION_PATH.'/../library/Snazzware/View/Scripts');
		
		//Zend_Controller_Action_HelperBroker::addHelper(new Snazzware_Controller_Action_Helper_ViewRenderer());
		
		Log::info(get_class($this),'dispatch',$_SERVER['REQUEST_URI']);
		
		$this->preRun();
		$result = parent::run();
		$this->postRun();
		
		$duration = microtime(true) - $start;		
		Log::info(get_class($this),'complete',$duration.' '.$_SERVER['REQUEST_URI']);
		
		return $result;
	}

}

