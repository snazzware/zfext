<?php 

class Snazzware_Widget_Decorator_HtmlTag extends Snazzware_Widget_Decorator_Abstract {
	
	public function render($view,$widget,&$content) {
		$tag = $this->getOption('tag','div');
		$id = $this->getOption('id','');
		$class = $this->getOption('class','');
		$style = $this->getOption('style','');
		
		$openTag = "<{$tag} id='$id' class='$class' style='$style'>";
		$closeTag = "</{$tag}>";
		
		$content = "{$openTag}{$content}{$closeTag}";
	}
	
}


?>