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

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Snazzware/Form/Element/Xhtml.php';

/**
 * Submit form element
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Submit.php 23484 2010-12-10 03:57:59Z mjh_ca $
 */
class Snazzware_Form_Element_Submit extends Snazzware_Form_Element_Xhtml
{
    /**
     * Default view helper to use
     * @var string
     */
    public $helper = 'Form_Submit';

    /**
     * Constructor
     *
     * @param  string|array|Zend_Config $spec Element name or configuration
     * @param  string|array|Zend_Config $options Element value or configuration
     * @return void
     */
    public function __construct($spec, $options = null)
    {
        if (is_string($spec) && ((null !== $options) && is_string($options))) {
            $options = array('label' => $options);
        }

        if (!isset($options['ignore'])) {
            $options['ignore'] = true;
        }

        parent::__construct($spec, $options);
    }

    /**
     * Return label
     *
     * If no label is present, returns the currently set name.
     *
     * If a translator is present, returns the translated label.
     *
     * @return string
     */
    public function getLabel()
    {
        $value = parent::getLabel();

        if (null === $value) {
            $value = $this->getName();
        }

        if (null !== ($translator = $this->getTranslator())) {
            return $translator->translate($value);
        }

        return $value;
    }

    /**
     * Has this submit button been selected?
     *
     * @return bool
     */
    public function isChecked()
    {
        $value = $this->getValue();

        if (empty($value)) {
            return false;
        }
        if ($value != $this->getLabel()) {
            return false;
        }

        return true;
    }

    /**
     * Default decorators
     *
     * Uses only 'Submit' and 'DtDdWrapper' decorators by default.
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Tooltip')
                 ->addDecorator('ViewHelper');                 
        }
        return $this;
    }
}