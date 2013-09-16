<?

class Snazzware_View_Helper_Widget_Paginated_Grid_Control_Event_ResetFiltersClick extends Snazzware_View_Helper_Widget_Paginated_Pagination_Control_Event_PageChangeClick {
	
	protected function getParameters($widget) {		
		return "{resetFilters: 1}";
	}
	
	public function Widget_Paginated_Grid_Control_Event_ResetFiltersClick($view, $widget) {
		return parent::helper($view, $widget);
	}	
	
}
