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

class Snazzware_Widget_Paginated_Grid_Column_DateTime extends Snazzware_Widget_Paginated_Grid_Column {
	
	protected $helper = 'Widget_Paginated_Grid_Content_DateTime';
	
	public function translateFilter(&$filter) {
		
		$value = trim($filter['value']);
		
		try {
			$parsed = date_parse_from_format(ConfigUtils::get('global','phpDateFormat','Y-m-d'),$value);
			
			if (!$parsed['year']) $year = date('Y');
			else $year = $parsed['year'];
			
			if (!$parsed['month']) $month = date('m');
			else $month = $parsed['month'];
			
			if (!$parsed['day']) $day = date('d');
			else $day = $parsed['day'];
			
			if ($month>12) $month = 1;
			if ($day>31) $day = 1;			

			$year = str_pad($year,4,'0',STR_PAD_LEFT);
			$month = str_pad($month,2,'0',STR_PAD_LEFT);
			$day = str_pad($day,2,'0',STR_PAD_LEFT);
			
			$value = new DateTime("{$year}-{$month}-{$day}");	
			
			$filter['value'] = $value->format(ConfigUtils::get('global','databaseDateFormat','Y-m-d'));					
		} catch (Exception $e) {
				
		}		
		
		if ($filter['op']=='like') $filter['op'] = '=';		
	}
	
}

