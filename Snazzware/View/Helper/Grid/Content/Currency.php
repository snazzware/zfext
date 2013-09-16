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

class Snazzware_View_Helper_Grid_Content_Currency extends Zend_View_Helper_FormElement {
	
	protected static $currency = null;
	
	protected static function getCurrency() {
		if (self::$currency == null) self::$currency = new Zend_Currency(ConfigUtils::get('global','snazzware.global.zendCurrencyLocale','en_US'));
		return self::$currency;
	}
	
	public function Grid_Content_Currency($name, $value = null, $attribs = null, $options = null) {
		
		$xhtml = '';
		
		if (!is_object($value)) {
			$value = preg_replace('/[^0-9\.-]/Uis','', $value);
			
			if (!is_numeric($value)) $value = 0;
			
			$formatted = self::getCurrency()->toCurrency($value);
			
			if ($value < 0) {
				$xhtml .= "<span style='color: red'>$formatted</span>";
			} else {
				$xhtml .= $formatted;
			}
		}
		
		return $xhtml;
	}
	
}
