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

class Snazzware_Form_Element_Matrix_Column_Formatted extends Snazzware_Form_Element_Matrix_Column {
		
	protected $mFormHelper = 'Form_Text';

	// render javascript to be executed when field is newly created
	public function renderFieldScript($view = null, $options = array()) {
		$xhtml = '';		

		$columnOptions = $this->getOptions();
		
		if (isset($columnOptions['format'])) $numberformat = $columnOptions['format'];
		else $numberformat = '#';
		
		$xhtml .= "
			$(\"#{$options['id']}\").bind('keydown', function(event, source) {
				if (event.keyCode == '13') {
					var currentValue = 	$(this).val();
					if (isNaN(currentValue)) currentValue = 0;

					var formatted = $.formatNumber(currentValue, {format:'{$numberformat}', locale: 'us'});
				
					$(this).val(formatted);
				}			
			});
		
			$(\"#{$options['id']}\").blur(function(){			
				var currentValue = 	$(this).val();
				if (isNaN(currentValue)) currentValue = 0;

				var formatted = $.formatNumber(currentValue, {format:'{$numberformat}', locale: 'us'});
				
				$(this).val(formatted);			
			});
			
			
		";
		
		return $xhtml;
	}
}

?>