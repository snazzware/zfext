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

class Snazzware_View_Helper_Form_Hidden extends Zend_View_Helper_FormElement
{
   
    public function Form_Hidden($name, $value = null, array $attribs = null, $options = null)
    {
    	if ($options['readonly']===true) {
    		return '';
    	} else {
	        $info = $this->_getInfo($name, $value, $attribs);
	        extract($info); // name, value, attribs, options, listsep, disable
	        if (isset($id)) {
	            if (isset($attribs) && is_array($attribs)) {
	                $attribs['id'] = $id;
	            } else {
	                $attribs = array('id' => $id);
	            }
	        }
	        return $this->_hidden($name, $value, $attribs);
    	}
    }
}
