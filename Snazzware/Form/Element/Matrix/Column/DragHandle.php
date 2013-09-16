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

class Snazzware_Form_Element_Matrix_Column_DragHandle extends Snazzware_Form_Element_Matrix_Column {

	public function __construct($name, $options = array()) {
		parent::__construct($name, $options);

		$this->setFormHelper('formHidden');
	}

	public function renderHeader($view = null, $options = array()) {
		if (!$options['editable']) {
			return '';
		} else {
			return "<th>&nbsp;</th>";
		}
	}

	public function renderCell($view = null, $options = array()) {
		$options = array_merge(array('editable'=>false, 'value'=>''), $options); // default options
				
		if (!$options['editable']) {
			return '';
		} else {
			return "<td class='draghandle' ><span class='ui-icon ui-icon-grip-dotted-vertical'></span></td>";
		}
	}

}

?>