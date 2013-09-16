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

class Snazzware_Controller_Crud extends Snazzware_Controller_Form {
	
	private $primaryEntityClassName = '';	
	private $gridClassName = '';
	private $gridAdapterClassName = 'Snazzware_Grid_Adapter_Entity';	
	private $grid = null;
	private $gridAdapter = null;	
	private $deleteEnabled = true;
	private $printEnabled = false;
	private $historyEnabled = false;
	private $printListingEnabled = false;
	private $backEnabled = true;
	private $useCloudPrint = false;
	private $downloadEnabled = false;
	private $createBeforeEdit = false;
	private $dupeProtection = false;
	private $highlightPreviousSelection = true;
	private $detailsActionName = 'details';
	private $listingActionName = 'listing';
	private $deleteActionName = 'delete';
	private $printActionName = 'print';
	private $historyActionName = 'history';
	private $printListingActionName = 'printlisting';
	private $downloadActionName = 'download';
	private $printTemplateName = 'print.phtml';
	private $printListingTemplateName = 'print_listing.phtml';
	private $printFilename = 'print.pdf';
	private $detailNavigation = false;
	
	private $relatedControllers = array();
		
	public function getControllerPath() {
		return $this->getRequest()->getModuleName().'/'.$this->getRequest()->getControllerName().'/';
	}
	
	public function indexAction()
    {   
    	parent::baseAction();   

    	$selfReferral = $this->checkSelfReferral();
    	
    	$id = State::get(__ZFEXT_PREFIX.'-crud-selection',$this->getPrimaryEntityClassName(),$this->_getParam('id',0));
    	
    	if (($id>0) && (!$selfReferral)) {
    		$this->_redirect($this->getControllerPath().$this->getDetailsActionName().'?id='.$id);
    	} else {
    		$this->_redirect($this->getControllerPath().$this->getListingActionName());
    	}
    }
	
    public function checkSelfReferral() {
    	$selfReferral = false;
    	 
    	if (isset($_SERVER['HTTP_REFERER'])) {
    		$path = parse_url($_SERVER['HTTP_REFERER'],PHP_URL_PATH);
    		$paths = array_filter(explode('/',$path));
    		$shifted = array_shift($paths);
    		if ($shifted == $this->getRequest()->getModuleName()) $shifted = array_shift($paths);
    		if ((count($paths)>0) && (in_array($shifted,array($this->getRequest()->getControllerName())+$this->getRelatedControllers()))) {
    			$selfReferral = true;
    		}
    	}
    	
    	return $selfReferral;
    }
    
    public function ajaxgetAction() {
    	parent::baseAction();        
    	if ((!$this->canRead()) || (($this->_getParam('id',0)<1) && (!$this->canCreate()))) $this->unauthorized();
    	
    	$entity = EntityUtils::get($this->getPrimaryEntityClassName(),$this->_getParam('id',0));
    	
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	header('Content-Type: application/json');
    	
    	echo $entity->toJson();
    }
    
	protected function renderToPdf($attachment=true) {
		$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	$html = $this->view->render($this->getRequest()->getControllerName().'/'.$this->getPrintTemplateName());
    	
    	header('Content-type: application/pdf');
    	if ($attachment) {
			header('Content-Disposition: attachment; filename="'.$this->getPrintFilename().'"');
    	} else {
    		header('filename="'.$this->getPrintFilename().'"');
    		
    		$print_trigger = "
    			<script type='text/javascript'>
					this.print();
    				this.closeDoc(true);
				</script>
    		</body>";
    		
    		$html = str_replace('</body>',$print_trigger,$html);
    	}    	
    	
    	echo Printer::html2pdf($html);
	}
	
	protected function renderToCloudPrint() {		
		$html = $this->view->render($this->getRequest()->getControllerName().'/'.$this->getPrintTemplateName());
		
		if (Printer::html($html,$this->getPrintFilename())) {
			Alert::info('Print job submitted.');
		} else {
			Alert::error('Error submitting print job.');
		}
	}
    
	public function inlineprintAction() {
		parent::baseAction();
		 
		if ((!$this->canRead()) || ($this->_getParam('id',0)<1)) $this->unauthorized();
	
		$entity = EntityUtils::get($this->getPrimaryEntityClassName(),$this->_getParam('id',0));
	
		$form = $this->getForm();
		$form->build($entity);
		$form->populateFromEntity($entity);
	
		$this->view->form = $form;
		 
		$this->view->entity = $entity;
	
		$this->renderToPdf(false);
	}
	
    public function printAction() {
    	parent::baseAction();
    	$id = $this->_getParam('id',0);
    	
    	if ((!$this->canRead()) || ($id<1)) $this->unauthorized();
    	
    	if ($this->getUseCloudPrint()) {    	
    		$entity = EntityUtils::get($this->getPrimaryEntityClassName(),$id);
    		    	
	    	$form = $this->getForm();
	    	$form->build($entity);
	    	$form->populateFromEntity($entity);
	    	
	    	$this->view->form = $form;
	    	 
	    	$this->view->entity = $entity;
	    	
	    	if (State::get('google','cloudprint_token',null)==null) {    		
	    		Alert::info('A printer has not been selected.');
	    		$this->renderToPdf();
	    	} else {
	    		$this->renderToCloudPrint();
	    	}
	    	
	    	$id = State::get(__ZFEXT_PREFIX.'-crud-selection',$this->getPrimaryEntityClassName(),$this->_getParam('id',0));
	    	
	    	if ($id>0) {
	    		$this->_redirect($this->getControllerPath().$this->getDetailsActionName().'?id='.$id);
	    	} else {
	    		$this->_redirect($this->getControllerPath().$this->getListingActionName());
	    	}
    	} else {
    		$this->_helper->layout->disableLayout();
    		$this->_helper->viewRenderer->setNoRender(true);
    		
    		$url = ConfigUtils::get('global','baseUrl').'/'.$this->getControllerPath().'inlineprint?id='.$id;
    		
    		echo "
    			<html>
    				<style type=\"text/css\">
    					embed[type*=\"application/x-shockwave-flash\"],embed[src*=\".swf\"],object[type*=\"application/x-shockwave-flash\"],object[codetype*=\"application/x-shockwave-flash\"],object[src*=\".swf\"],object[codebase*=\"swflash.cab\"],object[classid*=\"D27CDB6E-AE6D-11cf-96B8-444553540000\"],object[classid*=\"d27cdb6e-ae6d-11cf-96b8-444553540000\"],object[classid*=\"D27CDB6E-AE6D-11cf-96B8-444553540000\"]{	display: none !important;}
    				</style>
    				<body marginwidth=\"0\" marginheight=\"0\" style=\"background-color: rgb(38,38,38)\">
    					<embed width=\"100%\" height=\"100%\" name=\"pdfObject\" id=\"pdfObject\" src=\"$url\" type=\"application/pdf\" classid=\"clsid:CA8A9780-280D-11CF-A24D-444553540000\">
    				</body>
    				<script>    					
    					window.setInterval(function(){window.onfocus=function(){ window.close();}},750);
    				</script>    				
    			</html>
    		";
    	}
    }
    
    public function downloadAction() {
    	parent::baseAction();
    	
    	if ((!$this->canRead()) || ($this->_getParam('id',0)<1)) $this->unauthorized();
    	 
    	$entity = EntityUtils::get($this->getPrimaryEntityClassName(),$this->_getParam('id',0));
    	 
    	$form = $this->getForm();
    	$form->build($entity);
    	$form->populateFromEntity($entity);
    	 
    	$this->view->form = $form;
    	
    	$this->view->entity = $entity;
    	 
    	$this->renderToPdf();
    }
    
    public function deleteAction() {
    	parent::baseAction();        
    	if ((!$this->canDelete()) || ($this->_getParam('id',0)<1)) $this->unauthorized();
    	
    	$entity = EntityUtils::get($this->getPrimaryEntityClassName(),$this->_getParam('id',0));
    	
    	$this->preDelete($entity);
    	
    	EntityUtils::delete($entity);
    	
    	Alert::success('Record deleted.');
    	
    	$this->postDelete($entity);
    }
    
    public function selectionChanged($oldId,$newId) {
    	// Stub which gets called when the value of state variable __ZFEXT_PREFIX-crud-selection.$this->getPrimaryEntityClassName() is changed. 
    	// Note that either of these may be zero, indicating "no selected record"
    }
    
	public function detailsAction() {
    	parent::baseAction();        
    	if ((!$this->canRead()) || (($this->_getParam('id',0)<1) && (!$this->canCreate()))) $this->unauthorized();
    	
    	// Check to see if we are creating a new record, and if Create Before Edit is enabled, create and persist a new
    	// record first, then redirect back to this action w/ the new id. This is useful for situations where the user wants
    	// to see the ID number on the form right away when they are creating a new record.
    	if (($this->_getParam('id',0)==0) && $this->getCreateBeforeEdit()) {
    		$entity = EntityUtils::get($this->getPrimaryEntityClassName(),0);
    		EntityUtils::persist($entity);
    		
    		$this->redirect("/{$this->getControllerPath()}{$this->getRequest()->getActionName()}?id={$entity->getId()}");
    	}
    	
    	// Set selected entity id when we view details of an entity
    	$id = $this->_getParam('id',0);
    	$currentSelectedId = State::get(__ZFEXT_PREFIX.'-crud-selection',$this->getPrimaryEntityClassName(),0);    	
    	State::set(__ZFEXT_PREFIX.'-crud-selection',$this->getPrimaryEntityClassName(),$id);
    	if ($id != $currentSelectedId) $this->selectionChanged($currentSelectedId,$id);
    	
    	// Get selected entity by id
	    $entity = EntityUtils::get($this->getPrimaryEntityClassName(),$id);
    	
	    // Get, build, and secure form
    	$form = $this->getForm();    	
    	$form->build($entity);    	
    	$form->secure($this->getRequest()->getModuleName().'.'.$this->getRequest()->getControllerName().'.'.$this->getRequest()->getActionName());
    	
    	$elements = $form->getElements(); // TODO : Why is this here? Probably safe to remove, but need to test in case something is relying on this being called.    	
    	
    	if (($entity->getId()>0) && ($this->getDetailNavigation())) {
    		$grid = $this->getGrid();
    		$model = $this->getModel();    	
    		$grid->setAdapter(new Snazzware_Grid_Adapter_Entity($model));

    		$this->view->navinfo = $grid->getNavigationInformation($entity->getId());
    	}

    	if ($this->getRequest()->isPost()) {    		
    		if ($this->canUpdate()) {
	    		if ($form->isValid($this->_getAllParams())) {   
					if ($entity->getId()<=0) $new = true;
	    			else $new = false;
	    			
	    			if ((($new) && ($this->canCreate())) || ((!$new) && (!$this->getForm()->getReadOnly()))) {
	    				$form->saveToEntity($entity);
	    				
	    				if ($new) $this->preCreate($entity);
	    				else $this->preUpdate($entity);
	    				
	    				$this->prePersist($entity);

	    				if ($this->getDupeProtection()) {
	    					$thisPost = sha1(print_r($this->getRequest()->getParams(),true));
	    					$lastPost = State::get(__ZFEXT_PREFIX.'-crud-checksum',$this->getPrimaryEntityClassName(),'');	
	    					if ($thisPost != $lastPost) {	    				
	    						EntityUtils::persist($entity);
	    						State::set(__ZFEXT_PREFIX.'-crud-checksum',$this->getPrimaryEntityClassName(),$thisPost);
	    					}
	    				} else {
	    					EntityUtils::persist($entity);
	    				}
	    				
	    				$this->postPersist($entity);
	
	    				if ($new) $this->postCreate($entity);
	    				else $this->postUpdate($entity);
	    				
	    			} else {
	    				$this->unauthorized();
	    			}		
	    		}
    		} else {
    			$this->unauthorized();
    		}
    	} else {  
    		$this->prePopulate($entity);  		 	
    		$form->populateFromEntity($entity);
    		$this->postPopulate($entity);    		
    	}
    	
    	$this->view->form = $form;
    	
    	$this->view->entity = $entity;
    	
    	$this->buildDetailsNavigation($entity);
    	
    	$this->prepareView($entity);
    	
    	$this->view->bottomNavigation = $this->getNavigation('bottom');
    	$this->view->leftNavigation = $this->getNavigation('left');
    	
    	$this->view->promptOnModified = true;    	
    }
    
    public function generateCrudSerial() {
    	// from uniqid docs on php.net, Andrew Moore
    	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    			// 32 bits for "time_low"
    			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
    	
    			// 16 bits for "time_mid"
    			mt_rand( 0, 0xffff ),
    	
    			// 16 bits for "time_hi_and_version",
    			// four most significant bits holds version number 4
    			mt_rand( 0, 0x0fff ) | 0x4000,
    	
    			// 16 bits, 8 bits for "clk_seq_hi_res",
    			// 8 bits for "clk_seq_low",
    			// two most significant bits holds zero and one for variant DCE1.1
    			mt_rand( 0, 0x3fff ) | 0x8000,
    	
    			// 48 bits for "node"
    			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    	);
    }
    
    public function printlistingAction() {
    	parent::baseAction();
    	if (!$this->canRead()) $this->unauthorized();
    	 
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	 
    	$grid = $this->getGrid();
    	$model = $this->getModel();
    	 
    	$grid->setAdapter(new Snazzware_Grid_Adapter_Entity($model));
    	 
    	if ($grid->handleRequest($this->getRequest(),$this->view)) {
    		$this->_helper->layout->disableLayout();
    		$this->_helper->viewRenderer->setNoRender(true);
    	} else {
    		$this->view->grid = $grid;
    	}
    	 
    	$this->view->rows = $grid->getRows();
    	
    	$html = $this->view->render($this->getRequest()->getControllerName().'/'.$this->getPrintListingTemplateName());
    	
    	$print_trigger = "
    			<script>    			
					window.print();
    				window.close();    			
				</script>
    		</body>";
    	
    	$html = str_replace('</body>',$print_trigger,$html);
    	
    	echo $html;
    }
    
    // TODO : refactor out the rendering of history... this is a hack
    public function historyAction() {
    	parent::baseAction();
    	if (!$this->canRead()) $this->unauthorized();
    	
    	$id = $this->_getParam('id',0);
    	
    	$entity = EntityUtils::get($this->getPrimaryEntityClassName(),$id);
    	
    	$criteria = array(
    		'filters'=>array(
    			'targetclass'=>array(
    				'field'=>'targetclass',
    				'op'=>'=',
    				'value'=>$this->getPrimaryEntityClassName()
    			),
    			'targetid'=>array(
    				'field'=>'targetid',
    				'op'=>'=',
    				'value'=>$id
    			)
    		),
    		'sorts'=>array(
    			'createdAt'=>array(
    				'field'=>'createdAt',
    				'dir'=>'desc'
    			)
    		)
    	);    	
    	$history = EntityUtils::get(\Snazzware\Entity::getCrudHistoryClassName(),$criteria);
    	
    	echo "<h1>History for {$entity->getHumanReadableIdentifier()}</h1>";
    	
    	if (count($history)==0) {
    		echo "<h2>No history was found for this record.</h2>";
    	}
    	
    	foreach ($history as $crud) {
    		echo "<table>";
    		echo "<tr>";
    		echo "<th>Username</th>";
    		echo "<th>Timestamp</th>";
    		echo "</tr>";
    		echo "<tr>";
    		echo "<td>{$crud->getUser()->getUsername()}</td>";
    		echo "<td>{$crud->getCreatedAt()->format(ConfigUtils::get('global','phpDateTimeFormat'))}</td>";
    		echo "</tr>";
    		echo "</table>";
    		echo "<pre>";
    		echo $crud->getManifest();
    		echo "</pre>";
    		echo '<hr />';
    	}
    	
    	
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function listingAction() {
    	parent::baseAction();    	
    	if (!$this->canRead()) $this->unauthorized();
    	
    	// Clear selected entity id when we list available entities
    	$selection = State::get(__ZFEXT_PREFIX.'-crud-selection',$this->getPrimaryEntityClassName(),0);
    	if ($selection > 0) {
    		State::set(__ZFEXT_PREFIX.'-crud-selection-previous',$this->getPrimaryEntityClassName(),$selection);
    		$this->selectionChanged($selection,0);
    	}
    	State::set(__ZFEXT_PREFIX.'-crud-selection',$this->getPrimaryEntityClassName(),0);
    	
    	$grid = $this->getGrid();
    	
    	$grid->setAdapter($this->getGridAdapter());    	
    	$grid->setOption('ajaxUrl',ConfigUtils::get('global','baseUrl').'/'.$this->getControllerPath().$this->getRequest()->getActionName());
    	
    	if ($this->getHighlightPreviousSelection()) {
    		$grid->addRowHighlight('id',State::get(__ZFEXT_PREFIX.'-crud-selection-previous',$this->getPrimaryEntityClassName(),0));
    	}
    	
    	if ($grid->handleRequest($this->getRequest(),$this->view)) {
    		$this->_helper->layout->disableLayout();
    		$this->_helper->viewRenderer->setNoRender(true);    	
    	} else {    	
    		$this->view->grid = $grid;
    	}
    	
    	$this->buildListingNavigation();
    	
    	$this->view->bottomNavigation = $this->getNavigation('bottom');
    	$this->view->leftNavigation = $this->getNavigation('left');
    	
    	$this->_helper->viewRenderer->setRender('listing');
    	
    	$this->handleWidgetCallback();
    }
    
    public function popuplistingAction() {
    	parent::baseAction();
    	if (!$this->canRead()) $this->unauthorized();
    	 
    	$grid = $this->getGrid();
    	$grid->setName($grid->getName().'_popup');
    	$model = $this->getModel();
    	 
    	if ($this->_getParam('targetname','')!='') {
    		State::set(__ZFEXT_PREFIX.'-crud-popuplisting','targetname',$this->_getParam('targetname'));
    	}
    	if ($this->_getParam('callback','')!='') {
    		State::set(__ZFEXT_PREFIX.'-crud-popuplisting','callback',$this->_getParam('callback'));
    	}
    	if ($this->_getParam('oneshot','')==='true') {
    		$oneshot = 'window.close();';
    		$oneshotParam = '?oneshot=true';
    	} else {
    		$oneshot = '';
    		$oneshotParam = '';
    	}
    	 
    	$targetname = State::get(__ZFEXT_PREFIX.'-crud-popuplisting','targetname');
    	$callback = State::get(__ZFEXT_PREFIX.'-crud-popuplisting','callback',__ZFEXT_PREFIX."_callback_{$targetname}");
    	 
    	$ajaxGet = ConfigUtils::get('global','baseUrl').'/'.$this->getControllerPath().'ajaxget';    	
    	 
    	$grid->setAdapter(new Snazzware_Grid_Adapter_Entity($model));
    	$grid->setCallbackUrl(ConfigUtils::get('global','baseUrl').'/'.$this->getControllerPath().$this->getRequest()->getActionName().$oneshotParam);
    	$grid->setAjaxGetUrl($ajaxGet);
    	$grid->setOption('ajaxUrl',ConfigUtils::get('global','baseUrl').'/'.$this->getControllerPath().$this->getRequest()->getActionName().$oneshotParam);
    	
    	$grid->setOnClick("
        		$.get(\"{$grid->getAjaxGetUrl()}?id=".'{id}'."\", function(data) {
    				window.opener.{$callback}(data);
    				{$oneshot}
        		});
        	");
    	 
    	if ($grid->handleRequest($this->getRequest(),$this->view)) {
    		$this->_helper->layout->disableLayout();
    		$this->_helper->viewRenderer->setNoRender(true);
    	} else {
    		$this->_helper->layout->disableLayout();
    		$this->view->grid = $grid;
    	}
    	 
    	$this->_helper->viewRenderer->setNoController(true);
    	$this->_helper->viewRenderer->setRender('shared/popuplisting');
    }
    
	public function embeddedlistingAction() {
    	parent::baseAction();    	
    	if (!$this->canRead()) $this->unauthorized();
    	
    	$grid = $this->getGrid();
    	$grid->setName($grid->getName().'_embedded');
    	$model = $this->getModel();
    	
    	if ($this->_getParam('targetname','')!='') {
    		State::set(__ZFEXT_PREFIX.'-crud-embeddedlisting','targetname',$this->_getParam('targetname'));
    	}
		if ($this->_getParam('callback','')!='') {
    		State::set(__ZFEXT_PREFIX.'-crud-embeddedlisting','callback',$this->_getParam('callback'));
    	}
    	
    	$targetname = State::get(__ZFEXT_PREFIX.'-crud-embeddedlisting','targetname');
    	$callback = State::get(__ZFEXT_PREFIX.'-crud-embeddedlisting','callback',__ZFEXT_PREFIX."_callback_{$targetname}");
    	
    	$ajaxGet = ConfigUtils::get('global','baseUrl').'/'.$this->getControllerPath().'ajaxget';
    	
    	$grid->setAdapter(new Snazzware_Grid_Adapter_Entity($model));    	
    	$grid->setCallbackUrl(ConfigUtils::get('global','baseUrl').'/'.$this->getControllerPath().$this->getRequest()->getActionName());
    	$grid->setAjaxGetUrl($ajaxGet);
    	$grid->setOption('ajaxUrl',ConfigUtils::get('global','baseUrl').'/'.$this->getControllerPath().$this->getRequest()->getActionName());
    	
    	$grid->setOnClick("
    		$.get(\"{$grid->getAjaxGetUrl()}?id=".'{id}'."\", function(data) {
    			{$callback}(data);
    			$.fancybox.close();
    		});
    	");
    	
    	if ($grid->handleRequest($this->getRequest(),$this->view)) {
    		$this->_helper->layout->disableLayout();
    		$this->_helper->viewRenderer->setNoRender(true);    	
    	} else {    	
    		$this->_helper->layout->disableLayout();
    		$this->view->grid = $grid;
    	}    	
    	
    	$this->_helper->viewRenderer->setNoController(true);
    	$this->_helper->viewRenderer->setRender('shared/embeddedlisting');
    }
    
	public function buildListingNavigation() {
    	$nav = $this->getNavigation($this->getDefaultNavigationArea());
    	
    	if ($this->getCreateEnabled() && $this->canCreate($this->getRequest()->getActionName())) {
    		$nav->addItem($nav->createItem('Crud_New','new',array(    															
    			'module'=>$this->getRequest()->getModuleName(),
    			'controller'=>$this->getRequest()->getControllerName(),    															
    			'action'=>$this->getDetailsActionName()
    		)));
    	}
    	
    	if ($this->getPrintListingEnabled()) {
    		$nav->addItem($this->buildPrintListingNavigationItem());
    	}
    	
    	return $nav;
    }
    
    protected function buildPrintNavigationItem($entity = null) {
    	$baseUrl = ConfigUtils::get('global','base_url');
    	
    	$nav = $this->getNavigation($this->getDefaultNavigationArea());
    	
    	$printAction = "{$baseUrl}/{$this->getRequest()->getControllerName()}/{$this->getPrintActionName()}";
    	$detailsAction = "{$baseUrl}/{$this->getRequest()->getControllerName()}/{$this->getDetailsActionName()}";
    	$printUrl = "{$baseUrl}/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}/{$this->getPrintActionName()}?id={$entity->getId()}";
    	
    	return $nav->createItem('Crud_Print','print',array(
    		'form'=>$this->getForm(),
    		'printAction'=>$printAction,
    		'detailsAction'=>$detailsAction,
    		'printUrl'=>$printUrl,	
    		'module'=>$this->getRequest()->getModuleName(),
    	    'controller'=>$this->getRequest()->getControllerName(),
    	    'action'=>$this->getPrintActionName()    	    
    	));
    }
    
    protected function buildHistoryNavigationItem($entity = null) {
    	$baseUrl = ConfigUtils::get('global','base_url');
    	
    	if (is_object($entity) && EntityUtils::oneof($entity,'Snazzware\Entity')) {
    		$nav = $this->getNavigation($this->getDefaultNavigationArea());
    		
    		$historyUrl = "{$baseUrl}/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}/{$this->getHistoryActionName()}?id={$entity->getId()}"; 
    	
	    	return $nav->createItem('Crud_History','history',array(
	    		'historyUrl'=>$historyUrl,
	    		'module'=>$this->getRequest()->getModuleName(),
    			'controller'=>$this->getRequest()->getControllerName(),
    			'action'=>$this->getHistoryActionName()    			
	    	));
    	} else return null;
    }
    
    protected function buildPrintListingNavigationItem() {
    	$baseUrl = ConfigUtils::get('global','base_url');
    	
    	$nav = $this->getNavigation($this->getDefaultNavigationArea());
    	
    	$printListingUrl = "{$baseUrl}/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}/{$this->getPrintListingActionName()}";
    	 
    	return $nav->createItem('Crud_PrintListing','printlisting',array(
	    	'printListingUrl'=>$printListingUrl,
	    	'module'=>$this->getRequest()->getModuleName(),
	    	'controller'=>$this->getRequest()->getControllerName(),
	    	'action'=>$this->getPrintListingActionName()	    		
    	));
    }
    
    protected function buildDownloadNavigationItem($entity = null) {
    	$baseUrl = ConfigUtils::get('global','base_url');
    	
    	$nav = $this->getNavigation($this->getDefaultNavigationArea());
    	
    	$zfext = __ZFEXT_PREFIX;
    	
    	$downloadUrl = "{$baseUrl}/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}/{$this->getDownloadActionName()}?id={$entity->getId()}";
    	$downloadAction = "{$baseUrl}/{$this->getRequest()->getControllerName()}/{$this->getDownloadActionName()}";
    	$detailsAction = "{$baseUrl}/{$this->getRequest()->getControllerName()}/{$this->getDetailsActionName()}";
    	
    	return $nav->createItem('Crud_Download','download',array(
    		'form'=>$this->getForm(),
    		'downloadUrl'=>$downloadUrl,
    		'downloadAction'=>$downloadAction,
    		'detailsAction'=>$detailsAction,      	
        	'module'=>$this->getRequest()->getModuleName(),
        	'controller'=>$this->getRequest()->getControllerName(),
        	'action'=>$this->getDownloadActionName()       		
       	));
    }
    
	public function buildDetailsNavigation($entity = null) {		
    	$nav = $this->getNavigation($this->getDefaultNavigationArea());
    
    	$zfext = __ZFEXT_PREFIX;
    	
    	$baseUrl = ConfigUtils::get('global','base_url');
    	
    	if (!$this->getForm()->getReadOnly()) {
    		$nav->addItem($nav->createItem('Crud_Save','save',array(
    			'form'=>$this->getForm(),   														
    			'module'=>$this->getRequest()->getModuleName(),
    			'controller'=>$this->getRequest()->getControllerName(),
    			'action'=>$this->getDetailsActionName()
    		)));    			    		
    	}
    	
    	if ($this->getDeleteEnabled()) {
	    	if ($this->_getParam('id',0)>0) {
				if ($this->canDelete($this->getRequest()->getActionName())) {
					$deleteUrl = ConfigUtils::get('global','baseUrl').'/'.$this->getControllerPath().$this->getDeleteActionName().'?id='.$this->_getParam('id',0);
					
		    		$nav->addItem($nav->createItem('Crud_Delete','delete',array(
		    			'form'=>$this->getForm(),
		    			'deleteUrl'=>$deleteUrl,
						'module'=>$this->getRequest()->getModuleName(),
    					'controller'=>$this->getRequest()->getControllerName(),
    					'action'=>$this->getDeleteActionName(),
		    		)));    					
		    	}
	    	}
    	}
    	
		if ($this->getPrintEnabled()) {
    		$nav->addItem($this->buildPrintNavigationItem($entity));    		
    	}
    	
    	if ($this->getDownloadEnabled()) {
    		$nav->addItem($this->buildDownloadNavigationItem($entity));
    	}
    	
    	if (is_object($entity) && EntityUtils::oneof($entity,EntityUtils::getRootEntityClassName())) {
    		if ($this->getHistoryEnabled()) {
    			$nav->addItem($this->buildHistoryNavigationItem($entity));
    		}
    	}
    	
    	if ($this->getBackEnabled()) {
    		$nav->addItem($nav->createItem('Crud_Back','back',array(
    			'module'=>$this->getRequest()->getModuleName(),
    			'controller'=>$this->getRequest()->getControllerName(),
    			'action'=>$this->getListingActionName()
    		)));
    	}    	
    	
    	return $nav;
    }

    protected function prepareView($dto) {
    	// stub
    }

    protected function prePopulate($dto) {
		// stub	
	}
	
    protected function postPopulate($dto) {
		// stub	
	}
    
    protected function prePersist($dto) {
		// stub	
	}
	
	protected function postPersist($dto) {
		State::set(__ZFEXT_PREFIX.'-crud','postload-redirect',EntityUtils::processTokens($this->_getParam(__ZFEXT_PREFIX.'-crud-postload-redirect',''),$dto));
		State::set(__ZFEXT_PREFIX.'-crud','postload-redirect-target',$this->_getParam(__ZFEXT_PREFIX.'-crud-postload-redirect-target',''));
		$target = $this->_getParam(__ZFEXT_PREFIX.'-crud-postpersist-redirect-target',''); 
			
		if ($target!=='') {
			$url = $this->_getParam(__ZFEXT_PREFIX.'-crud-postpersist-redirect','');
			echo "
				<script>
					$(function() {
						window.open(\'$url\',\'$target\');
					});
				</script>
			";
		} else {
			$this->redirect(EntityUtils::processTokens($this->_getParam(__ZFEXT_PREFIX.'-crud-postpersist-redirect',$this->getControllerPath().'listing'),$dto));
		}
	}
	
	protected function preCreate($dto) {
		// stub	
	}
	
	protected function postCreate($dto) {
		// stub
	}
	
	protected function preUpdate($dto) {
		// stub	
	}
	
	protected function postUpdate($dto) {
		// stub
	}
	
	protected function preDelete($dto) {
		// stub
	}
	
	protected function postDelete($dto) {
		$this->redirect(EntityUtils::processTokens($this->_getParam(__ZFEXT_PREFIX.'-crud-postdelete-redirect',$this->getControllerPath().'listing'),$dto));
	}
    
	protected function setGrid($value) {
		$this->grid = $value;
	}
	
	protected function getGrid() {
		if ($this->grid == null) {
			$classname = $this->getGridClassName();
			$this->grid = new $classname;	
		}
		return $this->grid;
	}
	
	protected function getGridAdapter() {
		if ($this->gridAdapter == null) {
			$classname = $this->getGridAdapterClassName();
			$this->gridAdapter = new $classname($this->getModel());	
		}
		return $this->gridAdapter;
	}
	
	protected function getPrimaryEntityClassName() {
		return $this->primaryEntityClassName;
	}
		
	protected function setPrimaryEntityClassName($classname) {
		$this->primaryEntityClassName = $classname;
	}
	
	protected function getGridClassName() {
		return $this->gridClassName;
	}
		
	protected function setGridClassName($classname) {
		$this->gridClassName = $classname;
	}
	
	protected function getGridAdapterClassName() {
		return $this->gridAdapterClassName;
	}
		
	protected function setGridAdapterClassName($classname) {
		$this->gridAdapterClassName = $classname;
	}
	
	protected function getDeleteEnabled() {
		return $this->deleteEnabled;
	}
	
	protected function setDeleteEnabled($value) {
		$this->deleteEnabled = $value;
	}
	
	protected function getPrintEnabled() {
		return $this->printEnabled;
	}
	
	protected function setPrintEnabled($value) {
		$this->printEnabled = $value;
	}
	
	protected function getHistoryEnabled() {
		return $this->historyEnabled;
	}
	
	protected function setHistoryEnabled($value) {
		$this->historyEnabled = $value;
	}
	
	protected function getPrintListingEnabled() {
		return $this->printListingEnabled;
	}
	
	protected function setPrintListingEnabled($value) {
		$this->printListingEnabled = $value;
	}
	
	protected function getBackEnabled() {
		return $this->backEnabled;
	}
	
	protected function setBackEnabled($value) {
		$this->backEnabled = $value;
	}
	
	protected function getUseCloudPrint() {
		return $this->useCloudPrint;
	}
	
	protected function setUseCloudPrint($value) {
		$this->useCloudPrint = $value;
	}
	
	protected function getDownloadEnabled() {
		return $this->downloadEnabled;
	}
	
	protected function setDownloadEnabled($value) {
		$this->downloadEnabled = $value;
	}
	
	protected function getHighlightPreviousSelection() {
		return $this->highlightPreviousSelection;
	}
	
	protected function setHighlightPreviousSelection($value) {
		$this->highlightPreviousSelection = $value;
	}
	
	protected function getDetailNavigation() {
		return $this->detailNavigation;
	}
	
	protected function setDetailNavigation($value) {
		$this->detailNavigation = $value;
	}
	
	protected function getDupeProtection() {
		return $this->dupeProtection;
	}
	
	protected function setDupeProtection($value) {
		$this->dupeProtection = $value;
	}
	
	protected function getCreateBeforeEdit() {
		return $this->createBeforeEdit;
	}
	
	protected function setCreateBeforeEdit($value) {
		$this->createBeforeEdit = $value;
	}
	
	protected function getPrintFilename() { return $this->printFilename; }
	protected function setPrintFilename($value) { $this->printFilename = $value; }
	
	
	protected function getDetailsActionName() { return $this->detailsActionName; }
	protected function setDetailsActionName($value) { $this->detailsActionName = $value; }
	
	protected function getDeleteActionName() { return $this->deleteActionName; }
	protected function setDeleteActionName($value) { $this->deleteActionName = $value; }
	
	protected function getListingActionName() { return $this->listingActionName; }
	protected function setListingActionName($value) { $this->listingActionName = $value; }
	
	protected function getPrintActionName() { return $this->printActionName; }
	protected function setPrintActionName($value) { $this->printActionName = $value; }
	
	protected function getHistoryActionName() { return $this->historyActionName; }
	protected function setHistoryActionName($value) { $this->historyActionName = $value; }
	
	protected function getPrintListingActionName() { return $this->printListingActionName; }
	protected function setPrintListingActionName($value) { $this->printListingActionName = $value; }
	
	protected function getPrintTemplateName() { return $this->printTemplateName; }
	protected function setPrintTemplateName($value) { $this->printTemplateName = $value; }
	
	protected function getPrintListingTemplateName() { return $this->printListingTemplateName; }
	protected function setPrintListingTemplateName($value) { $this->printListingTemplateName = $value; }
	
	protected function getDownloadActionName() {
		return $this->downloadActionName;
	}
	protected function setDownloadActionName($value) {
		$this->downloadActionName = $value;
	}
	
	protected function getRelatedControllers() {
		return $this->relatedControllers;
	}
	
	protected function addRelatedController($value) {
		$this->relatedControllers[] = $value;
	}
	
}

