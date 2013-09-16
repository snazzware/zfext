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

class Snazzware_Printer {	
	
	static private $mBlnDomPDFStatus = false;
	
	static private $mDefaultPaper = 'letter';
	
	static private function initDomPDF() {
		if (!self::$mBlnDomPDFStatus) {
			
			
			//Set up printing
			require_once('Snazzware/3rdparty/dompdf/0.6.0beta3/dompdf_config.inc.php');
			//require_once('Snazzware/3rdparty/dompdf/dompdf_config.inc.php');
				
			$autoloader = \Zend_Loader_Autoloader::getInstance();
			$autoloader->pushAutoloader('DOMPDF_autoload', '');
			
			self::$mBlnDomPDFStatus = true;
		}
	}
	
	static public function setDefaultPaper($paper) {
		self::$mDefaultPaper = $paper;
	}
	
	static public function getDefaultPaper() {
		return self::$mDefaultPaper;
	}
	
	static public function html2pdf($html, $options = array()) {
		self::initDomPDF();
		
		$dompdf = new \DOMPDF();
		
		
		if (isset($options['orientation'])) $orientation = $options['orientation'];
		else $orientation = 'portrait';
		
		if (isset($options['paper'])) $paper = $options['paper'];
		else $paper = self::getDefaultPaper();
		
		$dompdf->set_paper($paper,$orientation);		
		
		$dompdf->load_html($html);
		$dompdf->render();
		
		if (isset($options['outputPath'])) {
			file_put_contents($options['outputPath'], $dompdf->output());
			return true;
		} else {
			return $dompdf->output();			
		}
	}
	
	static public function getPrintersAsOptions() {
		$Client_Login_Token = self::getCloudprintToken();
		
		$client = new \Zend_Gdata_HttpClient();
		$client->setClientLoginToken($Client_Login_Token);
		
		$client->setHeaders('Authorization','GoogleLogin auth='.$Client_Login_Token);
		$client->setUri('http://www.google.com/cloudprint/interface/search');
		 
		$response = $client->request(\Zend_Http_Client::POST);
		 
		$PrinterResponse = json_decode($response->getBody());
		
		$options = array();
		
		foreach ($PrinterResponse->printers as $printer) {
			$options[$printer->id] = $printer->name;
		}
		
		return $options;
	}
	
	static public function getCloudprintToken() {
		if (State::get('google','cloudprint_token',null)==null) {
			$client = \Zend_Gdata_ClientLogin::getHttpClient(ConfigUtils::get('google','cloudprint_username',''), ConfigUtils::get('google','cloudprint_password',''), 'cloudprint');
			
			State::set('google','cloudprint_token',$client->getClientLoginToken());
		}

		return State::get('google','cloudprint_token',null);
	}
	
	static public function html($html,$jobtitle='Untitled') {
		$data = self::html2pdf($html);
		
		$Client_Login_Token = self::getCloudprintToken();
		
		$client = new \Zend_Gdata_HttpClient();
		$client->setClientLoginToken($Client_Login_Token);
		
		$client->setHeaders('Authorization','GoogleLogin auth='.$Client_Login_Token);
		$client->setHeaders('X-CloudPrint-Proxy','Mimeo');
		
		//GCP Services - Register
		$client->setUri('https://www.google.com/cloudprint/interface/submit');
		
		$client->setParameterPost('printerid', State::get('google','cloudprint_printerid',ConfigUtils::get('google','cloudprint_default_printerid','')));
		$client->setParameterPost('title', $jobtitle);
		$client->setParameterPost('contentTransferEncoding','base64');
		$client->setParameterPost('content',base64_encode($data));
		$client->setParameterPost('contentType','application/pdf');
		
		$response = $client->request(\Zend_Http_Client::POST);
		
		$PrinterResponse = json_decode($response->getBody());
		
		return (isset($PrinterResponse->success) && $PrinterResponse->success==1); 		
	} 
	
}