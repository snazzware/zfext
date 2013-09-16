<?

class Snazzware_View_Helper_Widget_Paginated_Pagination_Control_Event_NextPageClick extends Snazzware_View_Helper_Widget_Paginated_Pagination_Control_Event_PageChangeClick {

	protected function getParameters($widget) {
		$options = $widget->getOptions();
		$parent = $options['parent'];
		$parentOptions = $parent->getOptions();

		return "{page: {$parentOptions['nextpage']}}";
	}

	public function Widget_Paginated_Pagination_Control_Event_NextPageClick($view, $widget) {
		return parent::helper($view, $widget);
	}

}
