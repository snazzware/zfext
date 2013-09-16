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

class Snazzware_View_Helper_Form_Select extends Zend_View_Helper_FormElement
{

    public function Form_Select($name, $value = null, $attribs = null,
        $options = null, $listsep = "<br />\n")
    {
    	$xhtml = '';
    	
    	if ((isset($options['readonly']) && $options['readonly']===true) || (isset($options['editable']) && $options['editable']===false)) {
    		$valuecaption = '';
    		foreach ((array) $options['items'] as $opt_value => $opt_label) {
    			if ($opt_value == $value) {
    				$valuecaption = $opt_label;
    			}
    		}
    		
    		$xhtml .= $valuecaption;
    		
    		$xhtml .= $this->_hidden($name, $value);
    	} else {    	
	        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
	        extract($info); // name, id, value, attribs, options, listsep, disable
	
	        // force $value to array so we can compare multiple values to multiple
	        // options; also ensure it's a string for comparison purposes.
	        $value = array_map('strval', (array) $value);
	
	        // check if element may have multiple values
	        $multiple = '';
	
	        if (substr($name, -2) == '[]') {
	            // multiple implied by the name
	            $multiple = ' multiple="multiple"';
	        }
	
	        if (isset($attribs['multiple'])) {
	            // Attribute set
	            if ($attribs['multiple']) {
	                // True attribute; set multiple attribute
	                $multiple = ' multiple="multiple"';
	
	                // Make sure name indicates multiple values are allowed
	                if (!empty($multiple) && (substr($name, -2) != '[]')) {
	                    $name .= '[]';
	                }
	            } else {
	                // False attribute; ensure attribute not set
	                $multiple = '';
	            }
	            unset($attribs['multiple']);
	        }
	
	        // now start building the XHTML.
	        $disabled = '';
	        if (true === $disable) {
	            $disabled = ' disabled="disabled"';
	        }
	
	        // Build the surrounding select element first.
	        $xhtml = '<select'
	                . ' name="' . $this->view->escape($name) . '"'
	                . ' id="' . $this->view->escape($id) . '"'
	                . $multiple
	                . $disabled
	                . $this->_htmlAttribs($attribs)
	                . ">\n    ";
	
	        // build the list of options
	        $list       = array();
	        $translator = $this->getTranslator();
	        foreach ((array) $options['items'] as $opt_value => $opt_label) {
	            if (is_array($opt_label)) {
	                $opt_disable = '';
	                if (is_array($disable) && in_array($opt_value, $disable)) {
	                    $opt_disable = ' disabled="disabled"';
	                }
	                if (null !== $translator) {
	                    $opt_value = $translator->translate($opt_value);
	                }
	                $list[] = '<optgroup'
	                        . $opt_disable
	                        . ' label="' . $this->view->escape($opt_value) .'">';
	                foreach ($opt_label as $val => $lab) {
	                    $list[] = $this->_build($val, $lab, $value, $disable);
	                }
	                $list[] = '</optgroup>';
	            } else {
	                $list[] = $this->_build($opt_value, $opt_label, $value, $disable);
	            }
	        }
	
	        // add the options to the xhtml and close the select
	        $xhtml .= implode("\n    ", $list) . "\n</select>";		
    	}
    	
    	return $xhtml;
    }

    /**
     * Builds the actual <option> tag
     *
     * @param string $value Options Value
     * @param string $label Options Label
     * @param array  $selected The option value(s) to mark as 'selected'
     * @param array|bool $disable Whether the select is disabled, or individual options are
     * @return string Option Tag XHTML
     */
    protected function _build($value, $label, $selected, $disable)
    {
        if (is_bool($disable)) {
            $disable = array();
        }

        $opt = '<option'
             . ' value="' . $this->view->escape($value) . '"'
             . ' label="' . $this->view->escape($label) . '"';

        // selected?
        if (in_array($value, $selected)) { // originally this was casting $value to (string), but this was causing issues with some numeric values...
            $opt .= ' selected="selected"';
        }

        // disabled?
        if (in_array($value, $disable)) {
            $opt .= ' disabled="disabled"';
        }

        $opt .= '>' . $this->view->escape($label) . "</option>";

        return $opt;
    }
    

}
