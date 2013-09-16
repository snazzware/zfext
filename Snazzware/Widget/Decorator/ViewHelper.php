<?php 

class Snazzware_Widget_Decorator_ViewHelper extends Snazzware_Widget_Decorator_Abstract {
	
	public function render($view,$widget,&$content) {
		$helper = $widget->getHelper();
		
		$content = $view->$helper($view,$widget);			
	}
	
}


?>