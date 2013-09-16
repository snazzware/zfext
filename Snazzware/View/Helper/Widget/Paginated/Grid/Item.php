<?

class Snazzware_View_Helper_Widget_Paginated_Grid_Item extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated_Grid_Item($view, $widget, $item, $options) {
		$xhtml = '';
		
		$zfext = __ZFEXT_PREFIX;
		
		if ($widget->getOnClick() != null) {
			$onClick = $widget->processTokens($widget->getOnClick(),$item);
		} else
			if ($widget->getOnClickUrl() != null) {
			$onClickUrl = $widget->processTokens($widget->getOnClickUrl(),$item);
			$onClick = 'window.location.href="'.$onClickUrl.'"';
		} else {
			$onClick = '';
		}
		 
		$highlight = $widget->getRowHighlight($item);
	
		$xhtml .= "<tr onclick='{$onClick}' class='{$options['rowClasses']} {$options['alt']} {$highlight}'>";
		foreach ($widget->getColumns() as $column) {
			if ($column->getOption('display',true)==true) {
				$xhtml .= $column->renderData($item);
			}
		}
		$xhtml .= '</tr>';
		
		return $xhtml;
	}
	
}
