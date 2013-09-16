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

class Snazzware_Grid_Column_Checkbox extends Snazzware_Grid_Column {
	
	protected $mHelper = 'Grid_Content_Checkbox';
	protected $mHeaderHelper = 'Grid_Column_Header_Checkbox';	
	
	public function handleRequest($request,$view) {
		$handled = parent::handleRequest($request,$view);
		
		$name = $this->getFullName();//"{$this->getGrid()->getName()}_{$this->getName()}";
		
		if ($handled) {
			$prefix = __ZFEXT_PREFIX."_grid_{$name}_"; 
			
			foreach ($request->getParams() as $k=>$v) {
				if (substr($k,0,strlen($prefix))===$prefix) {
					$id = substr($k,strlen($prefix));
					
					if ($v=='checked') Snazzware_Grid_Column_Checkbox::check($name,$id);
					else Snazzware_Grid_Column_Checkbox::uncheck($name,$id);
				}
			}
			
			$result = array(
				'selected'=>count(Snazzware_Grid_Column_Checkbox::getChecked($name))
			);
			
			header('Content-Type: application/json');
			 
			echo json_encode($result);
		}
		
		return $handled;
	}
	
	public function isEnabled($options) {
		return true;
	}
	
	public static function check($name,$id) {
		State::set("Snazzware_Grid_Column_Checkbox_{$name}",$id,1);				
	}
	
	public static function uncheck($name,$id) {
		State::clear("Snazzware_Grid_Column_Checkbox_{$name}",$id);
	}
	
	public static function uncheckAll($name) {
		State::clear("Snazzware_Grid_Column_Checkbox_{$name}");
	}
	
	public static function isChecked($name,$id) {
		return State::get("Snazzware_Grid_Column_Checkbox_{$name}",$id,0);
	}
	
	public static function getChecked($name) {
		return array_keys(State::getNamespace("Snazzware_Grid_Column_Checkbox_{$name}",array()));
	}
	
}

