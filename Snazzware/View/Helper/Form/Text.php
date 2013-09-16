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
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FormText.php 20096 2010-01-06 02:05:09Z bkarwin $
 */


/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a "text" element
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Snazzware_View_Helper_Form_Text extends Zend_View_Helper_FormElement
{    
    public function Form_Text($name, $value = null, $attribs = null, $options=null)
    {    	    	
    	/*if ($options['readonly']===true) {    		
    		$info = $this->_getInfo($name, $value, $attribs);
	     	extract($info); // name, value, attribs, options, listsep, disable
    		
    		return "<span id='{$this->view->escape($id)}'>$value</span>";
    	} else {*/
    		$editable = '';
	        if (($options['readonly']===true) || (isset($options['editable']) && $options['editable']===false)) {
	        	$editable = ' readonly="readonly" ';
	        }
	    
    		$info = $this->_getInfo($name, $value, $attribs);
	     	extract($info); // name, value, attribs, options, listsep, disable
    		
	        // build the element
	        $disabled = '';
	        if ($disable) {
	            // disabled
	            $disabled = ' disabled="disabled" ';
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
	                . $editable
	                . $this->_htmlAttribs($attribs)
	                . $endTag;
	
	        return $xhtml;
    	}
    //}
}
