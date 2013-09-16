<?

class Snazzware_View_Helper_Widget_Paginated_Page extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated_Page($view, $widget) {
		$xhtml = '';
		
		$options = $widget->getOptions();
		
		$helper = $widget->getItemHelper();
		$emptyHelper = $widget->getEmptyHelper();
		
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml .= "<div class='{$zfext}-widget-paginated-page {$zfext}-widget-paginated-page-{$widget->getName()}'>";
		if (count($options['items'])>0) {	
			foreach ($options['items'] as $item) {
				$xhtml .= $view->$helper($view, $widget, $item);
			}		
		} else {
			$xhtml .= $view->$emptyHelper($view, $widget);
		}
		$xhtml .= "</div>";
		
		$xhtml .= "
			<script>
				function {$widget->getId()}_prepare_pagination_parameters(opt) {
					return opt;
				}
			</script>
		";
		
		return $xhtml;
	}
	
}
