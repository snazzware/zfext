<?

class Snazzware_View_Helper_Widget_Paginated_Empty extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated_Empty($view, $widget) {
		$xhtml = '';
		
		$emptyCaption = $widget->getOption('emptyCaption','No items were found.');
		
		$xhtml .= "<h2 class='empty'>$emptyCaption</h2>";
		
		return $xhtml;
	}
	
}
