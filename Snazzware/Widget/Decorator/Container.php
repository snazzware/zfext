<?php 

class Snazzware_Widget_Decorator_Container extends Snazzware_Widget_Decorator_Abstract {
	
	public function render($view,$widget,&$content) {		
		$id = $widget->getId();
		$class = $widget->getOption('class','');
		$style = $widget->getOption('style','');
		$name = $widget->getOption('name',$widget->getId());
		$ajaxUrl = $widget->getOption('ajaxUrl','');
		
		
		$zfext = __ZFEXT_PREFIX;
		
		if ($widget->getOption('renderContainer',true)==true) {			
			$openTag = "<div id='$id' class='$class' name='$name' style='$style' ajaxUrl='$ajaxUrl' >";
			$closeTag = "</div>";
			$script = $widget->getOption('innerScript','') . $widget->getOption('outerScript','');
		} else {
			$openTag = '';
			$closeTag = '';
			$script = $widget->getOption('innerScript','');
		}
		
		$script .= $widget->getOption('script','');
		
		$content = "{$openTag}{$content}{$closeTag}<script>{$script}</script>";
	}
	
}


?>