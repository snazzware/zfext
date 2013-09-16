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


/*
 * Requires jQuery and fancybox.
 * 
 * Provides functions to display "alert box" style messages of types success, error, and info, on the subsequent page rendering.
 * For example, after processing an action such as delete, save, etc. this allows the status of the operation to easily be displayed
 * to the user after a redirect back to the post-action landing page.
 * 
 * E.g. deleteAction completes, it calls Alert::success('The delete was successful');
 * 
 * In the layout used by all pages, a call is made to echo Alert::render();
 * 
 * If any message was previously set for the current user's session, it is rendered, and then the message is cleared from the session.
 * 
 */
class Snazzware_Alert {
	
	protected static $helper = "Alert";
	
	/*
	 * Sets current alert to "success"
	 * 
	 * @param string $message The message to use.
	 */
	public static function success($message) {		
		State::set('alert','message',$message);
		State::set('alert','icon',ConfigUtils::get('alert','icon_success',''));
		State::set('alert','caption',ConfigUtils::get('alert','caption_success','Success'));
	}
	
	/*
	 * Sets current alert to "error" with a custom message.
	 * 
	 * @param string $message The message to use.
	 */
	public static function error($message) {		
		State::set('alert','message',$message);
		State::set('alert','icon',ConfigUtils::get('alert','icon_error',''));
		State::set('alert','caption',ConfigUtils::get('alert','caption_error','Error'));
	}
	
	/*
	 * Sets current alert to "info" with a custom message.
	 * 
	 * @param string $message The message to use.
	 */
	public static function info($message) {		
		State::set('alert','message',$message);
		State::set('alert','icon',ConfigUtils::get('alert','icon_info',''));
		State::set('alert','caption',ConfigUtils::get('alert','caption_info','Information'));
	}
	
	/*
	 * If a message exists, returns a javascript block which displays the alert inside a fancybox.
	 */
	public static function render($view) {		
		$xhtml = '';
		
		if (State::get('alert','message','')!='') {
			$helper = self::getHelper();
			
			$xhtml .= $view->$helper(
				State::get('alert','caption',''),
				State::get('alert','message',''),
				State::get('alert','icon',ConfigUtils::get('alert','icon_info'))
			);			
			
			State::set('alert','message','');
		}
		
		return $xhtml;
	}
	
	public static function getHelper() { return self::$helper; }
	public static function setHelper($value) { self::$helper = $value; }
	
}