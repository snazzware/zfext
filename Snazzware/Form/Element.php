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

class Snazzware_Form_Element extends Zend_Form_Element {
	
	protected $viewHelperDecoratorName = 'ViewHelper';
	private $_readonly = false;
	private $_editable = true;
	protected $_mPropertyAdapter = null;	
	public $options = array('readonly'=>false, 'editable'=>true);
	
	public function setName($name)
    {
    	$name = str_replace('.','_',$name);
    	parent::setName($name);
    }
	
	public function init() {
		parent::init();
		
		$this->setAttrib('autocomplete', 'off');
    }
	
	public function setPropertyAdapter($value) { 
		$this->_mPropertyAdapter = $value;
		$this->_mPropertyAdapter->setElement($this); 
	}
	
	public function getPropertyAdapter() { return $this->_mPropertyAdapter; }
	
	public function setReadOnly($value) {
		$this->options['readonly'] = $value;		
	}
	public function getReadOnly() { if (isset($this->options['readonly'])) return $this->options['readonly']; else return false; }

	public function setEditable($value) {
		$this->options['editable'] = $value;		
	}
	public function getEditable() { if (isset($this->options['editable'])) return $this->options['editable']; else return false; }
	
	/**
     * Load default decorators
     *
     * @return Zend_Form_Element
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $zfext = __ZFEXT_PREFIX;
        
        $typetokens = explode('_',$this->getType());
        $type = strtolower(array_pop($typetokens));
        $cssclass = "{$zfext}-form-element-{$type}";
        
        // TODO : make a callback like is used for getId to get readonly at render time to set css...
        // if ($this->getReadOnly()) $cssclass .= '-readonly';
        
        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $getId = create_function('$decorator',
                                     'return $decorator->getElement()->getId()
                                             . "-element";');
            $getClass = create_function('$decorator',"
            	\$typetokens = explode('_',\$decorator->getElement()->getType());
        		\$type = strtolower(array_pop(\$typetokens));
        		\$cssClass =  '{$zfext}-form-element-'.\$type;
            	\$class = '{$zfext}-form-element '.\$cssClass;
            	if (\$decorator->getElement()->hasErrors()) \$class .= ' {$zfext}-form-element-error '.\$cssClass.'-error ';
            	if (\$decorator->getElement()->getReadOnly()) \$class .= ' {$zfext}-form-element-readonly '.\$cssClass.'-readonly ';
            	return \$class;
            ");
            $this->addDecorator($this->viewHelperDecoratorName)
                 ->addDecorator('Errors')
                 ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                 ->addDecorator('HtmlTag', array('tag' => 'div',
                 								'class' => array('callback' => $getClass),
                                                 'id'  => array('callback' => $getId)))
                 ->addDecorator('Label', array('class' => "{$zfext}-form-label"));
        }
        return $this;
    }
    
    public function renderLabel($view = null)
    {
    	$decorators = $this->getDecorators();
    	$this->clearDecorators();
    	$this->addDecorator('Label', array('class' => __ZFEXT_PREFIX.'-form-label'));
    	$xhtml = $this->render($view);
    	$this->setDecorators($decorators);
    	
    	return $xhtml;
    }
    
    public function renderElement($view = null) 
    {
    	$decorators = $this->getDecorators();    	    	
    	$this->removeDecorator('Label');    	
    	$xhtml = $this->render($view);
    	$this->setDecorators($decorators);
    	
    	return $xhtml;
    }
    
    /**
     * Utility method to add uppercase text-transform rule and apply StringToUpper filter
     * 
     * @bool $value 
     */
    public function setUppercase($value = true) {
    	if ($value == true) {
	    	$style = trim($this->getAttrib('style'));
			if ((strlen($style)>0) && (substr($style,-1)!=';')) $style .= '; ';
			$style .= ' text-transform: uppercase; ';
			$this->setAttrib('style',$style);
			$this->addFilter('StringToUpper');
    	} else {
    		// TODO : un-uppercase
    	}
    }
	
}


?>