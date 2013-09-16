<?

class Snazzware_View_Helper_Widget_Paginated_Pagination_Control extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated_Pagination_Control($view, $widget) {
		$xhtml = '';
		
		$zfext = __ZFEXT_PREFIX;
		
		$options = $widget->getOptions();
		$parent = $options['parent'];
		
		$xhtml .= "<div class='{$zfext}-widget-paginated-pagination-control {$zfext}-widget-paginated-pagination-control-{$widget->getName()}' id='{$widget->getId()}'>";
		switch ($widget->getOption('iconPlacement','top')) {
			case 'left':
				$xhtml .= "
					<img src='{$options['icon']}' style='vertical-align: middle; padding-right: 4px;'/>{$options['caption']}
				";
			break;
			default:
				$xhtml .= "
					<img src='{$options['icon']}' /><BR />{$options['caption']}		
				";
			break;
		}
		$xhtml .= "</div>";		
		
		return $xhtml;
	}
	
}
