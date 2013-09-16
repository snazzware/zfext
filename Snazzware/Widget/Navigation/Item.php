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

abstract class Snazzware_Widget_Navigation_Item extends Snazzware_Widget {
	
	protected $helper = "Widget_Navigation_Item";
	
	public function __construct($options = array()) {
						
		parent::__construct(array_merge_recursive_distinct(array(			
			'events'=>array(
				'click'=>array(
					'helper'=>'Widget_Navigation_Item_Event_Click'
				)
			)
		),$options));
		
		if ($this->getOption('securityPath','') == '') {
			$this->setOption('securityPath',$this->getOption('module').'.'.$this->getOption('controller').'.'.$this->getOption('action'));
		}
		
		$this->removeDecorator('Container');
	}
	
	public function canExecute() {		 	
		return SecurityUtils::canExecute($this->getOption('securityPath'));
	}
	
	public function __toString()
    {
    	$xhtml = '';
    	
        try {
            $xhtml = $this->render();            
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);            
        }
        
        return $xhtml;
    }
	
}