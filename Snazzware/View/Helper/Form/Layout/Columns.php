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

class Snazzware_View_Helper_Form_Layout_Columns extends Zend_View_Helper_Abstract
{
    
    public function Form_Layout_Columns($view, $form, $options) {
		$xhtml = '';
    	
    	// TODO : defaults? Exceptions?
    	$caption = $options['caption'];
    	$columns = $options['columns'];
    	$action = $view->escape($form->getAction());
    	$method = $view->escape($form->getMethod());
    	
    	// Find tallest column
    	$maxRows = 0;
    	foreach ($columns as $column) {
    		if (count($column)>$maxRows) $maxRows = count($column);
    	}
    	
    	// Pivot columns in to rows
    	$rows = array();    	
    	$colNumber = 0;
    	foreach ($columns as $column) {    		    	
    		$rowNumber = 0;
    		foreach ($column as $element) {
    			if (!isset($rows[$rowNumber])) $rows[$rowNumber] = array();
    			$rows[$rowNumber++][$colNumber] = $element;
    		}
    		while ($rowNumber < $maxRows) $rows[$rowNumber++][$colNumber] = null;
    		unset($element);
    		$colNumber++;
    	}
    	unset($column);
    	
    	// Render
    	$rendered = array();
    	
    	$xhtml .= "<form action='$action' method='$method' id='{$form->getId()}' class='layout-columns'>";
    	$xhtml .= "<h1>{$caption}</h1>";
    	$xhtml .= "<table>";
    	foreach ($rows as $row) {
    		$xhtml .= '<tr>';
    		foreach ($row as $element) {
    			if ($element != null) {
	    			$xhtml .= '<th>'.$form->$element->renderLabel().'</th>';
	    			$xhtml .= '<td>'.$form->$element->renderElement().'</td>';
	    			$rendered[$element] = true;
    			} else {
    				$xhtml .= '<th>&nbsp;</th><td>&nbsp;</td>';
    			}
    		}
    		$xhtml .= '</tr>';
    	}
    	$xhtml .= "</table>";
    	
    	// Render any hidden elements
    	foreach ($form->getElements() as $element) {    		
    		if (!isset($rendered[$element->getName()]) && ($element instanceof Snazzware_Form_Element_Hidden)) {
    			$xhtml .= $element->render();
    		}
    	}
    	$xhtml .= "</form>";
    	
    	return $xhtml;
    }
}
