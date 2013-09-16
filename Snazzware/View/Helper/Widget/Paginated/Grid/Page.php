<?

class Snazzware_View_Helper_Widget_Paginated_Grid_Page extends Snazzware_View_Helper_Widget_Paginated_Page {
	
	public function Widget_Paginated_Grid_Page($view, $widget) {
		$xhtml = '';
		
		$rowClasses = ' '.__ZFEXT_PREFIX.'-grid-data ';
		
		if ($widget->getOnClickUrl() != null) {
			$rowClasses .= ' clickable ';
		}
		
		$options = $widget->getOptions();
		
		$helper = $widget->getItemHelper();
		
		$zfext = __ZFEXT_PREFIX;
		
		// Begin Table
		$xhtml .= "<table class='{$zfext}-grid' id='{$widget->getId()}_table'>";
		
		// Column Group
		$xhtml .= "<colgroup>";
		foreach ($widget->getColumns() as $column) {
			if ($column->getOption('display',true)==true) {
				$xhtml .= $column->renderCol($widget);
			}
		}
		$xhtml .= "</colgroup>";
		
		// Table Header
		$xhtml .= "<thead>";
		$xhtml .= "<tr>";
		foreach ($widget->getColumns() as $column) {
			if ($column->getOption('display',true)==true) {
				$xhtml .= $column->renderHeader($widget);
			}
		}
		
		$xhtml .= "</tr>";
		$xhtml .= "</thead>";
		
		// Table Body
		$xhtml .= "<tbody>";
		
		$alt = '';
		foreach ($options['items'] as $item) {
			$xhtml .= $view->$helper($view, $widget, $item, array('rowClasses'=>$rowClasses, 'alt'=>$alt));
		
			if ($alt=='') $alt='alt';
			else $alt = '';
		}
		
		$xhtml .= "</tbody>";
		$xhtml .= "</table>";
		
		// Loading overlay
		/*$xhtml .= "
			<div class='<?=__ZFEXT_PREFIX;?>-grid-overlay' id='<?=$widget->getId();?>_overlay'>
				<img src='<?=ConfigUtils::get('global','image_loading');?>' class='loading' />
			</div>
		";*/
		
		
		// scripting
		
		$xhtml .= "
			<script>
			
			$(function() {
				$('#{$widget->getId()}').bind('{$zfext}-pagination-preChange', function(parameters) {
					
				});
				
				$('#{$widget->getId()}_table').sorttable({
					handle: '.{$zfext}-grid-column-handle',
					stop: function(e, ui) {
						// Determine new order of columns						
						var columnOrder = new Array();
						var columnCount = 0;
						$('#{$widget->getId()}_table').find('th').each(function(index, value) {
							columnOrder[columnCount++] = $(value).attr('colname'); 							
						});
						
						// Sort the colgroup entries to match						
						$('#{$widget->getId()}_table').find('col').sortElements(function(a, b) {
							var idxA = columnOrder.indexOf($(a).attr('name'));
							var idxB = columnOrder.indexOf($(b).attr('name'));
							console.log(idxA+' '+idxB);
							return (idxA < idxB) ? -1 : (idxA > idxB) ? 1 : 0; 
						});
				
						// Display loading overlay
						{$widget->getId()}_showLoading();
						
						// Build parameters
						var parameters = new Object();
						$(parameters).attr('order',columnOrder.join(','));						
										
						// send new column order to server
						$('#{$widget->getId()}').{$zfext}WidgetCallback('columnorder',parameters,function() {
							var parameters = {$widget->getId()}_prepare_pagination_parameters({});
							
							// Submit to server and replace widget contents with new rendering
							$('#{$widget->getId()}').{$zfext}WidgetCallback('refresh',parameters,function(data) {					
								$('#{$widget->getId()}').html(data);								
							});
						});
					}
				});
			});
			
			function {$widget->getId()}_prepare_pagination_parameters(parameters) {								
		        // pagination
				if ((parameters) && (parameters.page)) {
					$(parameters).attr('page',parameters.page);
					$(parameters).attr('pagesize',parameters.pagesize);
				} else {
					$(parameters).attr('pagesize','{$options['pagesize']}');
				}
		
			    // filtering  
				$('#{$widget->getId()}').find('.{$zfext}-grid-filter').each(function() {
					if ($(this).val().length>0) $(parameters).attr($(this).attr('name'),$(this).val());        	
				});
		
				// sorting      
				$('#{$widget->getId()}').find('.{$zfext}-grid-sort').each(function() {
					if ($(this).val().length>0) $(parameters).attr($(this).attr('name'),$(this).val());        	
				});
		
				return parameters;
				
				// dispatch and update 
				/*$('#{$widget->getId()}').{$zfext}WidgetCallback('refresh',parameters,function(data) {					
					$('#{$widget->getId()}').html(data);
					{$zfext}_grid_autoheight_{$widget->getId()}();
				});*/
			}

			function {$zfext}_grid_export_{$widget->getId()}() {
				var form = new jQuery('<form>',{
					'action': '{$options['ajaxUrl']}',
					'method': 'post',
					'target': '_blank'
				});
				
				form.append(jQuery('<input>',{
					'name': '_widgetName',
					'value': '{$widget->getName()}',
					'type': 'hidden'
				}));
				
				form.append(jQuery('<input>',{
					'name': '_widgetAction',
					'value': 'refresh',
					'type': 'hidden'
				}));
		
				// export format
				form.append(jQuery('<input>',{
					'name': 'export',
					'value': 'csv',
					'type': 'hidden'
				}));
		
				// filtering  
				$('#{$widget->getId()}').find('.{$zfext}-grid-filter').each(function() {
					if ($(this).val().length>0) {
						form.append(jQuery('<input>',{
							'name': $(this).attr('name'),
							'value': $(this).val(),
							'type': 'hidden'
						}));
					}        	
				});
		
				// sorting      
				$('#{$widget->getId()}').find('.{$zfext}-grid-sort').each(function() {
					if ($(this).val().length>0) {
						form.append(jQuery('<input>',{
							'name': $(this).attr('name'),
							'value': $(this).val(),
							'type': 'hidden'
						}));
					}       	
				});
		
				form.submit();
			}

			var {$zfext}_grid_last_autoheight_time_{$widget->getId()} = 0;
		
			function {$zfext}_grid_autoheight_{$widget->getId()}() {
		";
					
		if ($widget->getAutoResize()==true) {
			$xhtml .= "
				var currentSeconds = (new Date()).getTime();
		        	
				if (currentSeconds - {$zfext}_grid_last_autoheight_time_{$widget->getId()} > 100) {		
					{$zfext}_grid_last_autoheight_time_{$widget->getId()} = currentSeconds;
		
					var grid = $('#{$widget->getId()}');
					var parent = grid.parent();
					var toprow = grid.find('.{$zfext}-grid-data');
		
		        	if ((grid.length) && (parent.length) && (toprow.length)) {
						var parentHeight = grid.parent().height();
						var parentTop = grid.parent().offset().top;
						
			        	var myTop = grid.offset().top;
			        	var rowTop = toprow.offset().top;
			        	var myHeight = grid.height();
			        	
			        	var rowHeight = grid.find('.{$zfext}-grid-data').height();
			        	
			        	if ((parentHeight + parentTop) > $(window).height()) { // make sure we don't extend beyond the bottom of the window
			        		parentHeight = $(window).height() - parentTop;			        	
			        	}
			        	
			        	var room = parentHeight - (rowTop - parentTop);
			        	
			        	var rows = Math.floor(room / rowHeight);
		
			        	rows -= 2;
			        	
		        	} else {
		            	var rows = 5;
		        	}
			        	
		        	if (rows<5) rows = 5; // minimum of five rows
		
		        	if (Math.abs(rows-<?=$this->grid->getPageSize();?>)>1) {
			        	if (rows != <?=$this->grid->getPageSize();?>) { // if we've calculated a different optimal value, reload		        				        	
			        		{$zfext}_grid_apply_filters_{$widget->getId()}({page: <?=$this->vars['page'];?>, pagesize: rows});
				        }
		        	}		        	
		        	
				}
			";
		}
		$xhtml .= "
			}
        
			$('#{$zfext}-content').bind('{$zfext}-content-resize', function() {		        	
				{$zfext}_grid_autoheight_{$widget->getId()}();
			});
		        
			function {$zfext}_grid_clear_filters_{$widget->getId()}() {	        
				$('#{$widget->getId()}').find('.{$zfext}-grid-filter').each(function() {
					$(this).val('');        	
				});
		        
				{$zfext}_grid_apply_filters_{$widget->getId()}();
				{$zfext}_grid_autoheight_{$widget->getId()}();
			}
        
			$(function() {
				var colElement, colWidth, originalSize, newColWidth;
				var nextElement, nextWidth, nextOriginalSize, nextNewWidth;
		        
				//{$zfext}_grid_autoheight_{$widget->getId()}();
		
		        // the code below is based on an online tutorial for resizable table columns using jquery	
				$('#{$widget->getId()}_table th:not(:last-child)').resizable({
					handles: 'e',
					minWidth: 50,
		
					// set correct COL element and original size
					start: function(event, ui) {
						var colIndex = ui.helper.index() + 1;
						colElement = $('#{$widget->getId()}_table').find('colgroup > col:nth-child(' + colIndex + ')');
		
						nextElement = $('#{$widget->getId()}_table').find('colgroup > col:nth-child(' + (colIndex+1) + ')');
						
						// get col width (faster than .width() on IE)
						colWidth = parseInt(colElement.get(0).style.width, 10);
						originalSize = ui.size.width;				  
		
						nextWidth = parseInt(nextElement.get(0).style.width, 10);				
					},
						
					// set COL width
					resize: function(event, ui) {
						var resizeDelta = ui.size.width - originalSize;
		
						
						newColWidth = colWidth + (colWidth * (resizeDelta/originalSize));
						nextNewWidth = nextWidth - (colWidth * (resizeDelta/originalSize));
		
						if (newColWidth<2) newColWidth = 2;
						if (nextNewWidth<2) nextNewWidth = 2;
		
						colElement.width(newColWidth+'%');
						nextElement.width(nextNewWidth+'%');				
						
						// height must be set in order to prevent IE9 to set wrong height
						$(this).css('height', 'auto');
					},
		        		
					stop: function(event, ui) {
						var parameters = new Object();
				
						$(parameters).attr('colwidth_'+colElement.attr('name'),newColWidth);
						$(parameters).attr('colwidth_'+nextElement.attr('name'),nextNewWidth);
										
						// send width to server
						$('#{$widget->getId()}').{$zfext}WidgetCallback('columnresize',parameters,function() {
							//
						});
					}
				});
			});
		</script>	
		";
		
		return $xhtml;
	}
	
}
