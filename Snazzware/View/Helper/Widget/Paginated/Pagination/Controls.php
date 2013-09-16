<?

class Snazzware_View_Helper_Widget_Paginated_Pagination_Controls extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated_Pagination_Controls($view, $widget, $position, $placement = 'center') {
		$xhtml = '';
		
		$options = $widget->getOptions();
		
		foreach ($widget->getPaginationControls() as $control) {
			if ($control->getOption('placement','center')==$placement) {
				$originalId = $control->getId();
				$control->setId($control->getId().'_'.$position);
				$xhtml .= $control->render($view, $control);
				$control->setId($originalId);
			}
		}
		
		return $xhtml;
	}
	
}
