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

namespace Snazzware;

/** @MappedSuperclass @HasLifecycleCallbacks */
abstract class Entity {	
	
	/**
	 *	 
	 * @Column(type="integer", nullable=false)
	 * @Id
	 * @GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;
	
	/** @Column(type="string", nullable=true) */
	protected $createdBy;
	
	/** @Column(type="string", nullable=true) */
	protected $updatedBy;
	
	/** @Column(type="datetime", nullable=true) */
	protected $createdAt;
	
	/** @Column(type="datetime", nullable=true) */
	protected $updatedAt;
	
	protected $_updates = array();
	protected $_compositeUpdates = array();
	protected static $_ignoreUpdates = null;
	
	protected static $_crudHistoryClassName = '';
	
	/**
	 * 
	 * Static copy of metadata for this class, used for magic conversion
	 * @var ClassMetadata
	 */
	private static $_metadata = array();
	
	/** @PrePersist */
    public function onPrePersist()
    {
    	$this->setCreatedBy(\SecurityUtils::getUser()->getUsername());
        $this->setCreatedAt(new \DateTime());        
    }
    
	/** @PreUpdate */
    public function onPreUpdate()
    {
    	$this->setUpdatedBy(\SecurityUtils::getUser()->getUsername());
        $this->setUpdatedAt(new \DateTime());
    }
    
	/** @PostPersist */
    public function onPostPersist()
    {
    	$this->_logUpdates('onPostPersist');
    }
    
	/** @PostUpdate */
    public function onPostUpdate()
    {    	
    	$this->_logUpdates('onPostUpdate');    	
    }
	
    public function toJson() {
    	return json_encode(get_object_vars($this));
    }
    
    /*
     * Magic method to provide a "set" accessor and "get" mutator for all member variables, of the format getProperty, where Property is
     * the return of ucfirst('property') and 'property' is the name of the proerty being set or retrieved. This behavor can
     * of course be overridden by providing specific get/set method implementations.
     * 
     * @param string $name
     * @param mixed $args
     */
	public function __call($name, $args) {					
		if (strlen($name)>3) {
			$target = lcfirst(substr($name,3));
			
			if (substr($name,0,3)=='get') {
				$path = explode('_',$target);
				if (count($path)>1) {
					$target = $path[0];
					array_shift($path);
					if (property_exists(get_class($this),$target)) {						
						$entity = $this->$target;
						if (is_object($entity)) {
							$getter = 'get'.ucfirst(implode('_',$path));						
							return $entity->$getter();
						} else return null;
					} else return null;
				} else {
					if (property_exists(get_class($this),$target)) {
						return $this->$target;
					} else return null;
				}
			} else
			if (substr($name,0,3)=='set') {
				if (property_exists(get_class($this),$target)) {
					$value = $args[0];		
					if ($this->$target != $value) {
						$this->_update($target,$value);	
					}			
					$this->$target = $value;
				}
			}
		}
	}
	
	/*
	 * Records a property value change for later logging, e.g. after persisting.
	 * 
	 * @param 	string 	$property	The name of the property that has been updated.
	 * @param 	mixed 	$value		The new value of the property 		
	 * @return 	void
	 */
	protected function _update($property,$value) {
		if (!in_array($property,static::getIgnoreUpdates())) {
			$this->_updates[$property] = $value;
		}
	}
	
	public function compositeUpdate($child,$property,$value) {	
		if (!in_array($property,static::getIgnoreUpdates())) {	
			$this->_compositeUpdates[] = array('child'=>$child,'property'=>$property,'value'=>$value);
		}		
	}
	
	public function isUpdated($property) {
		return isset($this->_updates[$property]);
	}
	
	/*
	 * Calls Log::info with all property value changes recorded by calls to _update.
	 * 
	 * @param 	string	$identifier	Identifier for the log entries, usually the method name which called _logUpdates.
	 * @return	void
	 */
	protected function _logUpdates($identifier) {
		$crudHistoryClassName = self::getCrudHistoryClassName();
		
		if (!\EntityUtils::oneof($this,$crudHistoryClassName)) {		
			if (count($this->_updates)==0) {
	    		//Log::info(get_class($this),$identifier,"{$this->getId()} no changes logged");	
	    	} else {
	    		$manifest = '';
		    	foreach ($this->_updates as $property=>$value) {		    		
		    		if (is_object($value)) {		    			
						if (get_class($value) == 'DateTime') $value = $value->format(\ConfigUtils::get('global','phpDateFormat','Y-m-d'));
						else 
						if ($value instanceof \Snazzware\Entity) {
							$value = '('.$value->getHumanReadableIdentifier().')';
						} else $value = '('.get_class($value).')';						
		    		}
		    		
		    		$message = "$property = [$value]";
		    		$manifest .= "{$message}\r\n";
		    		\Log::info(get_class($this),$identifier,"{$this->getId()} $message");
		    	}
		    	
		    	foreach ($this->_compositeUpdates as $compositeUpdate) {
		    		$child = $compositeUpdate['child'];
		    		$property = $compositeUpdate['property'];
		    		$value = $compositeUpdate['value'];
		    		if (is_object($value)) {		    			
		    			if (get_class($value) == 'DateTime') $value = $value->format(\ConfigUtils::get('global','phpDateFormat','Y-m-d'));
		    			else
		    				if ($value instanceof \Snazzware\Entity) {
		    				$value = '('.$value->getHumanReadableIdentifier().')';
		    			} else $value = '('.get_class($value).')';		    	
		    		}
		    		
		    		$message = "$child $property = [$value]";
		    		$manifest .= "{$message}\r\n";
		    		\Log::info(get_class($this),$identifier,"{$this->getId()} $message");
		    	}
		    	
		    	if ($crudHistoryClassName != '') {
		    		$history = \EntityUtils::get($crudHistoryClassName,0);
		    		$history->setUser(\SecurityUtils::getUser());
		    		$history->setIpaddress(\SecurityUtils::getIpAddress());
		    		$history->setUsername(\SecurityUtils::getUser()->getUsername());
		    		$history->setTargetclass(get_class($this));
		    		$history->setTargetid($this->getId());
		    		$history->setDescription($this->getHumanReadableIdentifier());
		    		$history->setManifest($manifest);
		    		\EntityUtils::persist($history);
		    	}
	    	}
		}
	}
	
	/*
	 * Returns a list of properties to be excluded by _update and thus not logged by _logUpdates.
	 * 
	 * @return	array	List of properties to be excluded by _update and thus not logged by _logUpdates.
	 */
	protected static function getIgnoreUpdates() {
		if (self::$_ignoreUpdates == null) {
			self::$_ignoreUpdates = array('createdBy','createdAt','updatedBy','updatedAt');			
		} 
		return self::$_ignoreUpdates;
	}
	
	/*
	 * Caching accessor for class metadata. If metadata has not yet been retrieved from EntityUtils for this class, it is retrieved,
	 * stored for later use, and returned.
	 * 
	 * @return	mixed	Class metadata
	 */
	protected static function getClassMetadata($classname) {
		if (!isset(self::$_metadata[$classname])) self::$_metadata[$classname] = \EntityUtils::getClassMetadata($classname);
		return self::$_metadata[$classname];
	}
	
	
	public static function setCrudHistoryClassName($value) {
		self::$_crudHistoryClassName = $value;
	}
	
	public static function getCrudHistoryClassName() {
		return self::$_crudHistoryClassName;
	}
	
	public function getHumanReadableIdentifier() {
		if ($this->getId()>0) {
			return get_class($this).' id '.$this->getId();
		} else {
			return get_class($this).' (new)';
		}
	}
}