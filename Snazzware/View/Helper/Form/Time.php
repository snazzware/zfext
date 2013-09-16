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

class Snazzware_View_Helper_Form_Time extends Zend_View_Helper_FormElement
{
    
    public function Form_Time($name, $value = null, $attribs = null, $options = array())
    {    	
    	if ($options['readonly']===true) {
    		return $value;
    	} else {    	
	        $info = $this->_getInfo($name, $value, $attribs);
	        extract($info); // name, value, attribs, options, listsep, disable
	
	        // build the element
	        $disabled = '';
	        if ($disable) {
	            // disabled
	            $disabled = ' disabled="disabled"';
	        }
	
	        // XHTML or HTML end tag?
	        $endTag = ' />';
	        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
	            $endTag= '>';
	        }
	
	        $xhtml = '<input type="text"'
	                . ' name="' . $this->view->escape($name) . '"'
	                . ' id="' . $this->view->escape($id) . '"'
	                . ' value="' . $this->view->escape($value) . '"'
	                . $disabled
	                . $this->_htmlAttribs($attribs)
	                . $endTag;
	                
	        $xhtml .= '<script> $(function() { $( "#'. $this->view->escape($id).'" ).timepicker({ showPeriod: true, defaultTime: "00:00" }); }); </script>';
	
	        return $xhtml;
    	}
    }
}
