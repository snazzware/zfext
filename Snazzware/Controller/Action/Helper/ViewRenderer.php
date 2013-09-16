<?php 

class Snazzware_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{
	
	/**
	* Render a view based on path specifications
	*
	* Renders a view based on the view script path specifications.
	*
	* @param  string  $action
	* @param  string  $name
	* @param  boolean $noController
	* @return void
	*/
	/*public function render($action = null, $name = null, $noController = null)
	{				
		$this->setRender($action, $name, $noController);
		$path = $this->getViewScript();
		try {
			$this->renderScript($path, $name);
		} Catch (Exception $e) {
			$path = basename($path);
			
			if (strlen($path) > 0 ) {				
				$this->renderScript($path);
			} else {
				throw $e;
			}
		}
	}	*/
	
}

?>