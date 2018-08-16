<?php
 
 /*
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
//App::uses('Paypal', 'Paypal.Lib');
App::uses('AppController', 'Controller');
class PaymentController extends AppController {

	/**
	 * This controller does not use a model
	 *
	 * @var array
	 */
	public $uses = array('Order','User', 'MainCategory', 'Subcategory', 'Picture', 'Page', 'FaqQuestion', 'FaqAnswer', 'Event', 'Message', 'Size', 'Country', 'Card','Test','Fbpage','Domain','Account','Customerorder','Customer','Shipping','Customeemail','Sizeprice','Themebanner','Guestemail');
	var $helpers = array('Js','Paginator');
	public $themename = 'settings';
	public $settingstheme = 'settings';
	public $components = array('Paginator', 'Upload','Stripe.Stripe'  /*  ,'Security' => array('csrfExpires' => '+1 hour') */);
	
	function beforeFilter() {
		parent::beforeFilter();
		$this -> Auth -> allow('*', 'getSize', 'getsubcategory','mywebsite','getcategory');
		$userid = $this -> Auth -> user('User.userid');
	}
	
	function requestpermission(){
		$this->autoRender = false;
		App::import('Vendor','PayPalpermission/Adaptive'); 
		/**
		 * Third Party User Values
		 * These can be setup here or within each caller directly when setting up the PayPal object.
		 */
		$api_subject = '';	// If making calls on behalf a third party, their PayPal email address or account ID goes here.
		$device_id = '';
		$device_ip_address = $_SERVER['REMOTE_ADDR'];
		/**
		 * Enable Headers
		 * Option to print headers to screen when dumping results or not.
		 */
		$print_headers = false;

		/**
		 * Enable Logging
		 * Option to log API requests and responses to log file.
		 */
		$log_results = false;
		$log_path = $_SERVER['DOCUMENT_ROOT'].'/logs/';
		$domain = 'https://www.shoptt.co/';

		$PayPalConfig = array(
					  'Sandbox' => Sandbox_mode,
					  'DeveloperAccountEmail' => shalePaypalEmail,
					  'ApplicationID' => paypalAppId,
					  'DeviceID' => '',
					  'IPAddress' => $_SERVER['REMOTE_ADDR'],
					  'APIUsername' => paypalUserId,
					  'APIPassword' => paypalPassword,
					  'APISignature' => paypalSignature,
					  'APISubject' => '',
                      'PrintHeaders' => $print_headers, 
					  'LogResults' => $log_results, 
					  'LogPath' => $log_path,
					);
		
		$PayPal = new Adaptive($PayPalConfig);

		// Prepare request arrays
		$Scope = array(
					'EXPRESS_CHECKOUT', 
					'DIRECT_PAYMENT', 
					'BILLING_AGREEMENT', 
					'REFERENCE_TRANSACTION', 
					'TRANSACTION_DETAILS',
					'TRANSACTION_SEARCH',
					'RECURRING_PAYMENTS',
					'ACCOUNT_BALANCE',
					'ENCRYPTED_WEBSITE_PAYMENTS',
					'REFUND',
					'NON_REFERENCED_CREDIT',
					'BUTTON_MANAGER',
					'MANAGE_PENDING_TRANSACTION_STATUS',
					'RECURRING_PAYMENT_REPORT',
					'EXTENDED_PRO_PROCESSING_REPORT',
					'EXCEPTION_PROCESSING_REPORT',
					'ACCOUNT_MANAGEMENT_PERMISSION',
					'ACCESS_BASIC_PERSONAL_DATA',
					'ACCESS_ADVANCED_PERSONAL_DATA'
				); 

		$RequestPermissionsFields = array(
			'Scope' => $Scope, 				// Required.   
			'Callback' => $domain.'payment/RequestPermissionsCallback'//callback function that specifies actions to take after the account holder grants or denies the request.
		);

		$PayPalRequestData = array('RequestPermissionsFields' => $RequestPermissionsFields);

		// Pass data into class for processing with PayPal and load the response array into $PayPalResult
		$PayPalResult = $PayPal->RequestPermissions($PayPalRequestData);
		// Write the contents of the response array to the screen for demo purposes.
		if($PayPalResult['Ack'] == 'Success'){
			header('location:'.$PayPalResult['RedirectURL']);die;
		}else{
			$this -> Session -> setFlash(__($PayPalResult['Errors'][0]['Message']), '/message/error');
			$this->redirect(array('controller' => 'settings', 'action' => 'mywebsite'));
		}
		die;
	}	
	public function RequestPermissionsCallback(){
		$this->autoRender = false;
		App::import('Vendor','PayPalpermission/Adaptive'); 	
		App::import('Vendor','PayPal/Auth/Oauth/AuthSignature');
		/**
		 * Third Party User Values
		 * These can be setup here or within each caller directly when setting up the PayPal object.
		 */
		$api_subject = '';	// If making calls on behalf a third party, their PayPal email address or account ID goes here.
		$device_id = '';
		$device_ip_address = $_SERVER['REMOTE_ADDR'];
		$print_headers = false;
		$log_results = false;
		$log_path = $_SERVER['DOCUMENT_ROOT'].'/logs/';
		$domain = 'https://www.shoptt.co/';
		$PayPalConfig = array(
					  'Sandbox' => Sandbox_mode,
					  'DeveloperAccountEmail' => shalePaypalEmail,
					  'ApplicationID' => paypalAppId,
					  'DeviceID' => '',
					  'IPAddress' => $_SERVER['REMOTE_ADDR'],
					  'APIUsername' => paypalUserId,
					  'APIPassword' => paypalPassword,
					  'APISignature' => paypalSignature,
					  'APISubject' => '',
                      'PrintHeaders' => $print_headers, 
					  'LogResults' => $log_results, 
					  'LogPath' => $log_path,
					);
		
		$PayPal = new Adaptive($PayPalConfig);
		
		// Prepare request arrays
		if(isset($this->params['url']['request_token']) && !empty($this->params['url']['request_token'])){
			$GetAccessTokenFields = array(
									'Token' => $this->params['url']['request_token'], 		// Required.  The request token from the response to RequestPermissions
									'Verifier' =>$this->params['url']['verification_code']	// Required. verification code returned in the redirect from PayPal to the return URL.
								);

			$PayPalRequestData = array('GetAccessTokenFields' => $GetAccessTokenFields);

			// Pass data into class for processing with PayPal and load the response array into $PayPalResult
			$PayPalResult = $PayPal->GetAccessToken($PayPalRequestData);
		
			// Write the contents of the response array to the screen for demo purposes.
			if($PayPalResult['Ack'] == 'Success'){
				$Token  		= $PayPalResult['Token'];
				$TokenSecret 	= $PayPalResult['TokenSecret'];
				// Prepare request arrays
				$EndPointURL = 'https://svcs.paypal.com/Permissions/GetBasicPersonalData';
				
				$auth = new AuthSignature();
				$headers = $PayPal->BuildHeaders(false);
				
				$headeauth = $auth->genSign($PayPalConfig['APIUsername'],$PayPalConfig['APIPassword'],$Token,$TokenSecret,'POST',$EndPointURL);
				
				$PayPalConfig['oauth_signature'] = "token=".$Token.",signature=".$headeauth['oauth_signature'].",timestamp=".$headeauth['oauth_timestamp'];
				
				$AttributeList = array(
							'http://axschema.org/namePerson/first',
							'http://axschema.org/namePerson/last',
							'http://axschema.org/contact/email',
							'http://axschema.org/contact/fullname',
							'http://openid.net/schema/company/name',
							'http://axschema.org/contact/country/home',
							'https://www.paypal.com/webapps/auth/schema/payerID'
						);
				$PayPal = new Adaptive($PayPalConfig);
				// Pass data into class for processing with PayPal and load the response array into $PayPalResult
				$PayPalResultData = $PayPal->GetBasicPersonalData($AttributeList);
				if($PayPalResultData['Ack'] == 'Success'){
					if($PayPalResultData['PersonalData']){
						$userid = $this -> Auth -> user('User.userid');
						$data['userid']  = $userid;
						$data['paypal_email']  = $PayPalResultData['PersonalData'][1]['PersonalDataValue'];
						$data['payerID']  = $PayPalResultData['PersonalData'][4]['PersonalDataValue'];
						$this->User->save($data);
						$this -> Session -> setFlash(__('Paypal details have been updated successfully.'), '/message/success');
						$this->redirect(array('controller' => 'settings', 'action' => 'mywebsite'));
					}
				}else{
					$this -> Session -> setFlash(__($PayPalResult['Errors'][0]['Message']), '/message/error');
					$this->redirect(array('controller' => 'settings', 'action' => 'mywebsite'));
				}
			}else{
				$this -> Session -> setFlash(__($PayPalResult['Errors'][0]['Message']), '/message/error');
				$this->redirect(array('controller' => 'settings', 'action' => 'mywebsite'));
			}
		}else{
			$this -> Session -> setFlash(__('Your request has been denied.'), '/message/error');
			$this->redirect(array('controller' => 'settings', 'action' => 'mywebsite'));
		}
		
	}
}
