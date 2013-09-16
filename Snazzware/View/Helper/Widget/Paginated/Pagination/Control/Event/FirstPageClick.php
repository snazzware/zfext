<?

class Snazzware_View_Helper_Widget_Paginated_Pagination_Control_Event_FirstPageClick extends Snazzware_View_Helper_Widget_Paginated_Pagination_Control_Event_PageChangeClick {
	
	protected function getParameters($widget) {
		return '{page: 1}';
	}
	
	public function Widget_Paginated_Pagination_Control_Event_FirstPageClick($view, $widget) {		
		return parent::helper($view, $widget);		
	}
	
}
