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


class Snazzware_View_Helper_Grid_Column_Header extends Zend_View_Helper_Abstract {
	
	public function Grid_Column_Header($grid, $column) {
		
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml = '';
		
		$filter = $grid->getDecoratedFilter($column->getName());
		if (isset($filter['decorated'])) $value = $filter['decorated'];
		else $value = '';
		
		if (isset($filter['op'])) $op = $filter['op'];
		else $op = 'like';
		
		$sort = $grid->getSort($column->getName());
		if (isset($sort['dir'])) $dir = $sort['dir'];
		else $dir = '';		
		
		/*
		if ($dir=='asc') $sortsymbol = '&uarr;';
		else if ($dir=='desc') $sortsymbol = '&darr;';
		else $sortsymbol = '';
		*/
		
		$uparrowurl = ConfigUtils::get('grid','icon_sortasc');
		$downarrowurl = ConfigUtils::get('grid','icon_sortdesc');
		
		if ($dir=='asc') $sortsymbol = "<img src='$uparrowurl' class='sort' />";
		else if ($dir=='desc') $sortsymbol = "<img src='$downarrowurl'  class='sort' />";
		else $sortsymbol = '';
		
		$name = str_replace('.','_',$column->getName());
		
		$filterid = $grid->getName().'-filter_'.$name;
		$sortid = $grid->getName().'-sort_'.$name;
		$sortButtonId = $grid->getName().'-sortbtn_'.$name;
		
		$styles = array();
		
		if (count($styles)>0) {
			$style = " style='".implode('; ',$styles)."' ";
		} else $style = '';
		
		
		
		$xhtml .= "<th $style>";
		$xhtml .= "<span class='{$zfext}-grid-caption' id='{$sortButtonId}'>{$sortsymbol}{$column->getCaption()}</span>";
		$xhtml .= "<br />";
		$helper = $column->getFilterHelper();
		$filterOptions = array_merge($column->getOption('filter',array()),array('id'=>$filterid,'op'=>$op,'grid'=>$grid->getName()));
		$xhtml .= $column->getView()->$helper("filter_{$name}",$value,array(),$filterOptions);
		$xhtml .= "<input type=hidden id='$sortid' name='sort_{$name}' class='{$zfext}-grid-sort' value='$dir' />";
		$xhtml .= "
		<script>
			$('#{$filterid}').keyup(function(e) {
				if (e.keyCode == 13) {
					{$zfext}_grid_apply_filters_{$grid->getName()}();
				}
			});
			$('#{$sortButtonId}').click(function(e) {
				var current = $('#{$sortid}').val();
				
				$('#{$grid->getName()}').find('.{$zfext}-grid-sort').each(function() {
			        	$(this).val('');        	
			    });
			
				if (current=='asc') {
					$('#{$sortid}').val('desc');
				} else {
					$('#{$sortid}').val('asc');
				}				
				
				{$zfext}_grid_apply_filters_{$grid->getName()}();
				
			});
		</script>
		";
		$xhtml .= '</th>';
		
		return $xhtml;		
	}
	
}
