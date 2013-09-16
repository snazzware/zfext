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

/*
 * 
 * 
 */

/**
 * This class serves two functions; it has static methods which allow for the registering of stereotypes 
 * to specific class names, and also a static function which produces a singleton of the factory class
 * for use in building things using Zend Framework's plugin structure.
 * 
 * See Widget/Paginated/Grid.php for an example of how this is used, in particular the getFactory() and 
 * createColumn() methods.
 * 
 * "We own a steel mill in Cleveland, shipping in Texas, oil refineries in Seattle, 
 * and a factory in Chicago that makes miniature models of factories."
 * 
 * @author jmc
 *
 */
class Snazzware_Factory {	

	protected static $stereotypes = null;
	protected static $factories = null;
	
	protected $loaders = array();
	protected $types = array();
	
	public function __construct($options) {
		if (isset($options['types'])) $this->types = $options['types'];
	}
	
	public static function create($options = array()) {				
		$stereotypes = static::getStereotypes();
		
		if (isset($options['stereotype']) && isset($stereotypes[$options['stereotype']])) {
			$classname = $stereotypes[$options['stereotype']];
		} else {
			$classname = $stereotypes['default'];
		}
		
		if ($classname == '') return null;		
		return new $classname($options);
	}
	
	public static function registerStereotype($stereotype,$classname) {
		$stereotypes = static::getStereotypes(); // init
		self::$stereotypes[$stereotype] = $classname;
	}
	
	public static function getStereotypes() {		
		if (self::$stereotypes == null) self::$stereotypes = array('default'=>'');
		return self::$stereotypes;
	}
	
	// Loaders - copied and modified from Zend Framework's Form class
	
	public static function getFactory($name, $options = array()) {
		if (!isset(self::$factories[$name])) self::$factories[$name] = new Snazzware_Factory($options);
		return self::$factories[$name];
	}
	
	public function getType($type) {
		if (isset($this->types[$type])) return $this->types[$type];
		else return null;
	}
	
	public function addType($type, $options) {
		$this->types[$type] = $options;
	}
	
	public function getTypes() {
		return $this->types;
	}
	
	public function setTypes($types) {
		$this->types = $types;
	}
	
	/**
	 * Set plugin loaders for use with decorators and elements
	 *
	 * @param  Zend_Loader_PluginLoader_Interface $loader
	 * @param  string $type (arbitrary)
	 * @return Zend_Form
	 * @throws Zend_Form_Exception on invalid type
	 */
	public function setPluginLoader(Zend_Loader_PluginLoader_Interface $loader, $type = null)
	{		
		if (isset($this->types[$type])) {		
			$this->loaders[$type] = $loader;
			return $this;
		} else {
			require_once 'Zend/Form/Exception.php';
			throw new Zend_Form_Exception(sprintf('Invalid type "%s" provided to setPluginLoader()', $type));
		}
	}
	
	/**
	 * Retrieve plugin loader for given type
	 *
	 * $type may be one of:
	 * - decorator
	 * - element
	 *
	 * If a plugin loader does not exist for the given type, defaults are
	 * created.
	 *
	 * @param  string $type
	 * @return Zend_Loader_PluginLoader_Interface
	 */
	public function getPluginLoader($type = null)
	{		
		if (!isset($this->loaders[$type])) {
			if (isset($this->types[$type])) {
				$prefixSegment = $this->types[$type]['prefixSegment'];
				$pathSegment   = $this->types[$type]['pathSegment'];
			} else {				
				require_once 'Zend/Form/Exception.php';
				throw new Zend_Form_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
			}
	
			require_once 'Zend/Loader/PluginLoader.php';
			$this->loaders[$type] = new Zend_Loader_PluginLoader(
					array('Zend_' . $prefixSegment . '_' => 'Zend/' . $pathSegment . '/')
			);
		}
	
		return $this->loaders[$type];
	}
	
	public function addPrefixPath($prefix, $path, $type = null)
	{		
		if (($type != null) && (isset($this->types[$type]))) {
			$loader = $this->getPluginLoader($type);
			$loader->addPrefixPath($prefix, $path);
		} else
		if ($type == null) {			
			$nsSeparator = (false !== strpos($prefix, '\\'))?'\\':'_';
			$prefix = rtrim($prefix, $nsSeparator);
			$path   = rtrim($path, DIRECTORY_SEPARATOR);
			foreach (array_keys($this->types) as $type) {
				$cType        = ucfirst(strtolower($type));
				$pluginPath   = $path . DIRECTORY_SEPARATOR . $cType . DIRECTORY_SEPARATOR;
				$pluginPrefix = $prefix . $nsSeparator . $cType;
				$loader       = $this->getPluginLoader($type);
				$loader->addPrefixPath($pluginPrefix, $pluginPath);
			}
			return $this;
		} else {			
			require_once 'Zend/Form/Exception.php';
			throw new Zend_Form_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
		}
	}
	
	public function addPrefixPaths(array $spec)
	{
		if (isset($spec['prefix']) && isset($spec['path'])) {
			return $this->addPrefixPath($spec['prefix'], $spec['path']);
		}
		foreach ($spec as $type => $paths) {
			if (is_numeric($type) && is_array($paths)) {
				$type = null;
				if (isset($paths['prefix']) && isset($paths['path'])) {
					if (isset($paths['type'])) {
						$type = $paths['type'];
					}
					$this->addPrefixPath($paths['prefix'], $paths['path'], $type);
				}
			} elseif (!is_numeric($type)) {
				if (!isset($paths['prefix']) || !isset($paths['path'])) {
					continue;
				}
				$this->addPrefixPath($paths['prefix'], $paths['path'], $type);
			}
		}
		return $this;
	}
}