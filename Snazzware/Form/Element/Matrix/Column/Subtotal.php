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

class Snazzware_Form_Element_Matrix_Column_Subtotal extends Snazzware_Form_Element_Matrix_Column {
		
	protected $mFormHelper = 'Form_Text';

	public function __construct($name, $options = array()) {						
		parent::__construct($name, array_merge(array('editable'=>false),$options));
	}
	
	public function renderScripting($view = null, $options = array()) {
		$xhtml = '';
		
		$zfext = __ZFEXT_PREFIX;
		
		$columnOptions = $this->getOptions();
		$factors = "'".implode("','",$columnOptions['factors'])."'";
		
		if (isset($columnOptions['format'])) $numberformat = $columnOptions['format'];
		else $numberformat = '#';
		
		if (isset($columnOptions['excludeColumn'])) $excludeColumn = $columnOptions['excludeColumn'];
		else $excludeColumn = '';
		
		if (isset($columnOptions['excludeValue'])) $excludeValue = $columnOptions['excludeValue'];
		else $excludeValue = '';
		
		if ($excludeColumn != '') {
			$exclusion = "
				else
				if ($(this).attr('colname') == '{$excludeColumn}') {
					if ($(this).is('input:checkbox')) {
						if ($(this).attr('checked')) {
							exclude = true;							
						} 
					} else {
						if ($(this).val() == '{$excludeValue}') {					
							exclude = true;
						}
					}
				}
			";
		} else $exclusion = '';
		
		$xhtml .= "
			<script>
			$('#{$zfext}-formgrid-table-{$options['matrix_name']}').bind('{$zfext}-onChange', function (event, source) {					
				$('#{$zfext}-formgrid-table-{$options['matrix_name']}').find('tr').each(function() {								
					var factors = new Array();
					var target = null;	
					var subtotal = 0;
					var exclude = false;
					
					// Get factor values and target element
					$(this).find('td').each(function() {									
						$(this).children().each(function() {
							if (jQuery.inArray($(this).attr('colname'),[{$factors}])>-1) {												
								factors.push($(this).val());	
							} else 
							if ($(this).attr('colname') == '{$this->getName()}') {
								target = this;
							} $exclusion
						});
					});
					
					// Multiply factors
					if (exclude != true) {						
						subtotal = 1;
						$(factors).each(function() {
							subtotal *= this;
						});
					}
					
					// Assign result to target
					if (target) {
						if (isNaN(subtotal)) {
							$(target).html('');
							$(target).val('');
						} else {
							var formatted = $.formatNumber(subtotal, {format:'{$numberformat}', locale: 'us'});
						
							$(target).html(formatted);
							$(target).val(formatted);
						}
					}					
					
				});
			});
			</script>
		";
		
		return $xhtml;
	}
}

?>