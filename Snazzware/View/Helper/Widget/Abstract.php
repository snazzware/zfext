<?php 

class Snazzware_View_Helper_Widget_Abstract extends Zend_View_Helper_Abstract {

	static protected $mSharedStylesRendered = array();
	
	static protected $mSharedScriptingRendered = array();
	
	static protected function renderSharedScripting($helper, $view, $widget) {
		$xhtml = '';
	
		if (!isset(self::$mSharedScriptingRendered[get_class($helper)])) {		
			self::$mSharedScriptingRendered[get_class($helper)] = true;
				
			$xhtml .= '<script>';
			$xhtml .= $helper->sharedScripting($view, $widget);
			$xhtml .= '</script>';
		}
	
		return $xhtml;
	}
	
	static protected function renderSharedStyles($helper, $view, $widget) {
		$xhtml = '';
	
		if (!isset(self::$mSharedStylesRendered[get_class($helper)])) {
			self::$mSharedStylesRendered[get_class($helper)] = true;
	
			$xhtml .= '<style>';
			$xhtml .= $helper->sharedStyles($view, $widget);
			$xhtml .= '</style>';
		}
	
		return $xhtml;
	}
	
	/**
	 * Override this function in your widget helpers to render any javascript which is shared between all
	 * instances of your widget on the page.
	 * 
	 * @param unknown $view
	 * @param unknown $widget
	 * @return string
	 */
	protected function sharedScripting($view, $widget) {
		return ''; // STUB
	}
	
	/**
	 * Override this function in your widget helpers to render any CSS which is shared between all
	 * instances of your widget on the page.
	 *
	 * @param unknown $view
	 * @param unknown $widget
	 * @return string
	 */
	protected function sharedStyles($view, $widget) {
		return ''; // STUB
	}
	
}

?>