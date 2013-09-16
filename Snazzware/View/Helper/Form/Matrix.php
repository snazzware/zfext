<?php 
/**
 * Snazzware Extensions for the Zend Framework 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.snazzware.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to josh@snazzware.com so we can send you a copy immediately.
 *
 * @category   Snazzware
 * @copyright  Copyright (c) 2011-2012 Josh M. McKee
 * @license    http://www.snazzware.com/license/new-bsd     New BSD License
 */

/*********
 * 
 * Depends on jquery and fancybox!
 * 
 */

class Snazzware_View_Helper_Form_Matrix extends Zend_View_Helper_FormElement {
	
	const default_minrows = 5;
	
	private $_sortableColumn = '';
	
	public function sort($a, $b) {		
		if ($a[$this->_sortableColumn]>$b[$this->_sortableColumn]) return 1;
		if ($a[$this->_sortableColumn]<$b[$this->_sortableColumn]) return -1;
		if ($a[$this->_sortableColumn]==$b[$this->_sortableColumn]) return 0;
	}
	
	public function Form_Matrix($name, $value = null, $attribs = null, $options = null) {
		$zfext = __ZFEXT_PREFIX;
		
		$options = array_merge( // default options
			array(
				'sortable'=>false,
				'deletable'=>false,
				'allow_insert_row'=>true,
				'onClickUrl'=>'',
				'caption'=>''
			),
			$options
		); 
		
		if (isset($options['listing_ajax_popup']) && ($options['listing_ajax_popup']==true)) $popup = true;
		else $popup = false;		
		
		if ((isset($options['readonly']) && $options['readonly']===true) || (isset($options['editable']) && $options['editable']===false)) {
			$editable = false;
		} else $editable = true;
		
		if (isset($options['sortable_column'])) $sortableColumn = $options['sortable_column'];
		else $sortableColumn = '';
				
		$xhtml = '';
		
		if (isset($options['columns'])) {
			$columns = $options['columns'];
			
			// drag handle
			if ($options['sortable']===true) {
				$dragHandle = null;
				foreach ($columns as $colname=>$column) {
					if ($column instanceof Snazzware_Form_Element_Matrix_Column_DragHandle) {
						$dragHandle = $column;
					}					
				}
				if ($dragHandle == null) {
					$dragHandle = new Snazzware_Form_Element_Matrix_Column_DragHandle('_draghandle');
					$columns['_draghandle'] = $dragHandle;
				}
			}
			
			// delete button
			if ($options['deletable']===true) {
				$deleteButton = null;
				foreach ($columns as $colname=>$column) {
					if ($column instanceof Snazzware_Form_Element_Matrix_Column_DeleteButton) {
						$deleteButton = $column;						
					}					
				}
				if ($deleteButton == null) {
					$deleteButton = new Snazzware_Form_Element_Matrix_Column_DeleteButton('_deleted');
					if (isset($options['deletion_ajax_url'])) {
						$deleteButton->setDeletionAjaxUrl($options['deletion_ajax_url']);
					}
					$columns = array('_deleted' => $deleteButton) + $columns;					
				}
				$deleteButtonName = $deleteButton->getName();
			}			
			
			// determine current number of rows
			if (is_array($value)) {
				$numrows = count($value); // subtracting one to skip master row
			} else {
				$numrows = (isset($attribs['minrows'])) ? $attribs['minrows'] : self::default_minrows;
			}
			
			if ($editable) {
				if (isset($options['listing_ajax_url'])) {
					if ($popup==true) {
						$embeddedListingUrl = $options['listing_ajax_url'].'/popuplisting';
					} else {
						$embeddedListingUrl = $options['listing_ajax_url'].'/embeddedlisting';
					}
					
					if (isset($options['listing_ajax_caption'])) $addFromListingCaption = $options['listing_ajax_caption'];
					else $addFromListingCaption = 'Add from List';
					
					$populateScript = '';
					foreach ($columns as $colname => $column) {
						$columnOptions = $column->getOptions();
						if (isset($columnOptions['listing_add_populate_from'])) {							
								$populateScript .= "
									if (entity.{$columnOptions['listing_add_populate_from']}) {
										$('#{$name}-'+{$zfext}_formgrid_rowcount_{$name}+'-{$colname}').val(entity.{$columnOptions['listing_add_populate_from']});
									}
								";							
						}
					}
					
					$xhtml .= "
						<div id='{$zfext}-formgrid-listing-add-{$name}' class='{$zfext}-form-button {$zfext}-formgrid-button'>{$addFromListingCaption}</div>
					
						<script>						
						
						function {$zfext}_formgrid_listing_add_callback_{$name}(entity) {
							$('#{$zfext}-formgrid-master-{$name}').clone().find('input').each(function() {
								$(this).attr('id', function(_, id) { if (id) return id.replace('-x-','-'+{$zfext}_formgrid_rowcount_{$name}+'-') }).attr('name', function(_, name) { return name.replace('[x]','['+{$zfext}_formgrid_rowcount_{$name}+']') });
								if (entity.readonly) $(this).attr('readonly',true);
							}).end().removeAttr('id').attr('class','{$zfext}-formgrid-row').appendTo('#{$zfext}-formgrid-table-{$name}');
							
							{$zfext}_formgrid_fieldscripts_{$name}({$zfext}_formgrid_rowcount_{$name});
							
							{$populateScript}
							
							{$zfext}_globalFormIsDirty = true;
							
							{$zfext}_formgrid_rowcount_{$name}++;

							{$zfext}_formgrid_bind_events_{$name}();	

							{$zfext}_formgrid_index_sortables_{$name}();
						}";
							
					if ($popup==true) {
						$xhtml .= "
							$('#{$zfext}-formgrid-listing-add-{$name}').click(function() {
								var width = screen.width * 0.75;
								var height = screen.height * 0.60;
								var left = (screen.width/2)-(width/2);
								var top = (screen.height/2)-(height/2);
								window.open('{$embeddedListingUrl}?targetname={$name}&callback={$zfext}_formgrid_listing_add_callback_{$name}',
									'{$zfext}-formgrid-listing-add-{$name}',
									'location=no,menubar=no,resizable=yes,status=no,titlebar=yes,toolbar=no,left='+left+',top='+top+',width='+width+',height='+height
								);								
							});	
											
						";
					} else {
						$xhtml .= "
							$('#{$zfext}-formgrid-listing-add-{$name}').click(function() {
								$.fancybox(
									'',
									{
										'href': '{$embeddedListingUrl}?targetname={$name}&callback={$zfext}_formgrid_listing_add_callback_{$name}',
							        	'autoDimensions'	: false,
										'width'         	: 'auto',
										'height'        	: 'auto',
										'transitionIn'		: 'none',
										'transitionOut'		: 'none'
									}
								);
							});	
											
						";
					}
					
					$xhtml .= "</script>";
				}
			}
			
			// begin table
			$xhtml .= "<table id='{$zfext}-formgrid-table-{$name}' class='{$zfext}-formgrid' style='width: {$options['width']}'>";
			
			// column groups
			$xhtml .= "<colgroup>";
			
			foreach ($columns as $colname => $column) {
				$tempOptions = $column->getOptions();
				if (isset($tempOptions['width'])) $width = $tempOptions['width'];
				else $width = 0;
				/*else {
					if (($column->getName() != 'id') && ($column->getName() != 'catalogitem')) {
						$width = 9;
					} else $width = 0;
				}*/
				
				$xhtml .= "<col name='{$column->getName()}' />";				
			}
			$xhtml .= "</colgroup>";
			
			// table body
			$xhtml .= "<tbody class='{$zfext}-formgrid-content'>";
			
			// caption row
			if ($options['caption']!='') {
				$colspan = count($columns);
				$xhtml .= "<tr id='{$zfext}-formgrid-caption-{$name}' class='{$zfext}-formgrid-caption'>";			
				$xhtml .= "<td id='{$zfext}-formgrid-caption-{$name}' class='{$zfext}-formgrid-caption' colspan='{$colspan}'>";
				$xhtml .= $options['caption'];
				$xhtml .= '</td>';
				$xhtml .= '</tr>';
			}
			
			// header row
			$xhtml .= "<tr id='{$zfext}-formgrid-header-{$name}' class='{$zfext}-formgrid-header'>";			
			foreach ($columns as $colname => $column) {
				$columnOptions['editable'] = $editable;
				
				$xhtml .= $column->renderHeader($this->view, $columnOptions);
			}
			$xhtml .= '</tr>';
			
			$columnOptions['matrix_name'] = $name;
			
			// master row
			$xhtml .= "<tr id='{$zfext}-formgrid-master-{$name}' class='{$zfext}-formgrid-master'>";
			foreach ($columns as $colname => $column) {				
				$columnOptions['id'] = "{$name}[x][{$colname}]";									
				$columnOptions['editable'] = true;

				$coloptions = array_merge($columnOptions, $column->getOptions());
				
				$xhtml .= $column->renderCell($this->view, $coloptions);
			} 	
			$xhtml .= '</tr>';
			
			// data row(s)
			$i = 0;
			if ($sortableColumn != '') {
				$this->_sortableColumn = $sortableColumn;
				uasort($value, array($this,'sort'));
			}
			
			foreach ($value as $key=>$row) {				
				if ($key !== 'x') {
					
					if ((!isset($options['deleted_flag_name'])) || (isset($options['deleted_flag_name']) && ($row[$options['deleted_flag_name']]!=$options['deleted_flag_value']))) { 					
						if ($options['onClickUrl']!='') {
							$rowclass = ' clickable ';
							$onclick = "onclick=\"window.location.href='".$this->processTokens($options['onClickUrl'],$row)."'\"";	
						} else {
							$onclick = '';
							$rowclass = '';	
						}
						
						$xhtml .= "<tr class=\"{$zfext}-formgrid-row {$rowclass}\" {$onclick}>";
						
						foreach ($columns as $colname => $column) {						
							
							if (isset($row[$colname])) $currvalue = $row[$colname];
							else $currvalue = '';
													
							$columnOptions['id'] = "{$name}[$i][{$colname}]";
							$columnOptions['editable'] = $editable;
							$columnOptions['value'] = $currvalue;						
							
							$coloptions = $column->getOptions();
							if (isset($coloptions['value'])) unset($coloptions['value']);
													
							$isValid = $column->isValid($currvalue);
							if (!$isValid) {
								$columnOptions['errors'] = $column->getErrors();
							} else unset($columnOptions['errors']);
							
							$coloptions = array_merge($columnOptions,$coloptions);						
							
							$xhtml .= $column->renderCell($this->view, $coloptions);
						}
						
						$xhtml .= '</tr>';
						$i++;	
					}		
				}
			}
			
			// end table
			$xhtml .= "</tbody>";
			$xhtml .= "</table>";

			if (isset($options['widgets'])) {
				$widgetOptions['matrix_name'] = $name;
				
				foreach ($options['widgets'] as $widgetname=>$widget) {
					$xhtml .= $widget->render($this->view, $widgetOptions);
				}
			}
			
			// button: add row
			if ($editable) {
				
				if ($options['allow_insert_row']===true) {
					$xhtml .= "
						<div id='{$zfext}-formgrid-addrow-{$name}' class='{$zfext}-form-button {$zfext}-formgrid-button'>new row</div>
						
						<script>
							$('#{$zfext}-formgrid-addrow-{$name}').click(function() {
								$('#{$zfext}-formgrid-master-{$name}').clone().find('input, select').each(function() {
									$(this).val('').attr('id', function(_, id) { return id.replace('-x-','-'+{$zfext}_formgrid_rowcount_{$name}+'-') }).attr('name', function(_, name) { return name.replace('[x]','['+{$zfext}_formgrid_rowcount_{$name}+']') });
								}).end().removeAttr('id').attr('class','{$zfext}-formgrid-row').appendTo('#{$zfext}-formgrid-table-{$name}');
								
								{$zfext}_formgrid_fieldscripts_{$name}({$zfext}_formgrid_rowcount_{$name});
								
								{$zfext}_globalFormIsDirty = true;
								
								{$zfext}_formgrid_rowcount_{$name}++;
								
								{$zfext}_formgrid_bind_events_{$name}();
								
								{$zfext}_formgrid_index_sortables_{$name}();
							});
						</script>
					";
				}
				
				$fieldscripts = '';
				
				foreach ($columns as $colname=>$column) {
					$columnOptions['id'] = "{$name}-\"+row+\"-{$colname}";
					$fieldscript = $column->renderFieldScript($this->view,$columnOptions);
					if ($fieldscript != '') {
						$fieldscripts .= "{$fieldscript}\r\n";
					} 
				}
				
				$xhtml .= "
											
					<script>
						var {$zfext}_formgrid_rowcount_{$name} = $i;
						
						function {$zfext}_formgrid_index_sortables_{$name}() {
							var count = 0;	
							$('#{$zfext}-formgrid-table-{$name}').find('input').each(function() {								
								if ($(this).attr('colname')=='{$sortableColumn}') {
									$(this).val(count);
									count++;	
								}								
							});													
						}						
						
						function {$zfext}_formgrid_fieldscripts_{$name}(row) {
							
							{$fieldscripts}	
						}
						
						function {$zfext}_formgrid_deleterow_{$name}(row) {
							
							
							$(row).children('.delete').find('input').each(function() { $(this).val('delete') });
							
							$(row).css('display','none');
							
							{$zfext}_globalFormIsDirty = true;
							
							$('#{$zfext}-formgrid-table-{$name}').trigger('{$zfext}-onDelete', {'target':this, 'row':row});
							$('#{$zfext}-formgrid-table-{$name}').trigger('{$zfext}-onChange', this);
							$('#{$zfext}-formgrid-table-{$name}').trigger('{$zfext}-postChange', this);
						}
						
						function {$zfext}_formgrid_bind_onchange_{$name}() {							
							$('#{$zfext}-formgrid-table-{$name}').find('input').not('.nobroadcast').each(function() {
								$(this).change(function() {									
									$('#{$zfext}-formgrid-table-{$name}').trigger('{$zfext}-onChange', this);
									$('#{$zfext}-formgrid-table-{$name}').trigger('{$zfext}-postChange', this);
								});
								$(this).keyup(function() {									
									$('#{$zfext}-formgrid-table-{$name}').trigger('{$zfext}-onChange', this);
									$('#{$zfext}-formgrid-table-{$name}').trigger('{$zfext}-postChange', this);
								});
							});							
							
							$('#{$zfext}-formgrid-table-{$name}').trigger('{$zfext}-onChange', this);
							$('#{$zfext}-formgrid-table-{$name}').trigger('{$zfext}-postChange', this);
						}
						
						function {$zfext}_formgrid_bind_events_{$name}() {
							{$zfext}_formgrid_bind_onchange_{$name}();
						}

						$(function() {
							{$zfext}_formgrid_bind_events_{$name}();	

							for(var i=0;i<{$zfext}_formgrid_rowcount_{$name};i++) {
								{$zfext}_formgrid_fieldscripts_{$name}(i);
							}
						});
						
						// Column resizing
						/*$(function() {
				        	var colElement, colWidth, originalSize, newColWidth;
				        
				        	$('#{$zfext}-formgrid-table-{$name} th').resizable({
				        		handles: 'e',
		
								 // set correct COL element and original size
								 start: function(event, ui) {
								   var colIndex = ui.helper.index() + 1;
								   colElement = $('#{$zfext}-formgrid-table-{$name}').find('colgroup > col:nth-child(' + colIndex + ')');
								
								  // get col width (faster than .width() on IE)
								  colWidth = parseInt(colElement.get(0).style.width, 10);
								  originalSize = ui.size.width;
								  
								 },
								
								 // set COL width
								 resize: function(event, ui) {
								   var resizeDelta = ui.size.width - originalSize;
								
								   newColWidth = colWidth + (colWidth * (resizeDelta/originalSize));
								   
								   newColWidth = (ui.size.width / $('#{$zfext}-formgrid-table-{$name}').width()) * 100;
								   
								   colElement.width(newColWidth+'%');
								
								   // height must be set in order to prevent IE9 to set wrong height
								   //$(this).css('height', 'auto');
							
				        		},
				        		
				        		stop: function(event, ui) {
				        			// Todo : send new width to server for future renderings				        		
				        		}
				        	});
				        });*/

					</script>
						
					";

				if ($options['sortable']===true) {
					$xhtml .= "
						<script>						
							$(function() {							
								$('#{$zfext}-formgrid-table-{$name} tbody.{$zfext}-formgrid-content').sortable({
									stop: function(event, ui) {
										{$zfext}_formgrid_index_sortables_{$name}();
									}
								});
								
								// Don't do this, it causes issues with firefox and is silly anyway.
								$('#{$zfext}-formgrid-table-{$name} tbody.{$zfext}-formgrid-content').disableSelection();
							});
						</script>					
					";			
				}
				
				
				
				foreach ($columns as $colname=>$column) {
					$xhtml .= $column->renderScripting($this->view, array('matrix_name'=>$name));									
				}
				
			}
		} else {
			$xhtml .= "<B>Required attribute(s): columns</B>";
		}
		
		return $xhtml;
	}
	
	public function processTokens($lStrValue,$row) {                
        $lAryMutators = array();
        
        $len = strlen($lStrValue);
        $tok = '';
        $bracketed = false;
        for ($i=0;$i<$len;$i++) {
            if (!$bracketed) {
                if ($lStrValue[$i] == '{') {
                    $bracketed = true;
                }    
            } else {
                if ($lStrValue[$i] == '}') {
                    $bracketed = false;
                    if (!empty($tok)) {
                        $lAryMutators[$tok] = true;
                        $tok = '';
                    }
                } else $tok .= $lStrValue[$i];
            }
        }
        if (!empty($tok)) $lAryMutators[$tok] = true;
        
        foreach (array_keys($lAryMutators) as $lStrMutator) {
        	$lStrMutatorField = str_replace('.','_',$lStrMutator);  
        	if (isset($row[$lStrMutatorField])) {         
            	$lStrValue = str_replace('{'.$lStrMutator.'}',$row[$lStrMutatorField],$lStrValue);
        	}            
        }
        
        return $lStrValue;
    }
	
}
