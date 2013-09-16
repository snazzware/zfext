<?

class Snazzware_View_Helper_Widget_Paginated_Grid_Control_Event_CustomizeClick extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated_Grid_Control_Event_CustomizeClick($view, $widget) {
		$xhtml = '';
		
		$options = $widget->getOptions();
		$parent = $options['parent'];
		$parentOptions = $parent->getOptions();
		
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml = "
			function () {
				var url = '{$parentOptions['customizeUrl']}';
				url += (/\?/.test(url) ? '&' : '?') + '{$zfext}-crud-postpersist-redirect=' + encodeURIComponent(window.location);
				window.location = url;
			}
		";
		
		return $xhtml;
	}	
	
}
