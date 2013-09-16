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
 * 
 * This is basically a wrapper around doctrine2 so that the rest of my framework extensions are
 * not dependent on doctrine (to a reasonable extent, I don't *plan* on changing ORMS... :).
 * 
 * @author jmc
 *
 */
class Snazzware_Entity_Utils {
	
	private static $em = null;
	
	public static function getEntityManager() { return self::$em; }
	public static function setEntityManager($em) { self::$em = $em; }
	
	public static function getRootEntityClassName() { return 'Snazzware\Entity'; }
	
	public static function get($class, $criteria = null) {
		$result = null;		
	    
		if (is_numeric($criteria) || $criteria === null) {
			if ($criteria>0) {
		    	$result = self::getEntityManager()->find($class,$criteria); 
		    } else {
		    	$result = new $class();
		    }	
		} else 
		if (is_array($criteria)) {			
			$qb = self::prepareQueryBuilder($class, $criteria);
			
			if (isset($criteria['options']['count']) && $criteria['options']['count']===true) {				
				$q = $qb->select('COUNT(a)')->getQuery();
				$result = $q->getSingleScalarResult();
				Log::debug('Snazzware\Entity\Utils','get',$q->getSQL());				
			} else {			
				$q = $qb->getQuery();
				
				$result = $q->execute();
				Log::debug('Snazzware\Entity\Utils','get',$q->getSQL());				
			}
		}
		
		return $result;
	}
	
	public static function iterateBegin($class, $criteria) {		
		$qb = self::prepareQueryBuilder($class, $criteria);
		
		return $qb->getQuery()->iterate(); 
	}

	public static function iterateNext($iterable, $previous = null) {
		if ($previous !== null) self::getEntityManager()->detach($previous);
		
		$result = $iterable->next();
		
		if (is_array($result) && count($result)>0) return reset($result);
		else return null;		
	}
	
	protected static function prepareQueryBuilder($class, $criteria) {
		$qb = self::getEntityManager()->createQueryBuilder();
			
		$qb->add('select','a')
		->add('from', "$class a");
			
		$joins['primary'] = 'a';
		$nextjoin = 'b';
			
		$vars = array();
		if (count($criteria)>0) {
	
			$hasWhereClause = false;
	
			$where = $qb->expr()->andx();
			$i = 1;
			if (isset($criteria['filters'])) {
				foreach ($criteria['filters'] as $criterion) {
					if (isset($criterion['field'])) {
						if (isset($criterion['op'])) {
							$joined = null;
	
							// Determine which joined alias to use for the field, if any.
							// Note: this only works one level deep right now...
							$tokens = explode('.',$criterion['field']);
							if (count($tokens)==2) {
								$field = array_pop($tokens);
								$imploded = implode('.',$tokens);
								if (isset($joins[$imploded])) {
									$joined = $joins[$imploded];
								} else {
									$qb->innerJoin('a.'.$imploded,$nextjoin);
									$joined = $nextjoin;
									$nextjoin++;
								}
							} else {
								$field = $criterion['field'];
								$joined = $joins['primary'];
							}
	
							if ($joined != null) {
								// Determine operator and use appropriate method of expression builder
								if ($criterion['op']=='=') {
									$where->add($joined.'.'.$qb->expr()->eq($field,'?'.$i));
									$vars[$i++] = $criterion['value'];
									$hasWhereClause = true;
								} else
									if ($criterion['op']=='in') {
									if (is_array($criterion['value'])) {
										for ($j=0;$j<count($criterion['value']);$j++) {
											$questionmarks[] = '?'.($i+$j);
										}
										$questionmarkstr = implode(',',$questionmarks);
										$where->add($joined.'.'.$qb->expr()->in($field,$questionmarkstr));
										foreach ($criterion['value'] as $value) {
											$vars[$i++] = $value;
										}
									} else {
										$where->add($joined.'.'.$qb->expr()->in($field,'?'.$i));
										$vars[$i++] = $criterion['value'];
									}
									$hasWhereClause = true;
								} else
									if ($criterion['op']=='like') {
									$tokens = explode(' ',$criterion['value']);
									unset($token);
									foreach ($tokens as $token) {
										$where->add($joined.'.'.$qb->expr()->like($field,'?'.$i));
										$vars[$i++] = '%'.$token.'%';
										$hasWhereClause = true;
									}
								} else
									if ($criterion['op']=='!=') {
									$where->add($joined.'.'.$qb->expr()->neq($field,'?'.$i));
									$vars[$i++] = $criterion['value'];
									$hasWhereClause = true;
								} else
									if ($criterion['op']=='>') {
									$where->add($joined.'.'.$qb->expr()->gt($field,'?'.$i));
									$vars[$i++] = $criterion['value'];
									$hasWhereClause = true;
								} else
									if ($criterion['op']=='>=') {
									$where->add($joined.'.'.$qb->expr()->gte($field,'?'.$i));
									$vars[$i++] = $criterion['value'];
									$hasWhereClause = true;
								} else
									if ($criterion['op']=='<') {
									$where->add($joined.'.'.$qb->expr()->lt($field,'?'.$i));
									$vars[$i++] = $criterion['value'];
									$hasWhereClause = true;
								} else
									if ($criterion['op']=='<=') {
									$where->add($joined.'.'.$qb->expr()->lte($field,'?'.$i));
									$vars[$i++] = $criterion['value'];
									$hasWhereClause = true;
								}
							}
						}
							
					}
				}
			}
	
			if ($hasWhereClause) $qb->add('where', $where);
	
			if (isset($criteria['sorts'])) {
				foreach ($criteria['sorts'] as $sort) {
					if (isset($sort['dir'])) {
							
						$joined = null;
							
						// Determine which joined alias to use for the field, if any.
						// Note: this only works one level deep right now...
						$tokens = explode('.',str_replace('_','.',$sort['field']));
						if (count($tokens)==2) {
							$field = array_pop($tokens);
							$imploded = implode('.',$tokens);
							if (isset($joins[$imploded])) {
								$joined = $joins[$imploded];
							} else {
								$qb->innerJoin('a.'.$imploded,$nextjoin);
								$joined = $nextjoin;
								$nextjoin++;
							}
						} else {
							$field = $sort['field'];
							$joined = $joins['primary'];
						}
							
						$qb->add('orderBy',$joined.'.'.$field.' '.$sort['dir']);
					}
				}
			}
	
			if (isset($criteria['options']['offset'])) $qb->setFirstResult($criteria['options']['offset']);
			if (isset($criteria['options']['max'])) $qb->setMaxResults($criteria['options']['max']);
	
			foreach ($vars as $k=>$v) {
				$qb->setParameter($k,$v);
			}
		}
			
		Log::debug('Snazzware\Entity\Utils','get',$qb->getDQL());
		Log::debug('Snazzware\Entity\Utils','get',print_r($qb->getParameters(),true));
	
		return $qb;
	}
	
	public static function delete($entity) {
		self::getEntityManager()->remove($entity);
		self::getEntityManager()->flush();
	}
	
	public static function getOne($class, $criteria = null) {
		$results = self::get($class, $criteria);
		if (is_array($results)) {
			if (count($results)>0) return $results[0];
			else return null;
		} else
		if (is_object($results)) return $results;
		else return null;
	}
	
	public static function persist($entity) {
		self::getEntityManager()->persist($entity);
    	self::getEntityManager()->flush();
	}
	
	// TODO : decouple this from doctrine2? probably...
	public static function getClassMetadata($classname) {
		$em = self::getEntityManager();
		$cmf = $em->getMetadataFactory();
		$class = $cmf->getMetadataFor($classname);
		return $class;
	}
	
	/**
     * 
     * Finds all values inside curly brackets and processes them.
     * 
     * @param string $lStrValue
     * @param \Snazzware\Entity $Entity
     */
    public static function processTokens($lStrValue,$entity) {    
		if ($entity instanceof \Snazzware\Entity) {    	
	        $lAryMutators = array();
	        
	        $len = strlen($lStrValue);
	        $tok = '';
	        $bracketed = false;
	        for ($i=0;$i<$len;$i++) {
	            if (!$bracketed) {
	                if ($lStrValue[$i] == '{') {
	                    $bracketed = true;
	                }    
	            } else {
	                if ($lStrValue[$i] == '}') {
	                    $bracketed = false;
	                    if (!empty($tok)) {
	                        $lAryMutators[$tok] = true;
	                        $tok = '';
	                    }
	                } else $tok .= $lStrValue[$i];
	            }
	        }
	        if (!empty($tok)) $lAryMutators[$tok] = true;
	        
	        foreach (array_keys($lAryMutators) as $lStrMutator) {
	        	$getter = 'get'.ucfirst($lStrMutator);
				$value = $entity->$getter();          	         
	            $lStrValue = str_replace('{'.$lStrMutator.'}',$value,$lStrValue);        	            
	        }
		}
        
        return $lStrValue;
    }
		
    // from php.net posted by "Jennifer" 31-Mar-2011
	/** 
	 * @desc: replacement for instanceof that accept strings or objects for both args 
	 * @param: Mixed $object- string or Object 
	 * @param: Mixed $class- string or Object 
	 * @return: Boolean 
	 */
    public static function oneof($object, $class){ 
	    if(is_object($object)) return $object instanceof $class; 
	    if(is_string($object)){ 
	        if(is_object($class)) $class=get_class($class); 
	
	        if(class_exists($class)) return is_subclass_of($object, $class) || $object==$class; 
	        if(interface_exists($class)) { 
	            $reflect = new ReflectionClass($object); 
	            return !$reflect->implementsInterface($class); 
	        } 
	
	    } 
	    return false; 
	} 
}

