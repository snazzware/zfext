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


class Snazzware_View_Helper_Widget_Paginated_Grid_Column_Header extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated_Grid_Column_Header($grid, $column) {		
		$xhtml = '';
		$zfext = __ZFEXT_PREFIX;
		
		// Load some environmental configuration
		$uparrowurl = ConfigUtils::get('grid','icon_sortasc');
		$downarrowurl = ConfigUtils::get('grid','icon_sortdesc');
		
		// Get current filter settings from parent
		$filter = $grid->getDecoratedFilter($column->getName());
		if (isset($filter['decorated'])) $value = $filter['decorated'];
		else $value = '';
		
		if (isset($filter['op'])) $op = $filter['op'];
		else $op = 'like';
		
		// Get current sort settings from parent
		$sort = $grid->getSort($column->getName());
		if (isset($sort['dir'])) $dir = $sort['dir'];
		else $dir = '';		
		
		if ($dir=='asc') $sortsymbol = "<img src='$uparrowurl' class='sort' />";
		else if ($dir=='desc') $sortsymbol = "<img src='$downarrowurl'  class='sort' />";
		else $sortsymbol = '';
		
		// Sanitize column name for rendering
		$name = str_replace('.','_',$column->getName());
		
		// Generate unique ids for some sub-elements
		$filterid = $grid->getName().'-filter_'.$name;
		$sortid = $grid->getName().'-sort_'.$name;
		$sortButtonId = $grid->getName().'-sortbtn_'.$name;

		// Begin render column header
		$xhtml .= "<th colname='{$column->getName()}'>";
		$xhtml .= "<div class='{$zfext}-grid-column-handle'></div>";
		$xhtml .= "<span class='{$zfext}-grid-caption' id='{$sortButtonId}'>{$sortsymbol}{$column->getCaption()}</span>";
		$xhtml .= "<br />";
		
		// Render column filter
		$helper = $column->getFilterHelper();
		$filterOptions = array_merge($column->getOption('filter',array()),array('id'=>$filterid,'op'=>$op,'grid'=>$grid->getName()));
		$xhtml .= $column->getView()->$helper("filter_{$name}",$value,array(),$filterOptions);
		
		// Render hidden field for column sort direction
		$xhtml .= "<input type=hidden id='$sortid' name='sort_{$name}' class='{$zfext}-grid-sort' value='$dir' />";
		
		// Finish render column header
		$xhtml .= '</th>';
		
		// Rendering scripting
		$grid->appendInnerScript("			
			$('#{$filterid}').keyup(function(e) {
				if (e.keyCode == 13) { // Was the enter key pressed?
					// Prepare array of current sorts/filters/etc.
					var parameters = {$grid->getId()}_prepare_pagination_parameters({});

					// Display loading overlay
					{$grid->getId()}_showLoading();
					
					// Submit to server and replace widget contents with new rendering
					$('#{$grid->getId()}').{$zfext}WidgetCallback('refresh',parameters,function(data) {					
						$('#{$grid->getId()}').html(data);
						{$zfext}_grid_autoheight_{$grid->getId()}();
					});
				}
			});
			$('#{$sortButtonId}').click(function(e) {
				// Save current sort direction for this column, if any
				var current = $('#{$sortid}').val();
				
				// Clear all other column sorts
				$('#{$grid->getName()}').find('.{$zfext}-grid-sort').each(function() {
			        	$(this).val('');        	
			    });
			
			    // Flip sort direction
				if (current=='asc') $('#{$sortid}').val('desc');
				else $('#{$sortid}').val('asc');					
				
				// Prepare array of current sorts/filters/etc.
				var parameters = {$grid->getId()}_prepare_pagination_parameters({});
				
				// Display loading overlay
				{$grid->getId()}_showLoading();
				
				// Submit to server and replace widget contents with new rendering					
				$('#{$grid->getId()}').{$zfext}WidgetCallback('refresh',parameters,function(data) {					
					$('#{$grid->getId()}').html(data);
					{$zfext}_grid_autoheight_{$grid->getId()}();
				});					
			});			
		");
		
		return $xhtml;		
	}
	
}
