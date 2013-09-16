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

class Snazzware_View_Helper_Form_Checkbox extends Zend_View_Helper_FormElement
{
    /**
     * Default checked/unchecked options
     * @var array
     */
    protected static $_defaultCheckedOptions = array(
        'checkedValue'   => '1',
        'uncheckedValue' => '0',
    	'checkedString' => ' checked=checked ',
    );

    /**
     * Generates a 'checkbox' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * @param mixed $value The element value.
     * @param array $attribs Attributes for the element tag.
     * @return string The element XHTML.
     */
    public function Form_Checkbox($name, $value = null, $attribs = null, array $checkedOptions = null)
    {
    	$info = $this->_getInfo($name, $value, $attribs);
	    extract($info); // name, id, value, attribs, options, listsep, disable
    	
	    if ((isset($checkedOptions['readonly']) && $checkedOptions['readonly']===true) || (isset($checkedOptions['editable']) && $checkedOptions['editable']===false)) {
    		if ($value==1) $xhtml = '&#10004;';
    		else $xhtml = '&#9744;';
    	} else { 
	        
	        $checked = false;
	        if (isset($attribs['checked']) && $attribs['checked']) {
	            $checked = true;
	            unset($attribs['checked']);
	        } elseif (isset($attribs['checked'])) {
	            $checked = false;
	            unset($attribs['checked']);
	        }
	        
	        if ($value == 1) $checked = true;
	
	        $checkedOptions['checked'] = $checked;
	        $checkedOptions['uncheckedValue'] = '0';
	        $checkedOptions['checkedValue'] = '1';
	        $checkedOptions['checkedString'] = '';
	        
	        if ($checked) $checkedOptions['checkedString'] = ' checked="checked" ';        
	        
	        // is the element disabled?
	        $disabled = '';
	        if ($disable) {
	            $disabled = ' disabled="disabled"';
	        }
	
	        // XHTML or HTML end tag?
	        $endTag = ' />';
	        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
	            $endTag= '>';
	        }
	
	        // build the element
	        $xhtml = '';
	        if (!$disable && !strstr($name, '[]')) {
	            $xhtml = $this->_hidden($name, $checkedOptions['uncheckedValue']);
	        }
	        $xhtml .= '<input type="checkbox"'
	                . ' name="' . $this->view->escape($name) . '"'
	                . ' id="' . $this->view->escape($id) . '"'
	                . ' value="' . $this->view->escape($checkedOptions['checkedValue']) . '"'
	                . $checkedOptions['checkedString']
	                . $disabled
	                . $this->_htmlAttribs($attribs)
	                . $endTag;
	
    	}
    	
    	return $xhtml;
    }

    /**
     * Determine checkbox information
     *
     * @param  string $value
     * @param  bool $checked
     * @param  array|null $checkedOptions
     * @return array
     */
    public static function determineCheckboxInfo($value, $checked, array $checkedOptions = null)
    {
        // Checked/unchecked values
        $checkedValue   = null;
        $uncheckedValue = null;
        if (is_array($checkedOptions)) {
            if (array_key_exists('checkedValue', $checkedOptions)) {
                $checkedValue = (string) $checkedOptions['checkedValue'];
                unset($checkedOptions['checkedValue']);
            }
            if (array_key_exists('uncheckedValue', $checkedOptions)) {
                $uncheckedValue = (string) $checkedOptions['uncheckedValue'];
                unset($checkedOptions['uncheckedValue']);
            }
            if (null === $checkedValue) {
                $checkedValue = (string) array_shift($checkedOptions);
            }
            if (null === $uncheckedValue) {
                $uncheckedValue = (string) array_shift($checkedOptions);
            }
        } elseif ($value !== null) {
            $uncheckedValue = self::$_defaultCheckedOptions['uncheckedValue'];
        } else {
            $checkedValue   = self::$_defaultCheckedOptions['checkedValue'];
            $uncheckedValue = self::$_defaultCheckedOptions['uncheckedValue'];
        }

        // is the element checked?
        $checkedString = '';
        if ($checked || ((string) $value === $checkedValue)) {
            $checkedString = ' checked="checked"';
            $checked = true;
        } else {
            $checked = false;
        }

        // Checked value should be value if no checked options provided
        if ($checkedValue == null) {
            $checkedValue = $value;
        }

        return array(
            'checked'        => $checked,
            'checkedString'  => $checkedString,
            'checkedValue'   => $checkedValue,
            'uncheckedValue' => $uncheckedValue,
        );
    }
}
