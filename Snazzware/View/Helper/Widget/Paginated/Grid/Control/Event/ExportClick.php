<?

class Snazzware_View_Helper_Widget_Paginated_Grid_Control_Event_ExportClick extends Snazzware_View_Helper_Widget_Paginated_Pagination_Control_Event_PageChangeClick {
	
	protected function getParameters($widget) {		
		return "{export: 'csv'}";
	}
	
	public function Widget_Paginated_Grid_Control_Event_ExportClick($view, $widget) {
		return parent::helper($view, $widget);
	}	
	
}
