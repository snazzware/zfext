<?

class Snazzware_View_Helper_Widget_Paginated_Pagination_Control_Event_PageChangeClick extends Zend_View_Helper_Abstract {
	
	protected function getParameters($widget) {
		return '{}';
	}
	
	public function helper($view, $widget) {
		$xhtml = '';
		
		$zfext = __ZFEXT_PREFIX;
		
		$options = $widget->getOptions();
		$parent = $options['parent'];
		$parentOptions = $parent->getOptions();
		
		$xhtml .= "
			function() {
				{$parent->getId()}_showLoading();
				
				var parameters = {$parent->getId()}_prepare_pagination_parameters({$this->getParameters($widget)});
				
				$('#{$parent->getId()}').trigger('{$zfext}-pagination-preChange', parameters);
				
				$('#{$parent->getId()}').{$zfext}WidgetCallback('refresh',parameters,function(data) {
					$('#{$parent->getId()}').html(data);
					if ($('html').offset().top!=0) $('html, body').animate({
						scrollTop: 0
					}, 500);
				});

				$('#{$parent->getId()}').trigger('{$zfext}-pagination-postChange');
			}
		";
		
		return $xhtml;
	}
	
	public function Widget_Paginated_Pagination_Control_Event_PageChangeClick($view, $widget) {
		return $this->helper($view, $widget);
	}
	
}
