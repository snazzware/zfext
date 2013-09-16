<?

class Snazzware_View_Helper_Widget_Paginated_Pagination extends Zend_View_Helper_Abstract {
	
	public function Widget_Paginated_Pagination($view, $widget, $position = 'top') {
		$xhtml = '';
		
		$options = $widget->getOptions();
		
		$controlsHelper = $widget->getPaginationControlsHelper();
		
		$controls['center'] = $view->$controlsHelper($view, $widget, $position);
		$controls['right'] = $view->$controlsHelper($view, $widget, $position, 'right');
		
		$zfext = __ZFEXT_PREFIX;
		
		$xhtml .= "
			<table id='{$widget->getId()}_pagination' class='{$zfext}-widget-paginated-pagination {$zfext}-widget-paginated-pagination-{$position} {$zfext}-widget-paginated-pagination-{$widget->getName()}'>
				<tr>
					<td class='pagenumbers'>
						<span class='caption'>{$options['caption']}</span>
						Page {$options['page']} of {$options['pages']}		
					</td>
					<td class='controls'>
						{$controls['center']}
					</td>
					<td class='itemcounts'>
						{$controls['right']}<br clear=all />					
						Showing {$options['itemstart']} thru {$options['itemend']} of {$options['itemcount']} total
					</td>
				</tr>				
			</table>
		";
			
		if ($widget->getOption('stickyControls',false)==true) {
			$xhtml .= "		
				<script>
					var {$widget->getId()}_pagination_top = 0;
					
					$(function() {
						{$widget->getId()}_pagination_top = $('#{$widget->getId()}_pagination').offset().top;
						
						$(window).scroll(function() {							
							$('#{$widget->getId()}_pagination').toggleClass('sticky',$(window).scrollTop()>{$widget->getId()}_pagination_top);
						});
					});
				</script>
			";
		}
		
		return $xhtml;
	}
	
}
