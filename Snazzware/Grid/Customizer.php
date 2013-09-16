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

class Snazzware_Grid_Customizer {

	protected $mPreferenceClassName = '\Snazzware\Entity\Preferences\Grid';
	
	public function getPreferenceClassName() { return $this->mPreferenceClassName; }
	public function setPreferenceClassName($value) { $this->mPreferenceClassName = $value; }
	
	public function getCustomization($classname) {		
		$criteria = array(
			'filters'=>array(
				'classname'=>array(
					'field'=>'classname',
					'op'=>'=',
					'value'=>$classname
				),
				'owner'=>array(
					'field'=>'owner',
					'op'=>'=',
					'value'=>SecurityUtils::getUser()->getId()
				)
			)
		);
		
		$result = EntityUtils::get($this->getPreferenceClassName(),$criteria);
		
		if (is_array($result)) return reset($result);
		else return null;		
	}

}

?>