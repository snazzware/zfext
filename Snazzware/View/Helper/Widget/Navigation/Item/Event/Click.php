<?

class Snazzware_View_Helper_Widget_Navigation_Item_Event_Click extends Zend_View_Helper_Abstract {
	
	protected function getUrl($widget) {
		$options = $widget->getOptions();
		$url = $widget->getOption('url','');
		if ($url == '') {
			$url = ConfigUtils::get('global','baseUrl')."/{$options['module']}/{$options['controller']}/{$options['action']}";
		}
		return $url;
	}
	
	public function Widget_Navigation_Item_Event_Click($view, $widget) {				
		$url = $this->getUrl($widget);
		
		return "
			function(event) {
				event.stopPropagation();				
				window.location = '$url';				
			}
		";
	}
	
}
