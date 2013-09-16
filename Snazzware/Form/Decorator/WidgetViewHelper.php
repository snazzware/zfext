<?php 

class Snazzware_Form_Decorator_WidgetViewHelper extends Zend_Form_Decorator_ViewHelper {
	
	public function render($content) {
		$element = $this->getElement();
		$view = $element->getView();
		$widget = $element->getWidget();
		
		$separator = $this->getSeparator();
		
		$elementContent = $widget->render($view);
		
		switch ($this->getPlacement()) {
			case self::APPEND:
				return $content . $separator . $elementContent;
			case self::PREPEND:
				return $elementContent . $separator . $content;
			default:
				return $elementContent;
		}
	}
	
}