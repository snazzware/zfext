<?php 

class Snazzware_View_Helper_Alert extends Zend_View_Helper_Abstract {

	public function Alert($caption, $message, $icon) {		
		$alert = str_replace("'",'"',"<img style='vertical-align: middle; ' src='{$icon}' /><font style='padding-left: 10px; font-size: 20pt; font-weight: bold;'>{$caption}</font><br /><Br />{$message}");
				
		$xhtml = "
			<script>
			$(function(){				
				$.fancybox('{$alert}',{
					'transitionIn': 'none',
					'transitionOut': 'none',
					'hideOnOverlayClick': false,
					'hideOnContentClick': true,
					'autoDimensions': false,
					'width': 480,
					'height': 120
				});				
			});
			</script>
		";		
		
		return $xhtml;
	}
	
}

?>