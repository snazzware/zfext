<?

class Snazzware_View_Helper_Widget_Paginated_Item extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated_Item($view, $widget, $item) {
		$xhtml = '';
		
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml .= "<div class='{$zfext}-widget-paginated-item {$zfext}-widget-paginated-{$widget->getName()}'>";		
		$xhtml .= "<pre>".print_r($item,true)."</pre>";				
		$xhtml .= "</div>";
		
		return $xhtml;
	}
	
}
