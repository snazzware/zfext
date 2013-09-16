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

class Snazzware_Form_Element_Matrix_Column_Hidden extends Snazzware_Form_Element_Matrix_Column {

	public function __construct($name, $options = array()) {
		parent::__construct($name, $options);

		$this->setFormHelper('Form_Hidden');
	}

	public function renderHeader($view = null, $options = array()) {
		return "<th style='display: none'>{$this->getCaption()}</th>";
	}

	public function renderCell($view = null, $options = array()) {
		$options = array_merge(array('editable'=>false, 'value'=>''), $options); // default options
		
		$helper = $this->getFormHelper();		
		
		$xhtml = '';
		$xhtml .= "<td style='display: none'>";
		$xhtml .= $view->$helper(
			$options['id'],
			$options['value'],
			array('colname'=>$this->getName()),
			array('readonly'=>(!$options['editable']))
			);
		$xhtml .= '</td>';
		
		return $xhtml;
	}

}

?>