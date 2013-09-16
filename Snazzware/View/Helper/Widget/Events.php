<?

class Snazzware_View_Helper_Widget_Events extends Zend_View_Helper_Abstract {
	
	public function Widget_Events($view, $widget) {
		$xhtml = '';
		
		$zfext = __ZFEXT_PREFIX;
		
		$events = $widget->getOption('events');		
				
		foreach ($events as $eventName=>$event) {
			$script = '';
			
			if (isset($event['script'])) {
				$script = $event['script'];
			} else 
			if (isset($event['helper'])) {
				$scriptHelper = $event['helper'];
				$script = $view->$scriptHelper($view, $widget);
			}
			
			$xhtml .= "
				<script>
					$('#{$widget->getId()}').bind('{$eventName}',$script);
				</script>
			";
		}
		
		return $xhtml;
	}
	
}
