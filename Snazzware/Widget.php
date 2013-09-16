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

class Snazzware_Widget {

	const WIDGET = 'WIDGET';
	const WIDGET_DECORATOR = 'WIDGET_DECORATOR';

	protected $helper = 'Widget_Default';
	protected $eventsHelper = 'Widget_Events';
	
	protected $view = null;
	protected $request = null;
	protected $renderPending = false;
	
	protected static $jqueryPluginHelper = 'Widget_JQPlugin';
	protected static $factory = null;
	protected static $widgetStyles = array();
	protected static $widgetScripts = array();
	
	protected $decorators = array();
	protected $widgets = array(); // child widgets
	
	/*
	 * Options
	* -------
	* id			used by view helpers to specify the html entity id of the rendered object, usually the outer container if it is a complex object
	* name			used on the server side to identify the widget in the current context
	*
	*/
	protected $options = array();

	public function setName($value) {
		$this->options['name'] = $value;
	}
	public function getName() {
		if (!isset($this->options['name'])) return get_class($this);
		else return $this->options['name'];
	}

	public function appendInnerScript($script) {
		$this->setOption('innerScript',$this->getOption('innerScript','')."\r\n".$script);
	}
	
	public function setId($value) {
		$this->options['id'] = $value;
	}
	public function getId() {
		if (!isset($this->options['id'])) {
			if (isset($this->options['name'])) {
				return get_class($this).'_'.$this->options['name'];
			} else {
				return get_class($this);
			}
		}
		else return $this->options['id'];
	}

	public function setHelper($value) {
		$this->helper = $value;
	}
	public function getHelper() {
		if ($this->helper=='') $this->setHelper(get_class($this)); return $this->helper;
	}

	public function setEventsHelper($value) {
		$this->eventsHelper = $value;
	}
	public function getEventsHelper() {
		if ($this->eventsHelper=='') $this->setEventsHelper(get_class($this)); return $this->eventsHelper;
	}
	
	public function addDecorator($name,$decorator) {
		$this->decorators[$name] = $decorator;
		return $this;
	}
	
	public function addDecorators($decorators) {
		foreach ($decorators as $name=>$decorator) {
			$this->addDecorator($name,$decorator);
		}
		return $this;
	}
	
	public function removeDecorator($name) {
		if (isset($this->decorators[$name])) {
			unset($this->decorators[$name]);
		}	
		return $this;
	}
	
	public function getDecorator($name) {
		if (isset($this->decorators[$name])) return $this->decorators[$name];
		else return null;
	}
	
	public function getDecorators() {
		return $this->decorators;
	}
	
	public function clearDecorators() {
		$this->decorators = array();
		return $this;
	}
	
	public function setDecorators($value) {
		$this->clearDecorators();
		return $this->addDecorators($value);
	}

	public function createWidget($type, $name, $options = array()) {
		$class = $this->getFactory()->getPluginLoader(self::WIDGET)->load($type);
		return new $class(array_merge(array('name'=>$name),$options));
	}
	
	public function createDecorator($type, $name='', $options = array()) {		
		$class = $this->getFactory()->getPluginLoader(self::WIDGET_DECORATOR)->load($type);
		return new $class(array_merge(array('name'=>$name),$options));
	}

	public function __construct($options = array()) {
		$this->setOption('renderContainer',true);
		$this->options = array_merge($this->options, $options);
		
		$this->loadDefaultDecorators();
	}

	public function loadDefaultDecorators() {
		$decorators = $this->getDecorators();
		if (empty($decorators)) {
			$this->addDecorator('ViewHelper',$this->createDecorator('ViewHelper'))
				->addDecorator('Container',$this->createDecorator('Container'));	
		}		
	} 
	
	public function setOption($option, $value) {
		$this->options[$option] = $value;
	}

	public function setOptions($options, $merge = true) {
		if ($merge) {
			$this->options = array_merge($this->options, $options);
		} else {
			$this->options = $options;
		}
	}

	public function getOption($option, $default=null) {
		if (isset($this->options[$option])) {
			return $this->options[$option];
		} else return $default;
	}

	public function getOptions() {
		return $this->options;
	}

	public function render(Zend_View_Interface $view = null) {
		$view = $this->getValidView($view); // ensures we have a non-null view

		$helper = $this->getHelper();

		$zfext = __ZFEXT_PREFIX;

		$xhtml = '';		
		foreach ($this->getDecorators() as $name=>$decorator) {
			$decorator->render($view,$this,$xhtml);
		}

		$events = $this->getOption('events');

		if ($events != null) {
			$eventsHelper = $this->getEventsHelper();
			$xhtml .= $view->$eventsHelper($view,$this);
		}

		if (!isset(self::$widgetStyles[get_class($this)])) {
			self::$widgetStyles[get_class($this)] = $this->renderStyling();
		}
		
		if (!isset(self::$widgetScripts[get_class($this)])) {
			self::$widgetScripts[get_class($this)] = $this->renderScripting();
		}
		
		return $xhtml;
	}

	public function handleRequest($request,$view) {
		$this->setRequest($request);
		$this->setView($view);
		
		$handled = false;
		
		$params = $request->getParams();
		
		if (isset($params['_widgetAction'])) {
			$actionMethod = strtolower($params['_widgetAction']).'Action';
			
			if (method_exists($this,$actionMethod)) {				
				$results = $this->$actionMethod();
				$handled = true;
			}			
		}
		
		if ($handled) {
			if ($results != null) echo $results;
			if ($this->getRenderPending()) echo $this->render($view);
		}
		
		return $handled;
	}

	public function getValidView($view = null)
	{
		if ($view == null) {
			require_once 'Zend/Controller/Action/HelperBroker.php';
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			$view = $viewRenderer->view;
		}

		return $view;
	}

	public static function getFactory() {
		if (self::$factory == null) {
			self::$factory = Snazzware_Factory::getFactory('Widget',array(
					'types'=>array(
							self::WIDGET=>array(
								'prefixSegment' => '',
								'pathSegment' => ''
							),
							self::WIDGET_DECORATOR=>array(
								'prefixSegment' => 'Widget_Decorator',
								'pathSegment' => 'Widget/Decorator'
							)
					)
			));
							
			self::$factory->addPrefixPath('Snazzware_Widget', 'Snazzware/Widget/', self::WIDGET);
			self::$factory->addPrefixPath('Snazzware_Widget_Decorator', 'Snazzware/Widget/Decorator', self::WIDGET_DECORATOR);
		}

		return self::$factory;
	}
	
	public static function renderJqueryPlugin($view) {
		$jqueryPluginHelper = self::$jqueryPluginHelper;
		
		return $view->$jqueryPluginHelper($view);
	}
	
	public function renderStyling() {
		return '';
	}
	
	public function renderScripting() {
		return '';
	}
	
	public function setView($view) { $this->view = $view; }
	public function getView() { return $this->view; }
	
	public function setRequest($request) { $this->request = $request; }
	public function getRequest() { return $this->request; }
	
	public function setRenderPending($value) { $this->renderPending = $value; }
	public function getRenderPending() { return $this->renderPending; }
	
	public static function renderCachedStylingAndScripting() {
		return "<style>".implode("\r\n",self::$widgetStyles)."</style><script>".implode("\r\n",self::$widgetScripts)."</script>";
	}
	
	// Child Widgets
	public function getWidgets() {
		if (!is_array($this->getOption('widgets',null))) $this->setOption('widgets',array());
	
		$widgets = $this->getOption('widgets');
	
		$sorted = array();
		$order = $this->getWidgetOrder();
		foreach ($order as $name) {
			if (isset($widgets[$name])) {
				$sorted[$name] = $widgets[$name];
				unset($widgets[$name]);
			}
		}
		return $sorted + $widgets;
	}
	public function addWidget(Snazzware_Widget $widget) { $widget->setOption('parent',$this); $widgets = $this->getWidgets(); $widgets[$widget->getName()] = $widget; $this->setWidgets($widgets); }
	public function setWidgets($value) { $this->setOption('widgets',$value); }
	public function setWidgetOrder($value) { $this->setOption('widgetOrder',$value); }
	public function getWidgetOrder() { return $this->getOption('widgetOrder',array()); }
	

}