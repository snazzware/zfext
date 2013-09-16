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

class Snazzware_Form_Element_Matrix_Column_DeleteButton extends Snazzware_Form_Element_Matrix_Column {

	private $mStrDeletionAjaxUrl = null;
	
	public function __construct($name, $options = array()) {
		parent::__construct($name, $options);

		$this->setFormHelper('formHidden');
	}

	public function getDeletionAjaxUrl() { return $this->mStrDeletionAjaxUrl; }
	public function setDeletionAjaxUrl($value) { $this->mStrDeletionAjaxUrl = $value; } 
	
	public function renderHeader($view = null, $options = array()) {
		if (!$options['editable']) {
			return '';
		} else {
			return "<th>&nbsp;</th>";
		}
	}

	public function renderCell($view = null, $options = array()) {		
		$options = array_merge(array('editable'=>false, 'value'=>''), $options); // default options

		$zfext = __ZFEXT_PREFIX;
		
		$xhtml = '';
		
		if ($options['editable']) {
			$helper = $this->getFormHelper();
			
			if ($this->getDeletionAjaxUrl() != null) {
			$xhtml .= "<td class='delete' onclick=\"
							
				if (confirm('Are you sure you want to delete this item?')) {				

					var parameters = new Object();
			        $(parameters).attr('primary_entity_id',$('#id').val());
			        
			        $(this).parent().find('input').each(function() {
			        	$(parameters).attr($(this).attr('colname'),$(this).val());
			        }); 
			        			        			        
			        var original = this;
			        
			        $.get('{$this->getDeletionAjaxUrl()}', parameters, function(data) {
			        	if (data.error) {
			        		$.fancybox('<B>'+data.error+'</B>');
			        	} else {
			        		{$zfext}_formgrid_deleterow_{$options['matrix_name']}($(original).parent());
			        	}
			        });
			    
				}
								
			\">";			
			} else {
				$xhtml .= "<td class='delete' onclick=\"
					if (confirm('Are you sure you want to delete this item?')) {										
						{$zfext}_formgrid_deleterow_{$options['matrix_name']}($(this).parent());
					}				
				\">";
			}
			$xhtml .= "<span class='ui-icon ui-icon-circle-close'></span>";			
			$xhtml .= $view->$helper($options['id'],$options['value'],array('colname'=>$this->getName()));			
			$xhtml .= "</td>";
		}
		
		return $xhtml;
	}

}

?>