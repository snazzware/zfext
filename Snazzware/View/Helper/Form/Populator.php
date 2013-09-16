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

/*********
 * 
 * Depends on jquery and fancybox!
 * 
 */

class Snazzware_View_Helper_Form_Populator extends Zend_View_Helper_FormElement {
	
	public function Form_Populator($name, $value = null, $attribs = null, $options = null) {
		$zfext = __ZFEXT_PREFIX;
		
		if ($options['readonly']===true) {
			return '';
		} else {
			$xhtml = '';
			
			if (isset($options['items'])) {
				$items = $options['items'];
				
				if (isset($options['caption'])) $caption = $options['caption'];
				else $caption = 'Select from List';
				
				if ((isset($options['listing_ajax_popup'])) && ($options['listing_ajax_popup'])) $popup = true;
				else $popup = false;
				
				if (isset($options['listing_ajax_action'])) {
					$action = $options['listing_ajax_action'];
				} else {					
					if ($popup) $action = 'popuplisting';
					else $action = 'embeddedlisting';
				}				
				
				$embeddedListingUrl = $options['listing_ajax_url'].'/'.$action;
				
				$populateScript = '';
				foreach ($items as $itemname => $item) {								
					$populateScript .= $item->renderScripting();				
				}
				
				if (isset($options['icon'])) {
					$icon = "<img src='{$options['icon']}' />";
				} else $icon = '';
				
				$xhtml .= "
					<div id='{$zfext}-formpopulator-select-{$name}' class='{$zfext}-form-button {$zfext}-formpopulator-button'>{$icon}{$caption}</div>
				
					<script>				
						function {$zfext}_formpopulator_listing_select_callback_{$name}(entity) {
							$('#{$name}-element').trigger('{$zfext}-onChange', this);												
							{$populateScript}	
							{$zfext}_globalFormIsDirty = true;
							$('#{$name}-element').trigger('{$zfext}-postChange', this);				
						}
				";
							
				if ($popup==true) {
					$xhtml .= "
												$('#{$zfext}-formpopulator-select-{$name}').click(function() {
													var width = screen.width * 0.75;
													var height = screen.height * 0.60;
													var left = (screen.width/2)-(width/2);
													var top = (screen.height/2)-(height/2);
													window.open('{$embeddedListingUrl}?targetname={$name}&callback={$zfext}_formpopulator_listing_select_callback_{$name}&oneshot=true',
														'{$zfext}-formpopulator-listing-add-{$name}',
														'location=no,menubar=no,resizable=yes,status=no,titlebar=yes,toolbar=no,left='+left+',top='+top+',width='+width+',height='+height
													);								
												});	
																
											";
					
					
				} else {
					$xhtml .= "
									
											$('#{$zfext}-formpopulator-select-{$name}').click(function() {
												$.fancybox(
													'',
													{
														'href': '{$embeddedListingUrl}?targetname={$name}&callback={$zfext}_formpopulator_listing_select_callback_{$name}',
											        	'autoDimensions'	: false,
														'width'         	: '90%',
														'height'        	: '75%',
														'minHeight'        	: '75%',
														'transitionIn'		: 'none',
														'transitionOut'		: 'none',
														'scrolling' 		: 'no'									
													}
												);
											});
										";
				}
				$xhtml .= "
					</script>
				";
			}
			
			return $xhtml;
		}
	}
	
}
