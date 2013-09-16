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

class Snazzware_Form_Element_Matrix_Widget_Count extends Snazzware_Form_Element_Matrix_Widget {
	
	public function render($view = null, $options = array()) {
		
		$zfext = __ZFEXT_PREFIX;
		
		$widgetOptions = $this->getOptions();
		
		if (isset($widgetOptions['format'])) $numberformat = $widgetOptions['format'];
		else $numberformat = '#';

		if (isset($widgetOptions['skipcolumn'])) $skipcolumn = $widgetOptions['skipcolumn'];
		else $skipcolumn = '';
		
		if (isset($widgetOptions['skipvalue'])) $skipvalue = $widgetOptions['skipvalue'];
		else $skipvalue = '';
		
		if (isset($widgetOptions['multiplier'])) $multiplier = $widgetOptions['multiplier'];
		else $multiplier = '';
		
		$xhtml = "
			<script>
			$('#{$zfext}-formgrid-table-{$options['matrix_name']}').bind('{$zfext}-postChange', function (event, source) {					
				var total = 0;					
				$('#{$zfext}-formgrid-table-{$options['matrix_name']}').find('tr').each(function() {
					if (($(this).css('display')!='none') && ($(this).hasClass('{$zfext}-formgrid-row'))) {					
						var value = 1;
						var skip = false;
					
						// Get factor values and target element
						$(this).find('td').each(function() {									
							$(this).children().each(function() {
								if ($(this).attr('colname')=='{$multiplier}') {
									if (!isNaN($.parseNumber($(this).val()))) {										
										value *= $.parseNumber($(this).val());
									}	
								} else
								if ($(this).attr('colname')=='{$skipcolumn}') {
									if ($(this).val()=='{$skipvalue}') {										
										skip = true;
									}	
								}
							});
						});						
						
						if (!skip) total += value;
					}								
				});				
				
				var formatted = $.formatNumber(total, {format:'{$numberformat}', locale: 'us'});
				
				if ($('#{$widgetOptions['target']}').is('input')) {
					$('#{$widgetOptions['target']}').val(formatted);
				} else {
					$('#{$widgetOptions['target']}').html(formatted);
				}
			});
			</script>
		";
		
		return $xhtml;		
	}
	
}

?>