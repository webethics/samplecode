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
class SettingsController extends AppController {

	/**
	 * This controller does not use a model
	 *
	 * @var array
	 */
	public $uses = array('Order','User', 'MainCategory', 'Subcategory', 'Picture', 'Page', 'FaqQuestion', 'FaqAnswer', 'Event', 'Message', 'Size', 'Country', 'Card','Test','Fbpage','Domain','Account','Customerorder','Customer','Shipping','Customeemail','Sizeprice','Themebanner','Guestemail','Tempimage');
	var $helpers = array('Js','Paginator');
	public $themename = 'settings';
	public $settingstheme = 'settings';
	public $components = array('Paginator', 'Upload','Stripe.Stripe'  ,'Security' => array('csrfExpires' => '+1 hour'));
	
	function beforeFilter() {
		/*-------------All the things that we need to call before any of the function/method being called -----------------------------*/
		parent::beforeFilter();
		$this -> Auth -> allow('*', 'getSize', 'getsubcategory','mywebsite','getcategory');
		$userid = $this -> Auth -> user('User.userid');
		$paypal_data =  $this -> User -> find('first', array('fields'=>array('paypal_email'),'conditions' => array('userid' => $userid)));
		$this->set('paypal_data',$paypal_data);
		$product_count  = $this -> Picture -> find('count', array('fields'=>array('Picture.pictureid'),'conditions' => array('Picture.userid' => $userid)));
		$this->set('product_count',$product_count);
		if($this->action == 'editPost' || $this->action == 'newPost'){
			$this->Security->unlockedFields = array('size','sizeprice','qty','quantity');
		}
		if($this->action == 'addcustomcategory' || $this->action == 'editcustomcategory'){
			$this->Security->unlockedFields = array('subcategory');
		}
			
		
		if (isset($this->Security) && $this->action == 'editPost' || $this->action == 'editcustomcategory') {
			$this->Security->validatePost = false;
		} 
	   $this->copyEmails();
	}

	public function index(){
		$this->redirect(array('controller' => 'settings', 'action' => 'account'));
	}
	
	/*-------------Sending Profile Image to amazon server -----------------------------*/
	function process_profile_image($original_image,$size=''){
		App::uses('AmazonS3', 'AmazonS3.Lib');
		$AmazonS3 = new AmazonS3(array('AKIAJUN4MN7X6EOAMK4A', 'iQwPlU6b6XK3b1G8oBQ/RLxwdjOcYaMF5rbbSq3c', 'shoptt'));
			
		if (isset($original_image) && !empty($original_image)){
			if(!$size)
				$imagename = $_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/'.$original_image;
			else
				$imagename = $_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/'.$size.'/'.$original_image;
			
			if(!$size)
				$folder = 'original';
			else	
				$folder  = $size;
			if($AmazonS3->put($imagename, 'profile/'.$folder)){
				die('///');
			}else{
				$this -> request -> data['User']['profilepic']=$original_image;
			}	
		} else {
			unset($this -> request -> data['profile'.$n.'pic']);
		}
		
		chmod($imagename, 755); 
		unlink($imagename);
		return $this -> request -> data;
	}
	
	/*-------------Manage User account Information -----------------------------*/
	function account($var='') {
		$this -> layout = '/settings/setting_default';
		$pageVar['menu']='account';
		$userid = $this -> Auth -> user('User.userid');
		
		if ($this -> request -> data) {
			if(isset($this -> request -> data['Picture']['filestatus']) && $this -> request -> data['Picture']['filestatus']=='files'){
				App::uses('UploadHandler', 'Vendor');
				$upload_handler = new UploadHandler(); 
				$i  = 1;
				foreach($upload_handler->response['files'] as $key=>$value){
					$img = array_reverse(explode('/',$value->url));
					$simg = array_reverse(explode('/',$value->mediumUrl));
					$mimg = array_reverse(explode('/',$value->smallUrl));
					$limg = array_reverse(explode('/',$value->largeUrl));
					
					$this -> request -> data['User']['profilepic'] = $img[0];
					
					$this -> request -> data['User']['sprofilepic'] = $simg[0];
					
					$this -> request -> data['User']['mprofilepic'] = $mimg[0];
					
					$this -> request -> data['User']['lprofilepic'] = $limg[0];
					
					$this->process_profile_image($this -> request -> data['User']['profilepic'],'');
					
					$this->process_profile_image($this -> request -> data['User']['lprofilepic'],'large');	
					
					$this->process_profile_image($this -> request -> data['User']['mprofilepic'],'medium');	
					
					$this->process_profile_image($this -> request -> data['User']['sprofilepic'],'small');
					
				}
			} 
			
			$mail = $this -> check_mail($this -> request -> data['User']['email'], $userid);
			
			$username = $this -> check_username($this -> request -> data['User']['name'], $userid);

			if ($this -> Auth -> user('User.email') == $this -> request -> data['User']['email']) {
				unset($this -> request -> data['User']['email']);
				$mail = 0;
			}
			
			if ($this -> Auth -> user('User.name') == $this -> request -> data['User']['name']) {
				$user_name  = $this -> request -> data['User']['name'];
				unset($this -> request -> data['User']['name']);
				$username = 0;
			}else{
				$user_name  = $this -> request -> data['User']['name'];
			}
				
			$error=0;
			if($mail!==0){
				$this -> Session -> setFlash(__('Email already exists in our records.'), '/message/success');
				$error=1;
			}elseif($username !==0){
				$this -> Session -> setFlash(__('Username already exists in our database.'), '/message/success');
				$error=1;
			}
			if ($error == 0) {
				$this -> User -> id = $userid;
				if ($this -> User -> save($this -> request -> data)) {
					$this->Session->write('Auth.User.User.username',$user_name);
					$domaindata = $this -> Domain -> find('first', array('conditions' => array('userid' => $userid)));
					if($domaindata){ 
						$id = $domaindata['Domain']['id'];
						$data['Domain']['userid']		= $userid; 
						$data['Domain']['id']			= $id;
						$data['Domain']['storedomain']	= $user_name.'.shoptt.co';
						$this->Domain->save($data);
					}
					$this->Session->write('Auth.User.User.paypal_email',$this -> request -> data['User']['paypal_email']);
					$this -> Session -> setFlash(__('Records have been updated successfully.'), '/message/success');
				}
				$this->Picture->query("update pictures set currency = '".$this -> request -> data['User']['currency']."' where userid = '".$this -> Auth -> user('User.userid')."'");
			} 
			
			if(isset($this -> request -> data['User']['store']) && $this -> request -> data['User']['store'] != "") {		
				$this -> redirect(array('controller' => 'settings', 'action' => 'mywebsite'));
			}else{
				$this -> redirect(array('controller' => 'settings', 'action' => 'account'));
			}	
		}
		$countries = $this -> Country -> find('list', array('fields' => array('countrycode', 'country')));
		
		$paypal_data =  $this -> User -> find('first', array('fields'=>array('paypal_email'),'conditions' => array('userid' => $userid)));
		$this->set('paypal_data',$paypal_data);
		
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		$this -> set('countries', $countries);
		if($var){
			$this->set('slug',$var);
		}
		$this->set('pageVar',$pageVar);
		unset($this -> request -> data['User']['password']);

	}

	//function for checking username  when create an account
	function check_username($username = "", $id = '') {
		$this -> layout = $this -> autoRender = false;
		$exist_username = $this -> User -> find('count', array('fields'=>array('User.userid'),'conditions' => array('name' => $username, 'userid !=' => $id)));
		return $exist_username;
	}
	
	/*-------------Adding new Product to database-----------------------------*/
	function newPost() {
	
		ini_set("upload_max_filesize","200M");
		$userid=$this -> Auth -> user('User.userid');
		$this -> layout = '/settings/setting_default';
		$pageVar['menu']='post_product';
		
		if ($this -> request -> data) {
			/*-------------Calling the upload handler for the image-----------------------------*/
			App::uses('UploadHandler', 'Vendor');
			$upload_handler = new UploadHandler(); 
			if($upload_handler->response){
				
				if (isset($this -> request -> data['Picture']['qty']) && FALSE) {
					for ($i = 0; $i < count($this -> request -> data['Picture']['qty']); $i++)
						$size[] = array('sizeid' => '', 'quantity' => '');

				}
				$this -> request -> data['Picture']['userid'] = $this -> Auth -> user('User.userid');
				$i  = 1;
				foreach($upload_handler->response['files'] as $key=>$value){
					$img = array_reverse(explode('/',$value->url));
					$simg = array_reverse(explode('/',$value->mediumUrl));
					$mimg = array_reverse(explode('/',$value->smallUrl));
					$limg = array_reverse(explode('/',$value->largeUrl));
					
					$this -> request -> data['Picture']['picture'.$i.'url'] = $img[0];
					
					$this -> request -> data['Picture']['spicture'.$i.'url'] = $simg[0];
					
					$this -> request -> data['Picture']['mpicture'.$i.'url'] = $mimg[0];
					
					$this -> request -> data['Picture']['lpicture'.$i.'url'] = $limg[0];
					
					$this->process_image($this -> request -> data['Picture']['picture'.$i.'url'],$i,'');
					
					$this->process_image($this -> request -> data['Picture']['lpicture'.$i.'url'],$i,'large');	
					
					$this->process_image($this -> request -> data['Picture']['mpicture'.$i.'url'],$i,'medium');	
					
					$this->process_image($this -> request -> data['Picture']['spicture'.$i.'url'],$i,'small');
					
					$i++;
				}
				if($this -> Picture -> save($this -> request -> data)){
					if($this->Picture->id){
						if (isset($this->request->data['Picture']['size']) && count($this->request->data['Picture']['size'])) {
							$sizes='';
							foreach ($this->request->data['Picture']['size'] as $key => $value) {
								$qty=isset($this->request->data['Picture']['qty'][$key])?$this->request->data['Picture']['qty'][$key]:0;
								$sprice=isset($this->request->data['Picture']['sizeprice'][$key])?$this->request->data['Picture']['sizeprice'][$key]:0;
								$data['Sizeprice']['pictureid'] 	= $this->Picture->id;
								$data['Sizeprice']['sizeid'] 		= $value;
								$data['Sizeprice']['quantity'] 		= $qty;
								$data['Sizeprice']['sizeprice'] 	= $sprice;
								$this -> Sizeprice -> query("insert into sizeprices values ('','".$data['Sizeprice']['pictureid']."','".$data['Sizeprice']['sizeid']."','".$data['Sizeprice']['quantity']."','".$data['Sizeprice']['sizeprice']."')");
							}
						}
					}
					$this -> Picture -> query("update pictures set currency = '".$this -> request -> data['Picture']['currency']."' where userid = '".$userid."'");
					$this->addCount($userid,'photos');
					if(isset($this -> request -> data['Picture']['show_on_facebook']) && $this -> request -> data['Picture']['show_on_facebook']==1){
						@$this->addProductOnFacebook($userid,$this->Picture->id);	
						if(isset($this -> request -> data['Picture']['fbpage']) && $this -> request -> data['Picture']['fbpage'] !=''){
							$pageid = $this -> request -> data['Picture']['fbpage'];
							@$this->postProductOnPage($userid,$this->Picture->id,$pageid);
						}	
					}
					/*-------------Sending product information to the twitter and show there-----------------------------*/
					if(isset($this -> request -> data['Picture']['show_on_twitter']) && $this -> request -> data['Picture']['show_on_twitter']==1){
						App::import('Vendor','Codebird',array('file' =>'codebird.php'));	
						\Codebird\Codebird::setConsumerKey("lpmOffmzGPzH07j8WOMXuQ", "xlPTHAsbRG5OqxB7D3U4jRvEfbv9yGYNtmDj2uI390");
						$cb = \Codebird\Codebird::getInstance();
						$token = '635904789-mo07NQFsS6MaDp4sxhg0lRld3csfKdCURLK5jC9b';
						$secret= 'jIDqWAsau2jyizAouUJTw3aFlCEi4wCAv0ZI8c2qY';
						$cb->setToken($this -> Auth -> user('User.twitter_token'),$this -> Auth -> user('User.twitter_secrete'));
						$status =  $this -> request -> data['Picture']['title'];
						$status .= ' ';
						$status .= $this->base_url.'picture/'.$this->Picture->id.'/'.str_replace(' ', '-', $this -> request -> data['Picture']['title']);
						$status .= ' @shop_tt #buynow #shoptt';
						$params = array(
						  'status' => $status
						
						);
						$reply = $cb->statuses_update($params);
					}
					$customersData  = $this->Customer->find('all',array('fields'=>array('device_token'),'conditions'=>array('Customer.domainID'=>$userid,'Customer.isactive'=>1,'Customer.device_token !=' => '')));
					$message = $this -> Auth -> user('User.name').' has posted a new Product.Check it out.';
					$detailedid  = $this->Picture->id;
					$type = 'product';
					$category_name = '';
					if(!empty($customersData)){
						foreach($customersData as $key=>$value){
							$response = $this->sendNotification($value['Customer']['device_token'],$message,$type,$detailedid,$passphrase='admin',$category_name);
						}
					}
					$this -> Session -> setFlash(__('Record has been added successfully.'), '/message/success');
					die;
				}else{
					$this -> Session -> setFlash(__('Some Error In adding product.'), '/message/error');
					$this -> redirect(array('controller' => 'settings', 'action' => 'newPost'));
				}
			}else{
				$this -> Session -> setFlash(__('Some Error In Posting Photos.'), '/message/error');
				$this -> redirect(array('controller' => 'settings', 'action' => 'newPost'));
			}	
		}

		$mainCategory = $this -> MainCategory -> find('list', array('fields' => array('id', 'maincategory_name'), 'conditions' => array('userid'=>$userid,'status' => 1)));
		$fb_twiter = $this->User->find('first',array('fields'=>array('show_on_facebook','show_on_twitter'),'conditions'=>array('User.userid'=>$userid)));
		$category = array();
		$subcat = array();
		$sizes = $this -> Size -> find('list', array('fields' => array('sizeid', 'size'), 'conditions' => array('Size.userid'=>$userid),'order'=>array('sortorder'=>'ASC'), 'group' => array('Size.size')));
		$fbpages = $this->Fbpage->find('list',array('fields'=>array('id','name'),'conditions'=>array('Fbpage.userid'=>$userid)));
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		$shippingdata = $this -> Shipping -> find('first', array('conditions' => array('userid' => $userid)));
		$this->set('shipping',$shippingdata);
		$this -> set('show_facebook', $fb_twiter['User']['show_on_facebook']);
		$this -> set('show_twitter', $fb_twiter['User']['show_on_twitter']);
		$this->set(compact('sizes','fbpages','mainCategory','category','subcat','pageVar'));
	
	}
	/*-------------------------------------------- adding the bitly url Code -----------------------------------------------*/
	function bitly_url_shorten($long_url, $access_token, $domain)
	{
		$url = 'https://api-ssl.bitly.com/v3/shorten?access_token='.$access_token.'&longUrl='.urlencode($long_url).'&domain='.$domain;
		try {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 4);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$output = json_decode(curl_exec($ch));
		} catch (Exception $e) {
			
		}
		if(isset($output)){return $output->data->url;}
	}
	
	function facebookoff(){
		$this -> autoRender = false;	
		$this->User->query("update users set show_on_facebook = 0 where userid = '".$this -> Auth -> user('User.userid')."'");
		echo 1;die;
	}	
	function twitteroff(){
		$this -> autoRender = false;	
		$this->User->query("update users set show_on_twitter = 0 where userid = '".$this -> Auth -> user('User.userid')."'");
		echo 1;die;
	}
	/*-----------------------------------------Process product image to Amazon Server -----------------------------------------------*/
	function process_image($original_image,$n='',$size=''){
		App::uses('AmazonS3', 'AmazonS3.Lib');
		$AmazonS3 = new AmazonS3(array('AKIAJUN4MN7X6EOAMK4A', 'iQwPlU6b6XK3b1G8oBQ/RLxwdjOcYaMF5rbbSq3c', 'shoptt'));
			
		if (isset($original_image) && !empty($original_image)){
			if(!$size)
				$imagename = $_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/'.$original_image;
			else
				$imagename = $_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/'.$size.'/'.$original_image;
			
			if(!$size)
				$folder = 'original';
			else	
				$folder  = $size;
			if($AmazonS3->put($imagename, 'product/'.$folder)){
				die('///');
			}else{
				$this -> request -> data['Picture']['picture'.$n.'url']=$original_image;
			}	
		} else {
			unset($this -> request -> data['picture'.$n.'url']);
		}
		
		chmod($imagename, 755); 
		unlink($imagename);
		return $this -> request -> data;
	}
	/*----------------------------------------- get all the uploaded files from database -----------------------------------------------*/
	function getUploadedFiles($pictureid=''){
		$data = $this -> Picture -> find('first', array('fields'=>array('picture1url','picture2url','picture3url','picture4url','picorder'),'conditions' => array('pictureid' => $pictureid)));
		$files = array();
		$AmazonS3 = new AmazonS3(array('AKIAJUN4MN7X6EOAMK4A', 'iQwPlU6b6XK3b1G8oBQ/RLxwdjOcYaMF5rbbSq3c', 'shoptt'));
		if($data['Picture']['picorder']){
			$neworder = explode(',',$data['Picture']['picorder']);
			array_unshift($neworder, "");
			unset($neworder[0]);
		}else{
			$neworder = array(1,2,3,4);
			array_unshift($neworder, "");
			unset($neworder[0]);
		}
		$i=1;
		unset($data['Picture']['picorder']);
		foreach($data['Picture'] as $key=>$value){
			if(!empty($value)){
				$files[$neworder[$i]]['url']= $AmazonS3->publicUrl('product/small/'.$value);
				$files[$neworder[$i]]['thumbnailUrl'] =   '/files/thumbnail/'.$value;
				$files[$neworder[$i]]['name'] = $value;
				$files[$neworder[$i]]['size'] = $this->getimagedetails($AmazonS3->publicUrl('product/original/'.$value));
				$files[$neworder[$i]]['delete_type'] = 'DELETE';
				$files[$neworder[$i]]['delete_url'] = '/settings/deleteImage/'.$pictureid.'/'.$value.'/'.$key;
				$files[$neworder[$i]]['pictureurl'] = $key;
				$i++;
			}
		}
		
		$datasend['files']  = $files;
		echo json_encode($datasend);die;
	}
	function getimagedetails($img){
		$img = get_headers($img, 1);
		return $this->human_filesize($img["Content-Length"],2);
	}
	
	function human_filesize($bytes, $decimals = 2) {
		$size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
	

	/*-----------------------------------------Delete a image -----------------------------------------------*/
 	function deleteImage($pictureid='',$picture_name,$field){
		if(!empty($pictureid) && !empty($picture_name)){
			$this->Picture->query('Update pictures set '.$field.' = "",s'.$field.' = "",m'.$field.' = "",l'.$field.' = "" where pictureid= '.$pictureid.' and '.$field.'="'.$picture_name.'"');
		}
		echo 'success';die;
	} 
	/*-----------------------------------------Edit the product information -----------------------------------------------*/
	function editPost($pictureid='') {
		ini_set("upload_max_filesize","200M");
		$this -> layout = '/settings/setting_default';
		$pageVar['menu']='edit_post_product';
		$userid = $this -> Auth -> user('User.userid');
		
		if ($this -> request -> data) {
			if(isset($this -> request -> data['Picture']['filestatus']) && $this -> request -> data['Picture']['filestatus']=='files'){
				App::uses('UploadHandler', 'Vendor');
				$upload_handler = new UploadHandler(); 
				if($upload_handler->response){
					
					if (isset($this -> request -> data['Picture']['qty']) && FALSE) {
						for ($i = 0; $i < count($this -> request -> data['Picture']['qty']); $i++)
							$size[] = array('sizeid' => '', 'quantity' => '');

					}
					$this -> request -> data['Picture']['userid'] = $this -> Auth -> user('User.userid');
					
					
					if(isset($this -> request -> data['Picture']['already_exist']) && empty($this -> request -> data['Picture']['already_exist'])){
						$valuess  =  array(1,2,3,4);
					}else{
						$this -> request -> data['Picture']['already_exist'] = explode(',',$this -> request -> data['Picture']['already_exist']);
						for($j=1;$j<=4;$j++){
							if(!in_array($j,$this -> request -> data['Picture']['already_exist'])){
								$valuess[] = $j; 
							}
						}
					}
					
					foreach($upload_handler->response['files'] as $key=>$value){
						
							$img = array_reverse(explode('/',$value->url));
							$simg = array_reverse(explode('/',$value->mediumUrl));
							$mimg = array_reverse(explode('/',$value->smallUrl));
							$limg = array_reverse(explode('/',$value->largeUrl));
							
							$this -> request -> data['Picture']['picture'.$valuess[$key].'url'] = $img[0];
							
							$this -> request -> data['Picture']['spicture'.$valuess[$key].'url'] = $simg[0];
							
							$this -> request -> data['Picture']['mpicture'.$valuess[$key].'url'] = $mimg[0];
							
							$this -> request -> data['Picture']['lpicture'.$valuess[$key].'url'] = $limg[0];
							
							$this->process_image($this -> request -> data['Picture']['picture'.$valuess[$key].'url'],$valuess[$key],'');
							
							$this->process_image($this -> request -> data['Picture']['lpicture'.$valuess[$key].'url'],$valuess[$key],'large');	
							
							$this->process_image($this -> request -> data['Picture']['mpicture'.$valuess[$key].'url'],$valuess[$key],'medium');	
							
							$this->process_image($this -> request -> data['Picture']['spicture'.$valuess[$key].'url'],$valuess[$key],'small');
					
						
					}
					
					
					$this -> Picture -> id = $this -> request -> data['Picture']['pictureid'];
					$this -> Picture -> save($this -> request -> data);
					if($this->Picture->id){
						if (isset($this->request->data['Picture']['size']) && count($this->request->data['Picture']['size'])) {
							$sizes='';
							$this -> Sizeprice -> query("delete from sizeprices where pictureid = '".$this->Picture->id."'");
							foreach ($this->request->data['Picture']['size'] as $key => $value) {
								$qty=isset($this->request->data['Picture']['qty'][$key])?$this->request->data['Picture']['qty'][$key]:0;
								$sprice=isset($this->request->data['Picture']['sizeprice'][$key])?$this->request->data['Picture']['sizeprice'][$key]:0;
								$data['Sizeprice']['pictureid'] 	= $this->Picture->id;
								$data['Sizeprice']['sizeid'] 		= $value;
								$data['Sizeprice']['quantity'] 		= $qty;
								$data['Sizeprice']['sizeprice'] 	= $sprice;
								if(!empty($data['Sizeprice']['sizeid'])){
									$this -> Sizeprice -> query("insert into sizeprices values ('','".$data['Sizeprice']['pictureid']."','".$data['Sizeprice']['sizeid']."','".$data['Sizeprice']['quantity']."','".$data['Sizeprice']['sizeprice']."')");
								}
							}
						}
					}	
					$this -> Session -> setFlash(__('Record has been updated successfully.'), '/message/success');
				}
			}else{
				$this -> Picture -> id = $this -> request -> data['Picture']['pictureid'];
				if($this->Picture->id){
					if (isset($this->request->data['Picture']['size']) && count($this->request->data['Picture']['size'])) {
						$sizes='';
						$this -> Sizeprice -> query("delete from sizeprices where pictureid = '".$this->Picture->id."'");
						foreach ($this->request->data['Picture']['size'] as $key => $value) {
							$qty=isset($this->request->data['Picture']['qty'][$key])?$this->request->data['Picture']['qty'][$key]:0;
							$sprice=isset($this->request->data['Picture']['sizeprice'][$key])?$this->request->data['Picture']['sizeprice'][$key]:0;
							$data['Sizeprice']['pictureid'] 	= $this->Picture->id;
							$data['Sizeprice']['sizeid'] 		= $value;
							$data['Sizeprice']['quantity'] 		= $qty;
							$data['Sizeprice']['sizeprice'] 	= $sprice;
							if(!empty($data['Sizeprice']['sizeid'])){
								$this -> Sizeprice -> query("insert into sizeprices values ('','".$data['Sizeprice']['pictureid']."','".$data['Sizeprice']['sizeid']."','".$data['Sizeprice']['quantity']."','".$data['Sizeprice']['sizeprice']."')");
							}
						}
					}
				}
				
				$this -> Picture -> save($this -> request -> data);
				$this -> Picture -> query("update pictures set currency = '".$this -> request -> data['Picture']['currency']."' where userid = '".$userid."'");
				$this -> Session -> setFlash(__('Record has been updated successfully.'), '/message/success');
				$this -> redirect(array('controller' => 'settings', 'action' => 'products'));
				
			}
		}
		
		$data = $this -> Picture -> find('first', array('conditions' => array('pictureid' => $pictureid)));
		$mainCategory = $this -> MainCategory -> find('list', array('fields' => array('id', 'maincategory_name'), 'conditions' => array('userid'=>$userid,'status' => 1)));
		if(isset($data['Picture']['subcategoryid']) && !empty($data['Picture']['subcategoryid'])){
			$subcategoryid = $data['Picture']['subcategoryid'];
			if($subcategoryid){
				$category = $this -> Category -> find('list', array('fields' => array('categoryid', 'categoryname'), 'conditions' => array('status' => 1, "maincategory_id=(select maincategory_id from categories1 where categoryid={$subcategoryid})")));
				$subcat = $this -> Subcategory -> find('list', array('fields' => array('subcategoryid', 'subcategory'), 'conditions' => array("categoryid" => $subcategoryid)));
				$this -> set('category', $category);
					$this -> set('subcat', $subcat);
			}
			$mcategory = $this -> Category -> find('first', array('conditions' => array("categoryid" => $subcategoryid)));
			if($mcategory){
				$this -> request -> data['Picture']['maincategory'] = $mcategory['Category']['maincategory_id'];
			}
		}
		if(isset($data['Picture']['subsubcategoryid']) && !empty($data['Picture']['subsubcategoryid'])){
			$subsubcategoryid = $data['Picture']['subsubcategoryid'];
		}
		
		$sizes = $this -> Size -> find('list', array('fields' => array('sizeid', 'size'), 'conditions' => array('Size.userid'=>$userid),'order'=>array('sortorder'=>'ASC'), 'group' => array('Size.size')));
		

		$selsize = $this->Sizeprice->find('all',array('fields'=>array('id','sizeid','sizeprice','quantity'),'conditions'=>array('Sizeprice.pictureid'=>$pictureid)));
		$data['Picture']['sizes'] = $selsize;		
		
		$this -> request -> data = $data;
		$this -> set('sizes', $sizes);
		$this -> set('mainCategory', $mainCategory);
		$this -> set('pageVar',$pageVar); 

	}
	public function deleteSetSize($id){
		if ($this -> Sizeprice -> delete($id)) {
			echo 1;die;
		}else{
			echo 0;die;
		}
	}
	public function reorderimages($pictureid='',$order=''){
		$order = rtrim($order,',');
		$data['Picture']['pictureid']  = $pictureid;
		$data['Picture']['picorder']  = $order;
		$this->Picture->save($data);
		echo '{"status":"success"}';die; 
	}

	public function faq() {
		$userid = $this -> Auth -> user('User.userid');
		$pageVar['menu']='faq';
		$this -> layout = $this -> settingstheme . '/setting_default';
		if ($this -> request -> data) {
			foreach($this -> request -> data['FaqAnswer'] as $key=>$value){
				if (empty($value['id'])) {
					unset($value['id']);
					$this -> request -> data['FaqAnswer']['id']='';
				}else{
					$this -> request -> data['FaqAnswer']['id'] = $value['id'];
				}
				
				$this -> request -> data['FaqAnswer']['answer'] = $value['faq_answer_id'];
				$this -> request -> data['FaqAnswer']['faq_question_id'] = $value['faq_question_id'];
				$this -> request -> data['FaqAnswer']['userid'] = $userid;
				$this -> request -> data['FaqAnswer']['is_active'] = 1;
				$this -> FaqAnswer -> save($this -> request -> data['FaqAnswer']);
			}
			
			
			$this -> Session -> setFlash(__('Record has been updated successfully.'), '/message/success');
			$this -> redirect($this -> referer());
		}

		$data_answer = $this -> FaqAnswer -> find('all', array('conditions' => array('userid' => $userid)));
		$data_question = $this -> FaqQuestion -> find('list', array('conditions' => array('is_active' => 1), 'fields' => array('id', 'content')));
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		$this->set(compact('pageVar','data_answer','data_question'));

	}
	
	/*--------------------------------------------Listing all the products -------------------------------------------*/
	public function products($list = 0) {
		$pageVar['menu']='products';
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = $this -> settingstheme . '/setting_default';
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		$sort_orders = $this -> request -> data['User']['product_sort_order'];
		if(!$sort_orders){
			$sort_orders = 'Newest_Oldest';
		}
		$sort_orders = $this->sort_orders($sort_orders);
		$data = $this -> Picture -> find('all', array('conditions' => array('Picture.userid' => $userid),'order' => array($sort_orders[0]=>$sort_orders[1])));
		$this -> set('data', $data);
		$this->set('pageVar',$pageVar);
		if ($list == 1) {
			$this -> render('products_list');
		}
		
	}
	
	
	/*--------------------------------------Manage sort order of products -------------------------------------------*/
	public function sort_orders($sort_orders){
		if($sort_orders == 'Oldest_Newest'){
			$fields  =  'Picture.pictureid';
			$order = 'ASC';
		}else if($sort_orders == 'Newest_Oldest'){
			$fields  =  'Picture.pictureid';
			$order = 'DESC';
		}else if($sort_orders == 'Most_Expenisive'){
			$fields  =  'Picture.price';
			$order = 'DESC';
		}else if($sort_orders == 'Less_expensive'){
			$fields  =  'Picture.price';
			$order = 'ASC';
		}else if($sort_orders == 'Alphabetical_A_Z'){
			$fields  =  'Picture.title';
			$order = 'ASC';
		}else if($sort_orders == 'Alphabetical_Z_A'){
			$fields  =  'Picture.title';
			$order = 'DESC';
		} else if($sort_orders == 'Category'){
			$fields  =  'Picture.maincategory';
			$order = 'DESC';
		} else if($sort_orders == 'Category_A_Z'){
			$fields  =  'Picture.maincategory';
			$order = 'ASC';
		} else if($sort_orders == 'Category_Z_A'){
			$fields  =  'Picture.maincategory';
			$order = 'DESC';
		}
		$return = array();
		$return[] = $fields;
		$return[] = $order;
		return $return;
	}
	
	public function sort_products($sort_orders='',$list = 0){
		$data['User']['userid'] = $this -> Auth -> user('User.userid');
		$data['User']['product_sort_order'] = $sort_orders;
		$this->User->save($data);
		
		$sort_orders = $this->sort_orders($sort_orders);
		
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = '';
		$data = $this -> Picture -> find('all', array('conditions' => array('Picture.userid' => $userid),'order' => array($sort_orders[0]=>$sort_orders[1])));
		$this -> set('data', $data);
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		if($list ==1){
			$this->render('sort_products_list');
		}else{
			$this->render('sort_products');
		}
		
	}
	/*----------------------------------- change password code from account page -----------------------------------------*/
	public function changepassword() {
		if ($this -> request -> data) {
			 
			
			 if (strpos($this -> request -> data['User']['password'],' ') === false) {
		 
	    	unset($this -> request -> data['User']['confpassword']);

			$this -> User -> save($this -> request -> data);
			$this -> Session -> setFlash(__('Records have been updated successfully.'), '/message/success');
			}else{
			 
				$this -> Session -> setFlash(__('Password should not contain spaces.Please try again.'), '/message/success');
			}
				
		}
		$this -> redirect($this -> referer());

	}

	public function editbanner() {

		if ($this -> request -> data) {
			
			if (isset($this -> request -> data['User']['filebprofilepic']) && !empty($this -> request -> data['User']['filebprofilepic']['name'])) {
				$data = $this -> request -> data['User']['filebprofilepic'];
				$image_name = $this -> saveimage($data, 'banner_images');
				$datareturn = $this->process_banner_image($image_name);
				
				//$this -> request -> data['User']['bprofilepic'] = 'http://' . $_SERVER['SERVER_NAME'] . $this -> webroot . $image_name;
			}
			
			$this -> User -> save($this -> request -> data);
			$this -> Session -> setFlash(__('Record has been updated successfully.'), '/message/success');
		}
		$this -> redirect($this -> referer());

	}
	function ajaxValidateUserCategory(){
		$userid = $this -> Auth -> user('User.userid');
		$this -> autoRender = false;
		$catName = $_GET['fieldValue'];
		$MainCatID = $_GET['maincategory'];
		$validateId=$_REQUEST['fieldId'];
		$catID = $_GET['category'];
		if(empty($MainCatID)){
			$catExixst = $this->MainCategory->find('first',array('conditions'=>array('MainCategory.maincategory_name'=>$catName,'MainCategory.userid' => $userid)));
			if ($catExixst) {
				$arrayToJs = array();
				$arrayToJs[0] = $validateId;
				$arrayToJs[1] = false;
				echo json_encode($arrayToJs); // RETURN ARRAY WITH ERROR
			} else {
				$arrayToJs = array();
				$arrayToJs[0] = $validateId;
				$arrayToJs[1] = true;
				echo json_encode($arrayToJs); // RETURN ARRAY WITH success
			}
		}else if(!empty($MainCatID) && empty($catID)){
			$catExixst = $this -> Category -> find('first', array('fields' => array('categoryid', 'categoryname'), 'conditions' => array('maincategory_id' => $MainCatID,'categoryname'=>$catName,'Category.userid' => $userid)));
			if ($catExixst) {
				$arrayToJs = array();
				$arrayToJs[0] = $validateId;
				$arrayToJs[1] = false;
				echo json_encode($arrayToJs); // RETURN ARRAY WITH ERROR
			} else {
				$arrayToJs = array();
				$arrayToJs[0] = $validateId;
				$arrayToJs[1] = true;
				echo json_encode($arrayToJs); // RETURN ARRAY WITH success
			}
		}else if(!empty($MainCatID) && !empty($catID)){
			$catExixst = $this -> Subcategory -> find('first', array('conditions' => array('categoryid' => $catID,'subcategory'=>$catName)));
			if ($catExixst) {
				$arrayToJs = array();
				$arrayToJs[0] = $validateId;
				$arrayToJs[1] = false;
				echo json_encode($arrayToJs); // RETURN ARRAY WITH ERROR
			} else {
				$arrayToJs = array();
				$arrayToJs[0] = $validateId;
				$arrayToJs[1] = true;
				echo json_encode($arrayToJs); // RETURN ARRAY WITH success
			}
		}
	}
	
	function process_banner_image($original_image){
		App::uses('AmazonS3', 'AmazonS3.Lib');
		$AmazonS3 = new AmazonS3(array('AKIAJUN4MN7X6EOAMK4A', 'iQwPlU6b6XK3b1G8oBQ/RLxwdjOcYaMF5rbbSq3c', 'shoptt'));
			
		if (isset($original_image) && !empty($original_image)){
			$image =  explode('/',$original_image);
			$imagename = $_SERVER['DOCUMENT_ROOT'].'/app/webroot/banner_images/'.$image[1];
			
			if($AmazonS3->put($imagename, 'banner')){
				die('///');
			}else{
				$this -> request -> data['User']['bprofilepic']=$image[1];
			}	
		} else {
			unset($this -> request -> data['User']['bprofilepic']);
		}
		
		chmod($imagename, 755); 
		unlink($imagename);
		return $this -> request -> data;
	}	
	
	public function deletebanner() {

		$userid = $this -> Auth -> user('User.userid');

		$this -> request -> data['User']['userid'] = $userid;
		$this -> request -> data['User']['bprofilepic'] = NULL;
		$this -> User -> save($this -> request -> data);
		$this -> Session -> setFlash(__('Records have been updated successfully.'), '/message/success');

		$this -> redirect($this -> referer());

	}
	/*--------------------------------------------- Get category of a particular type -------------------------------------*/
	public function getcategory($type) {
		$this -> autoRender = false;
		$value = strtolower($type);
		$value_length = $this -> Category -> find('list', array('fields' => array('categoryid', 'categoryname'), 'conditions' => array('maincategory_id' => $value)));
		$data[] = "<option value=''>Select </option>";
		foreach ($value_length as $key => $q) {
			$data[] = "<option value='" . $key . "'>" . $q . "</option>";
		}

		echo implode('', $data);
		die ;
	}

	/*--------------------------------------------- Get category of a particular type -------------------------------------*/
	public function getsubcategory($type) {
		$this -> autoRender = false;
		$value = strtolower($type);
		$data = array();
		$value_length = $this -> Subcategory -> find('list', array('fields' => array('subcategoryid', 'subcategory'), 'conditions' => array('categoryid' => $value)));
		$data[] = "<option value=''>Select </option>";
		foreach ($value_length as $key => $q) {
			$data[] = "<option value='" . $key . "'>" . $q . "</option>";
		}

		echo implode('', $data);
		die ;
	}

	public function logout() {
		
		$this -> Auth -> logout();
		$this -> Session -> setFlash(__('You are successfully logged out'), '/message/success');
		$this->Session->delete('Message.flash');
		$this -> redirect('/');
	}


	public function deleteAccount($id='') {
		if(!empty($id)){
			$userid = $this -> Auth -> user('User.userid');
			if($userid==$id){
				
				$this->User->deleteAll(array('userid'=>$id));
				$this->Picture->delete(array('userid'=>$id));
				
				$this -> Session -> setFlash(__('You are successfully deleted you account'), '/message/success');
			}else{
				$this -> Session -> setFlash(__('You are successfully logged out'), '/message/success');
			}
			$this->logout();
		}
	}
	public function needPaypal() {
		$this -> Session -> setFlash(__('To post a new product you need to configure your paypal account from the screen below.'), '/message/success');
		$this->redirect(array('action'=>'account'));
	}
	public function needPaymentMethod() {
		$this -> Session -> setFlash(__('To post a new product you need to configure Connect your Payments.'), '/message/success');
		$this->redirect(array('action'=>'mywebsite'));
	}
	
	public function sold(){
		$pageVar['menu']='sold';
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = $this -> settingstheme . '/setting_default';
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		$data = $this -> Customerorder -> find('all', array('conditions' => array('Customerorder.userid' => $userid), 'order' => array('orderid' => 'desc')));
		$this -> set('data', $data);
		$this->set('pageVar',$pageVar);
	}
	public function copyEmails(){
		$userid = $this -> Auth -> user('User.userid');
		$customersData  = $this->Customer->find('all',array('conditions'=>array('Customer.domainID'=>$userid)));
		$guestEmailData  = $this->Guestemail->find('all',array('conditions'=>array('Guestemail.domainID'=>$userid)));
		foreach($customersData as $custkey=>$custvalue){
			$custemail[] = $custvalue['Customer']['email'];
		}
		if(isset($custemail) &&!empty($custemail)) {
			foreach($guestEmailData as $key=>$value){
				if(!in_array($value['Guestemail']['guestemail'],$custemail)){
					$data['Customer']['email']  = $value['Guestemail']['guestemail'];
					$data['Customer']['domainID']  = $value['Guestemail']['domainID'];
					$this->Customer->save($data);
				}
			}
		}
		
	}
	
	/*--------------------------------------------- listing all the customers here -------------------------------------*/
	public function customers(){
		$pageVar['menu']='Customer';
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = $this -> settingstheme . '/setting_default';
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		$customersData  = $this->Customer->find('all',array('conditions'=>array('Customer.domainID'=>$userid)));
		foreach($customersData as $custkey=>$custvalue){
			$custemail[] = $custvalue['Customer']['email'];
		}
		$CustomerorderData = $this -> Customerorder -> find('all', array('conditions' => array('Customerorder.userid' => $userid), 'order' => array('orderid' => 'desc')));
		foreach($CustomerorderData as $key=>$value){
			if(!in_array($value['Customer']['email'],$custemail)){
				$customersData[]['Customer'] = $value['Customer'];
			}
		}
		
		$countryList=$this->getCountryList();
		$this->set(compact('customersData','countryList'));
		$this->set('pageVar',$pageVar);
	}	
	/*--------------------------------------------- Ajax fields validation for already existed email of customer-------------------------------------*/
	public function ajaxValidateFieldEmailExists($data='') {
		$userid = $this -> Auth -> user('User.userid');	 	
		$arrayToJs = array();
		$arrayToJs[0] = $_GET['fieldId'];
		if(isset($_GET['fieldValue']) && !empty($_GET['fieldValue'])){
			$email=$_GET['fieldValue'];
			$data=$this->Customer->find('count',array('fields'=>array('Customer.domainID'),'conditions'=>array('Customer.email'=>$email,'Customer.domainID'=>$userid)));
			
			if($data >= 1){
				$arrayToJs[1] =false;
			}else{
				$arrayToJs[1] =true;
			}
			echo json_encode($arrayToJs);
			die;
		} 
		
	}
	/*--------------------------------------------- Ajax fields validation for already existed username of customer-------------------------------------*/
	public function ajaxValidateFieldCustomer($data='') {
		$this->autoLayout = false;
  		$this->autoRender = false; 
		$userid = $this -> Auth -> user('User.userid');	 		 
		$arrayToJs = array();
		$arrayToJs[0] = $_GET['fieldId'];
		if(isset($_GET['fieldValue']) && !empty($_GET['fieldValue'])){		
			$customername=$_GET['fieldValue'];
			$data=$this->Customer->find('count',array('fields'=>array('Customer.domainID'),'conditions'=>array('name'=>$customername,'Customer.domainID'=>$userid)));
			
			if($data >= 1){
				$arrayToJs[1] =false;
			}else{
				$arrayToJs[1] =true;
			}
			echo json_encode($arrayToJs);
			die;
		}	
			
		
	}
	public function getCustomerDetails(){
		$customersData  = $this->Customer->find('first',array('fields'=>array('Customer.customerid','Customer.name','Customer.fullname','Customer.email','Customer.address','Customer.city','Customer.state','Customer.country','Customer.zipcode','Customer.phone'),'conditions'=>array('Customer.customerid'=>$_POST['selected_id'])));
		echo json_encode($customersData);die;
	}
	function updateCustomerDetail(){
		$userid = $this -> Auth -> user('User.userid');
		$this->layout = $this->autoRender = false;
		if($this->request->data){
			if(isset($this->request->data['Customer']['customerid']) && !empty($this->request->data['Customer']['customerid'])){
				unset($this->request->data['submit']);
				$this->Customer->save($this->request->data);
				$this->Session->setFlash(__('Record have been updated successfully.'), '/message/success');
				$this -> redirect(array('controller' => 'settings', 'action' => 'customers'));
			}else{
				$domaindata = $this -> Domain -> find('first', array('conditions' => array('userid' => $userid)));
				$new_password=$this->getRandom(10);
				$this->request->data['Customer']['password']  = $new_password;
				$this->request->data['Customer']['domainID']  = $userid;
				if($this->Customer->save($this->request->data)){
					if($domaindata){
						$themeID = $domaindata['Domain']['themeID'];
						$logo = $this->Themebanner->find('first',array('fields'=>array('logo'),'conditions' => array('Themebanner.userid' => $userid,'Themebanner.themeid' => $themeID)));
						if(!$logo){
							$logo = '';
						}
					}else{
						$domaindata = '';
					}
					
					$Email = new CakeEmail();
					$Email->viewVars(array('email' => $this->request->data['Customer']['email'],'password'=>$new_password,'domaindata'=>$domaindata,'logo'=>$logo,'name'=>$this -> Auth -> user('User.name'),'businessname'=>$this -> Auth -> user('User.businessname')));
					$Email->template('createpassword', 'default')
						->emailFormat('html')
						->subject('Password to login into System')
						->to($this->request->data['Customer']['email'])
						->from($this -> Auth -> user('User.email'))
						->send();	
						$this->Session->setFlash(__('Customer have been registered successfully.We have sent a new password to your email Address.'), '/message/success');
						$this -> redirect(array('controller' => 'settings', 'action' => 'customers'));
				}
			}
		}
	}
	
	/*--------------------------------------------- dashboard page code showing all the past data-------------------------------------*/
	public function dashboard(){
		$pageVar['menu']='dashboard';
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = $this -> settingstheme . '/setting_default';
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		
		$data = $this -> Customerorder -> find('all', array('conditions' => array('Customerorder.userid' => $userid), 'order' => array('orderid' => 'desc')));
		$today_amount = $today_orders  = $total_amount = $yesterday_amount =	$yesterday_orders =	$last_seven_day_amount 	= $last_seven_day_orders = $last_30_days_amount	=
		$last_30_days_orders = $last_1_year_amount	= $last_1_year_orders 	= 0;
		$today_date 		= date('Y-m-d');
		$yesterday_date 	= date('Y-m-d',strtotime('-1 days'));
		$last_seven_days 	= date('Y-m-d',strtotime('-1 week'));
		$last_30_days 		= date('Y-m-d',strtotime('-1 month'));
		$last_1_year 		= date('Y-m-d',strtotime('-1 year'));
		$chartdata = array();
		if($data){
			foreach($data as $key=>$value){
				if(date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) == $today_date){
					$today_amount = $today_amount + $value['Customerorder']['total'] + $value['Customerorder']['shipping_amt'];
					$today_orders = $today_orders + 1;
				}
				if(date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) == $yesterday_date){
					$yesterday_amount = $yesterday_amount + $value['Customerorder']['total'] + $value['Customerorder']['shipping_amt'];
					$yesterday_orders = $yesterday_orders + 1;
				}
				if(date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) <= $today_date && date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) >= $last_seven_days){
					$last_seven_day_amount = $last_seven_day_amount + $value['Customerorder']['total'] + $value['Customerorder']['shipping_amt'];
					$last_seven_day_orders = $last_seven_day_orders + 1;
				}  
				if(date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) <= $today_date && date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) >= $last_30_days){
					$last_30_days_amount = $last_30_days_amount + $value['Customerorder']['total'] + $value['Customerorder']['shipping_amt'];
					$last_30_days_orders = $last_30_days_orders + 1;
				} 
				
				if(date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) <= $today_date && date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) >= $last_1_year){
					$last_1_year_amount = $last_1_year_amount + $value['Customerorder']['total'] + $value['Customerorder']['shipping_amt'];
					$last_1_year_orders = $last_1_year_orders + 1;
				} 
				$total_amount =   $total_amount+$value['Customerorder']['total'] + $value['Customerorder']['shipping_amt'];
				
				if (!in_array(date('Y-m-d',strtotime($value['Customerorder']['orderdate'])), $chartdata) && date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) <= $today_date && date('Y-m-d',strtotime($value['Customerorder']['orderdate'])) >= $last_30_days) {
					if(isset($chartdata[date('Y-m-d',strtotime($value['Customerorder']['orderdate']))]['amount'])){
						$chartdata[date('Y-m-d',strtotime($value['Customerorder']['orderdate']))]['amount'] = $chartdata[date('Y-m-d',strtotime($value['Customerorder']['orderdate']))]['amount']  + $value['Customerorder']['total'] + $value['Customerorder']['shipping_amt'];
					}else{
						$chartdata[date('Y-m-d',strtotime($value['Customerorder']['orderdate']))]['amount'] = $value['Customerorder']['total'] + $value['Customerorder']['shipping_amt'];
					}
				}
				if(isset($value['Customerorder']['currency']) && !empty($value['Customerorder']['currency'])){
					$currency = $value['Customerorder']['currency'];
					if($currency == 'USD' || $currency == 'AUD' || $currency == 'CAN' || $currency == 'SGD'){$sign = '$';}
					if($currency == 'EUR'){$sign = '€';}
					if($currency == 'GBP'){$sign = '£';}
				}else{
					$currency = 'USD';$sign = '$';
				}
			}
			$this->set(compact('today_amount','today_orders','yesterday_amount','yesterday_orders','last_seven_day_amount','last_seven_day_orders','last_30_days_amount','last_30_days_orders','last_1_year_amount','last_1_year_orders','total_amount','chartdata','last_30_days','currency','sign'));
		}else{
			$currency = 'USD';$sign = '$';
			$this->set(compact('today_amount','today_orders','yesterday_amount','yesterday_orders','last_seven_day_amount','last_seven_day_orders','last_30_days_amount','last_30_days_orders','last_1_year_amount','last_1_year_orders','total_amount','chartdata','last_30_days','currency','sign'));
		}
		$this -> set('data', $data);
		$this->set('pageVar',$pageVar);
		if($this -> Auth -> user('User.UA-ID')){
			$analytics = $this->getService();
			$this->set('analyticsdata',$analytics);
		}
	}
	
	/*---------------------------------------------  Sending notification the phone for adding new product-------------------------------------*/
	function sendNotification($deviceToken,$message,$type,$detailedid,$passphrase='admin',$category_name=''){
		
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/webapp/ShopPush.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		
		
		$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		
		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);
		
		echo 'Connected to APNS' . PHP_EOL;
		$body['aps'] = array(
			'alert' => $message,
			'data'	=> array("type"=>$type,"pagetoopen"=>$detailedid,"category_name"=>$category_name),
			'sound' => 'default',
			'badge' => 1
		); 
		
		
		// Encode the payload as JSON
		$payload = json_encode($body);
		
		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		
		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
		
		
		if (!$result)
			echo 'Message not delivered' . PHP_EOL;
		else
			echo 'Message successfully delivered' . PHP_EOL;
		
		
		// Close the connection to the server
		fclose($fp);
		return $result;
	}
	
	public function changestatus(){
		$this -> autoRender = false;
		$userid = $this -> Auth -> user('User.userid');
		$orderIDs = $_POST['orderids'];
		$orderIDs = explode(',',$orderIDs);
		foreach($orderIDs as $key=>$value){
			$ord = explode('-',$value);
			$data['Customerorder']['orderid'] = $ord[0];	
			if($ord[1] == 1){
				$orderVal = 0;
			}else{
				$orderVal = 1;
			}
			$data['Customerorder']['orderstatus'] = $orderVal;
			$this->Customerorder->save($data['Customerorder']);
		}
		echo 'success';die;
	}
	
	public function updatestatus(){
		$this -> autoRender = false;
		$userid = $this -> Auth -> user('User.userid');
		$orderID = $_POST['orderid'];
		$orderVal = $_POST['orderval'];
		$data['Customerorder']['orderid'] = $orderID;
		$data['Customerorder']['orderstatus'] = $orderVal;
		if($this->Customerorder->save($data['Customerorder'])){
			echo 'success';die;
		}else{
			echo 'fail';die;
		}
		
	}	
		
	public function soldData(){
		$pageVar['menu']='sold';
		$this -> autoRender = false;
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = '';
		if(!empty($_REQUEST['id'])){
			if($_REQUEST['id'] == 'market'){
				$data = $this -> Customerorder -> find('all', array('conditions' => array('Customerorder.userid' => $userid), 'order' => array('orderid' => 'desc')));
				$this -> set('data', $data);
				$this->render('sold_market');
			}
			if($_REQUEST['id'] == 'web'){
				$data = $this -> Order -> find('all', array('conditions' => array('Order.product_user_id' => $userid), 'order' => array('orderid' => 'desc')));
				$this -> set('data', $data);
				//pr($data);die;
				$this->render('sold_data');
			}
		}else{
			$data = $this -> Order -> find('all', array('conditions' => array('User.userid' => $userid), 'order' => array('orderid' => 'desc')));
			$this -> set('data', $data);
		}
		//pr($data);die;
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		
		$this->set('pageVar',$pageVar);
	}
	public function sold_detail($orderid){
		$pageVar['menu']='Sold Detail';
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = $this -> settingstheme . '/setting_default';
		$data = $this -> Order -> find('first', array('conditions' => array('Order.product_user_id' => $userid,'orderid'=>$orderid), 'order' => array('orderid' => 'desc')));
		if($data){
			$size = $this->Size->find('first',array('fields'=>array('Size.size'),'conditions'=>array('Size.sizeid'=>$data['Order']['sizeid'])));
			$data['Order']['size']  = $size['Size']['size'];
			
			$buyersdata  = $this->User->find('first',array('fields'=>array('User.fullname,User.email,User.country'),'conditions'=>array('User.userid'=>$data['Order']['userid'])));
			$data['Order']['name'] = $buyersdata['User']['fullname'];
			$data['Order']['email'] = $buyersdata['User']['email'];
			$data['Order']['country'] = $buyersdata['User']['country'];
			$this -> set('data', $data);
		}
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		
		$this->set('pageVar',$pageVar);

	}
	
	public function solddetail($orderid){
		$pageVar['menu']='Sold Detail';
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = $this -> settingstheme . '/setting_default';
		$data = $this -> Customerorder -> find('first', array('conditions' => array('Customerorder.userid' => $userid,'order_number'=>$orderid), 'order' => array('orderid' => 'desc')));
		
		if($data){
			$size = $this->Size->find('first',array('fields'=>array('Size.size'),'conditions'=>array('Size.sizeid'=>$data['Customerorder']['sizeid'])));
			if($size){
				$data['Customerorder']['size']  = $size['Size']['size'];
			}else{
				$data['Customerorder']['size']  = '';
			}
		
			$buyersdata  = $this->Customer->find('first',array('fields'=>array('Customer.fullname,Customer.email,Customer.country'),'conditions'=>array('Customer.customerid'=>$data['Customerorder']['customer_id'])));
			$data['Customerorder']['name'] = $buyersdata['Customer']['fullname'];
			$data['Customerorder']['email'] = $buyersdata['Customer']['email'];
			$data['Customerorder']['country'] = $buyersdata['Customer']['country'];
			$this -> set('data', $data);
		}
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		
		$this->set('pageVar',$pageVar);

	}
	public function social(){
		$pageVar['menu']='social';
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = $this -> settingstheme . '/setting_default';
		
		if($this->request->data){
			$this -> User -> id = $userid;
			//$this->request->data['User']['userid']=$userid;
			$this->User->save($this->request->data);
				$this -> Session -> setFlash(__('Records have been updated successfully.'), '/message/success');
		}
		$this->request->data=$this->User->find('first',array('conditions'=>array('User.userid'=>$userid)));
				$this->set('pageVar',$pageVar);

	}
	
	function array_msort($array, $cols)
	{
		$colarr = array();
		foreach ($cols as $col => $order) {
			$colarr[$col] = array();
			foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
		}
		$eval = 'array_multisort(';
		foreach ($cols as $col => $order) {
			$eval .= '$colarr[\''.$col.'\'],'.$order.',';
		}
		$eval = substr($eval,0,-1).');';
		eval($eval);
		$ret = array();
		foreach ($colarr as $col => $arr) {
			foreach ($arr as $k => $v) {
				$k = substr($k,1);
				if (!isset($ret[$k])) $ret[$k] = $array[$k];
				$ret[$k][$col] = $array[$k][$col];
			}
		}
		return $ret;

	}
/*-------------------------webethics 9Apr2015-----------*/
	public function pricingpage() {
		$userid = $this -> Auth -> user('User.userid');
		if (empty($userid)) {
			$this -> redirect($this -> referer());
		}
		$data = $this -> Page -> find('first', array('conditions' => array('id' => 1)));
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		$paymentdata = $this -> Card -> find('first', array('conditions' => array('id' => $userid)));
		
		$this->set('paymentdata',$paymentdata);
		//$userData = $this -> User -> find('first', array('conditions' => array('User.name' => $username)));		
		$pageVar['menu'] = 'pricing';	
		$this -> layout = '/settings/setting_default';
				
		$this -> set('pageVar', $pageVar);
	}
	
	
	public function mywebsite(){
		$userid = $this -> Auth -> user('User.userid');
		if (empty($userid)) {
			$this -> redirect($this -> referer());
		}
		$data = $this -> Page -> find('first', array('conditions' => array('id' => 1)));
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		
		$domaindata = $this -> Domain -> find('first', array('conditions' => array('userid' => $userid)));
		$this->set('domain',$domaindata);
		
		$shippingdata = $this -> Shipping -> find('first', array('conditions' => array('userid' => $userid)));
		$this->set('shipping',$shippingdata);
		
		$paymentdata = $this -> Card -> find('first', array('conditions' => array('id' => $userid)));
		$this->set('paymentdata',$paymentdata);
		
		$Connecteddata = $this -> Account -> find('first', array('conditions' => array('id' => $userid)));
		$this->set('Connecteddata',$Connecteddata);
		
		$paypal_data =  $this -> User -> find('first', array('fields'=>array('paypal_email'),'conditions' => array('userid' => $userid)));
		$this->set('paypal_data',$paypal_data);
		
		$categories_data =  $this -> MainCategory -> find('first', array('fields'=>array('maincategory_name'),'conditions' => array('userid' => $userid)));
		$this->set('categories_data',$categories_data);
		
		$emailData = $this -> Customeemail -> find('first', array('conditions' => array('userid' => $userid)));	
		$this->set('emailData',$emailData);
		
		//$userData = $this -> User -> find('first', array('conditions' => array('User.name' => $username)));		
		$pageVar['menu'] = 'mywebsite';	
		$this -> layout = '/settings/setting_default';

		$mainCategory = $this -> MainCategory -> find('list', array('fields' => array('maincategory_name'), 'conditions' => array('status' => 1,'userid'=>$userid)));
		$category = array();
		$subcat = array();
		$this -> set('mainCategory', $mainCategory);
		$this -> set('category', $category);
		$this -> set('subcat', $subcat);
				
		$this -> set('pageVar', $pageVar);
	}
	
	public function selecttheme(){
		$userid = $this -> Auth -> user('User.userid');
		if (empty($userid)) {
			$this -> redirect($this -> referer());
		}
		$data = $this -> Page -> find('first', array('conditions' => array('id' => 1)));
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		//$userData = $this -> User -> find('first', array('conditions' => array('User.name' => $username)));		
		$pageVar['menu'] = 'edit theme';
		$this -> layout = '/settings/setting_default';
		
		$domaindata = $this -> Domain -> find('first', array('conditions' => array('userid' => $userid)));
		$this->set('domaindata',$domaindata);
		
		$product_data	=	$this->Picture->find('count',array('fields'=>array('Picture.pictureid'),'conditions'=>array('Picture.userid'=>$userid)));
		$this->set('product_data',$product_data);
		$this -> set('pageVar', $pageVar);
	} 
	public function getallcategories(){
		$this -> autoRender = false;
		$userid = $this -> Auth -> user('User.userid');
		if(isset($this -> request -> data['submit']) && !empty($this -> request -> data['submit']) && !empty($this -> request ->data['maincategory'])){
			$maincat = $this -> request ->data['maincategory'];
			$maincatid = $this -> MainCategory -> find('first', array('fields' => array('id'), 'conditions' => array('maincategory_name'=>ucfirst($maincat),'userid'=>$userid,'status' => 1)));
			if($maincatid){
				$allcategories = $this -> Category -> find('list', array('fields' => array('categoryname'), 'conditions' => array('status' => 1,'userid'=>$userid,'maincategory_id'=>$maincatid['MainCategory']['id'])));
				$cat = array();
				foreach ($allcategories as $key => $value) {
					$cat[]=strtolower($value);
				}
				echo json_encode($cat);die;
				 //echo '['.implode(',',$cat).']';die;
			}else{
				$category = $this -> Category -> find('list', array('fields' => array('categoryname'), 'conditions' => array('status' => 1,'userid'=>$userid)));
				foreach ($category as $key => $value) {
					$cat[]=strtolower($value);
				}
				echo json_encode($cat);die;
			}
		
		}
	}

	public function getallsubcategories(){
		$this -> autoRender = false;
		$userid = $this -> Auth -> user('User.userid');
		if(isset($this -> request -> data['submit']) && !empty($this -> request -> data['submit']) && !empty($this -> request ->data['wire'])){
			$maincat = $this -> request ->data['maincategory'];
			$maincatid = $this -> MainCategory -> find('first', array('fields' => array('id'), 'conditions' => array('maincategory_name'=>ucfirst($maincat),'userid'=>$userid,'status' => 1)));
			
			$cat = $this -> request ->data['wire'];
			$catid = $this -> Category -> find('first', array('fields' => array('categoryid'), 'conditions' => array('categoryname'=>ucfirst($cat),'maincategory_id'=>$maincatid['MainCategory']['id'],'userid'=>$userid,'status' => 1)));
			if($catid){
				$allsubcategories = $this -> Subcategory -> find('list', array('fields' => array('subcategory'), 'conditions' => array('status' => 1,'categoryid'=>$catid['Category']['categoryid'])));
				$cat = array();
				foreach ($allsubcategories as $key => $value) {
					$cat[]=strtolower($value);
				}
				echo json_encode($cat);die;
				 //echo '['.implode(',',$cat).']';die;
			}else{
				$allsubcategories = $this -> Subcategory -> find('list', array('fields' => array('subcategory'), 'conditions' => array('status' => 1,'userid'=>$userid)));
				foreach ($allsubcategories as $key => $value) {
					$cat[]=strtolower($value);
				}
				echo json_encode($cat);die;
			}
		
		}
	}	
	public function editcustomcategory($main_cat=''){
		$userid = $this -> Auth -> user('User.userid');
		if (empty($userid)) {
			$this -> redirect($this -> referer());
		}

		$this -> layout = '/settings/setting_default';
		$pageVar['menu'] = 'Edit Customise Category';
		$this -> set('pageVar', $pageVar);
		$this->set('main_cat',$main_cat);
		if(isset($this -> request -> data['submit']) && !empty($this -> request -> data['submit'])){
			$main_catid  = $this->request->data['main_catid'];
			$data  = $this -> request -> data['Settings'];
			if(isset($data['maincategory']) && !empty($data['maincategory'])){
				$maincat = $data['maincategory'];
				$maincatid = $this -> MainCategory -> find('first', array('fields' => array('id'), 'conditions' => array('maincategory_name'=>ucfirst($maincat),'status' => 1,'userid'=>$userid)));
				if(!$maincatid){
					$maxmainCategory = $this -> MainCategory -> find('first', array('fields' => array('MAX(sortorder) as max_order'), 'conditions' => array('status' => 1,'userid'=>$userid)));
					$maincatdata['Maincategory']['id']  = $main_catid;
					$maincatdata['Maincategory']['maincategory_name'] = ucfirst($data['maincategory']);
					$maincatdata['Maincategory']['userid']  = $userid;
					$maincatdata['Maincategory']['status']  = 1;
					$maincatdata['Maincategory']['sortorder']  = $maxmainCategory[0]['max_order']+1;
					
					$this->MainCategory->save($maincatdata['Maincategory']);
				}
			}
			if(isset($data['categories']) && !empty($data['categories'])){
				foreach($data['categories'] as $key=>$value){
					if(isset($value['category']) && !empty($value['category'])){
						foreach($value['category'] as $key2=>$value2){
							if($key2 != 'XX'){
								$cat = $value2;
								$this -> Category -> query("update categories1 set categoryname = '".ucfirst($cat)."' where userid = '".$userid."' and categoryid= '".$key2."'");
							}else{

								$maincat = $data['maincategory'];
								$maincatid = $this -> MainCategory -> find('first', array('fields' => array('id'), 'conditions' => array('maincategory_name'=>ucfirst($maincat),'userid'=>$userid,'status' => 1)));
						
								$cat = $value2;
								$catid = $this -> Category -> find('first', array('fields' => array('categoryid'), 'conditions' => array('categoryname'=>ucfirst($cat),'status' => 1,'userid'=>$userid,'maincategory_id'=>$maincatid['MainCategory']['id'])));
								if(!$catid){
									$maxCategory = $this -> Category -> find('first', array('fields' => array('MAX(sortorder) as max_order'), 'conditions' => array('status' => 1,'userid'=>$userid)));
									$catdatastatus  = 1;
									$catdatasortorder  = $maxCategory[0]['max_order']+1;
									$this -> Category -> query("Insert into categories1(maincategory_id,categoryname,userid,status,sortorder) values('".$maincatid['MainCategory']['id']."','".ucfirst($cat)."','".$userid."','".$catdatastatus."','".$catdatasortorder."')");
								}
							}
							if(isset($value['subcategory']) && !empty($value['subcategory'])){
								foreach($value['subcategory'] as $k=>$v){
									if($k != "XX"){
										$this -> Subcategory -> query("update subcategories set subcategory = '".ucfirst($v)."' where userid = '".$userid."' and subcategoryid = '".$k."'");
									}else{
										
										$cat = $value2;
										$catid = $this -> Category -> find('first', array('fields' => array('categoryid'), 'conditions' => array('categoryname'=>ucfirst($cat),'userid'=>$userid,'status' => 1)));
											
										$subcat = $v;
										$subcatid = $this -> Subcategory -> find('first', array('fields' => array('subcategoryid'), 'conditions' => array('subcategory'=>ucfirst($subcat),'userid'=>$userid,'status' => 1,'categoryid'=>$catid['Category']['categoryid'])));
										if(!$subcatid){
											
											$maxSubCategory = $this -> Subcategory -> find('first', array('fields' => array('MAX(sortorder) as max_order'), 'conditions' => array('status' => 1,'userid'=>$userid)));
											
											$status  = 1;
											$sortorder  = $maxSubCategory[0]['max_order']+1;
											$this -> Subcategory -> query("Insert into subcategories(categoryid,subcategory,userid,status,sortorder) values('".$catid['Category']['categoryid']."','".ucfirst($subcat)."','".$userid."','".$status."','".$sortorder."')");
										}
									}
								}
							}
						}
					}
					
				}	
			}
			
			$this -> Session -> setFlash(__('Category has been added successfully.'), '/message/success');
			$this -> redirect(array('controller' => 'settings', 'action' => 'editcustomcategory',$main_catid));
		}
		
		$catdata=$this->MainCategory->find('all',array('conditions'=>array('id'=>$main_cat,'status'=>'1','userid'=>$userid),'order'=>array('sortorder'=>'ASC')));
		if($catdata){
			foreach ($catdata as $mainkey => $mainvalue) {
				
				$parent_id=$mainvalue['MainCategory']['id'];
				$subcatids=$this->getCategoryids($parent_id);
				$subsubcats=array();
				$subcats=array();
				$subcatData=array();
				if($subcatids){
					foreach ($subcatids as $key => $value) {
						$subcatData[$key]=$value;
						// $subcats[]=$value['Category']['categoryid'];
						$subsubcatids=array();
						$subsubcatids=$this->getsubssubCategoryids($value['Category']['categoryid']);
						if(count($subsubcatids) && $subsubcatids !==NULL){
							foreach ($subsubcatids as $keybox => $valuebox) {
								$subcatData[$key]['subcategory'][$keybox]=$valuebox;
								//$subsubcats[]=$valuebox['Subcategory']['subcategoryid'];
							}
						}
					}
				}
				$catdata[$mainkey]['MainCategory']['category']=$subcatData;	
			}
		}
		$this->set('catData',$catdata);
	}
	
	public function addcustomcategory(){
		$userid = $this -> Auth -> user('User.userid');
		if (empty($userid)) {
			$this -> redirect($this -> referer());
		}

		$this -> layout = '/settings/setting_default';
		$pageVar['menu'] = 'Customise Category';
		$this -> set('pageVar', $pageVar);
		if(isset($this -> request -> data['submit']) && !empty($this -> request -> data['submit'])){
		
			$data  = $this -> request -> data['Settings'];
			
			if(isset($data['maincategory']) && !empty($data['maincategory'])){
				$maincat = $data['maincategory'];
				$maincatid = $this -> MainCategory -> find('first', array('fields' => array('id'), 'conditions' => array('maincategory_name'=>ucfirst($maincat),'status' => 1,'userid'=>$userid)));
				if(!$maincatid){
					$maxmainCategory = $this -> MainCategory -> find('first', array('fields' => array('MAX(sortorder) as max_order'), 'conditions' => array('status' => 1,'userid'=>$userid)));
					$maincatdata['Maincategory']['maincategory_name'] = ucfirst($data['maincategory']);
					$maincatdata['Maincategory']['userid']  = $userid;
					$maincatdata['Maincategory']['status']  = 1;
					$maincatdata['Maincategory']['sortorder']  = $maxmainCategory[0]['max_order']+1;
					
					$this->MainCategory->save($maincatdata['Maincategory']);
				}
			}
			if(isset($data['categories']) && !empty($data['categories'])){
				foreach($data['categories'] as $key=>$value){
					if(isset($value['category']) && !empty($value['category'])){
						
						$maincat = $data['maincategory'];
						$maincatid = $this -> MainCategory -> find('first', array('fields' => array('id'), 'conditions' => array('maincategory_name'=>ucfirst($maincat),'userid'=>$userid,'status' => 1)));
							
						$cat = $value['category'];
						$catid = $this -> Category -> find('first', array('fields' => array('categoryid'), 'conditions' => array('categoryname'=>ucfirst($cat),'status' => 1,'userid'=>$userid,'maincategory_id'=>$maincatid['MainCategory']['id'])));
						if(!$catid){
							
							$maxCategory = $this -> Category -> find('first', array('fields' => array('MAX(sortorder) as max_order'), 'conditions' => array('status' => 1,'userid'=>$userid)));
							
							$catdatastatus  = 1;
							$catdatasortorder  = $maxCategory[0]['max_order']+1;
							$this -> Category -> query("Insert into categories1(maincategory_id,categoryname,userid,status,sortorder) values('".$maincatid['MainCategory']['id']."','".ucfirst($cat)."','".$userid."','".$catdatastatus."','".$catdatasortorder."')");
						}
					}
					if(isset($value['subcategory']) && !empty($value['subcategory'])){
						foreach($value['subcategory'] as $k=>$v){
							
							$cat = $value['category'];
							$catid = $this -> Category -> find('first', array('fields' => array('categoryid'), 'conditions' => array('categoryname'=>ucfirst($cat),'userid'=>$userid,'status' => 1)));
								
							$subcat = $v;
							$subcatid = $this -> Subcategory -> find('first', array('fields' => array('subcategoryid'), 'conditions' => array('subcategory'=>ucfirst($subcat),'userid'=>$userid,'status' => 1,'categoryid'=>$catid['Category']['categoryid'])));
							if(!$subcatid){
								
								$maxSubCategory = $this -> Subcategory -> find('first', array('fields' => array('MAX(sortorder) as max_order'), 'conditions' => array('status' => 1,'userid'=>$userid)));
								
								$status  = 1;
								$sortorder  = $maxSubCategory[0]['max_order']+1;
								$this -> Subcategory -> query("Insert into subcategories(categoryid,subcategory,userid,status,sortorder) values('".$catid['Category']['categoryid']."','".ucfirst($subcat)."','".$userid."','".$status."','".$sortorder."')");
							}	
						}
					}
				}	
			}
			$this -> Session -> setFlash(__('Category has been added successfully.'), '/message/success');
			$this->redirect(array('action'=>'addcustomcategory'));
		} 	
		
		
		$mainCategory = $this -> MainCategory -> find('list', array('fields' => array('maincategory_name'), 'conditions' => array('status' => 1,'userid'=>$userid)));
		
		$category = $this -> Category -> find('list', array('fields' => array('categoryname'), 'conditions' => array('status' => 1)));
		$usercats = $this->getAllCategory();
		$mainCat = array();
		$cat = array();
		$subcat = array();
		//pr($usercats);
		if ($usercats) {
			foreach($usercats as $key1=>$value1){
				$mainCat[] = $value1['MainCategory']['id'];
				foreach($value1['MainCategory']['category'] as $key2=>$value2){
					$cat[] = $value2['Category']['categoryid'];
					if(isset($value2['subcategory'])&& !empty($value2['subcategory'])){
						foreach($value2['subcategory'] as $key3=>$value3){
							$subcat[] = $value3['Subcategory']['subcategoryid'];
						}
					}	
				}
			}
		}	
		
		$userCat = array('maincategory' => $mainCat, 'category' => $cat, 'subcategory' => $subcat);
		$this->set('userCat',$userCat);
		$sizes = $this->Size->find('all',array('conditions'=>array('Size.userid'=>$userid),'order'=>array('sortorder'=>'ASC')));
		$this->set('sizes',$sizes);
		$subcat = $this -> Subcategory -> find('list', array('fields' => array('subcategory'), 'conditions' => array('status' => 1)));
		$this -> set('mainCategory', $mainCategory);
		$this -> set('category', $category);
		$this -> set('subcat', $subcat);
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
	}
	
	public function deletesize($sizeid){
		$this->Size->query("delete from sizes where sizeid = '".$sizeid."'");
		$this -> Session -> setFlash(__('Size has been deleted.'), '/message/success');
		$this->redirect(array('action'=>'addcustomcategory'));
	}
	
	public function deletecategory($maincatid){
		$this->MainCategory->query("delete from maincategory where id = '".$maincatid."'");
		$catids = $this->Category->find('all',array('conditions'=>array('Category.maincategory_id'=>$maincatid)));
		
		if($catids){
			Foreach($catids as $key=>$value){
				$this->Subcategory->query("delete from subcategories where categoryid='".$value['Category']['categoryid']."'");
				$this->Category->query("delete from categories1 where maincategory_id = '".$maincatid."'");
			}
		}
		$this -> Session -> setFlash(__('Categories has been deleted.'), '/message/success');
		$this->redirect(array('action'=>'addcustomcategory'));
	}	
	
	public function addsize(){
		$userid = $this -> Auth -> user('User.userid');
		if($this->request->data){
			
			$sizes = $this->request->data['Settings']['size'];
			if(isset($sizes) && !empty($sizes)){
				$sizearray = explode(',',$sizes);
				foreach($sizearray as $key=>$value){
					$data['Size']['userid'] = $userid;
					$data['Size']['size'] = $value;
					$size = $this->Size->query('select sizeid from sizes where size = "'.$data['Size']['size'].'" and  userid  = "'.$data['Size']['userid'].'"');
					if(!$size){
												
						$sizesortorder = $this -> Size -> find('first', array('fields' => array('MAX(sortorder) as max_order'), 'conditions' => array('userid'=>$userid)));
						
						$this->Size->query("insert into sizes(userid,size,sortorder) values('".$data['Size']['userid']."','".$data['Size']['size']."','".$sizesortorder[0]['max_order']."')");
					}
				}
				
				$this -> Session -> setFlash(__('Size has been added successfully.'), '/message/success');
				$this->redirect(array('action'=>'addcustomcategory'));
			}
			$this -> Session -> setFlash(__('Size has not been enter.Add comma separated values.'), '/message/error');
			$this->redirect(array('action'=>'addcustomcategory'));
		}
	}	
	
	public function sizes(){
		$userid = $this -> Auth -> user('User.userid');
		$sizes = $this->Size->find('all',array('conditions'=>array('Size.userid'=>$userid)));
		echo json_encode($sizes);die;
	}
	
	public function remove_subcat(){
		$subcat_id = $this->request->data['subcatid'];
		if($subcat_id){
			if($this->Subcategory->delete($subcat_id)){
				$this->Picture->deleteAll(array('Picture.subcatid'=>$subcat_id));
			}
		}
	}
	public function remove_cat(){
		$catid = $this->request->data['catid'];
		if($catid){
			if($this->Category->delete($catid)){
				$this->Subcategory->deleteAll(array('Subcategory.categoryid'=>$catid));
				$this->Picture->deleteAll(array('Picture.subcategoryid'=>$catid));
			}
		}
	}
	
	public function order_update_size(){
		$reroder_data = $this->request->data['ids'];
		$reroder_data = json_decode($reroder_data);
		$i=1;
		foreach($reroder_data as $key=>$value){
			$this->Size->query("update sizes set sortorder =  '".$i."' where sizeid = '".$value->id."'");
			$i++;
		}die;	
	}
		
	public function order_update(){
		$reroder_data = $this->request->data['ids'];
		$reroder_data = json_decode($reroder_data);
		$i=1;
		
		foreach($reroder_data as $key=>$value){
			$this -> MainCategory -> query("update maincategory set sortorder =  '".$i."' where id = '".$value->id."'");
			$i++;
			if(isset($value->children) && !empty($value->children)){
				$j=1;
				foreach($value->children as $key1=>$value1){
					$this -> Category -> query("update categories1 set sortorder =  '".$j."' where categoryid = '".$value1->id."'");
					$j++;
					if(isset($value1->children) && !empty($value1->children)){
						$k=1;
						foreach($value1->children as $key2=>$value2){
							$this -> Subcategory -> query("update subcategories set sortorder =  '".$k."' where subcategoryid = '".$value2->id."'");
							$k++;
						}
					}
				}
			}
		
		}
		die;
	}
	
	public function getAllCategory()
	{ 
		$userid = $this -> Auth -> user('User.userid');
		$catdata=$this->MainCategory->find('all',array('conditions'=>array('status'=>'1','userid'=>$userid),'order'=>array('sortorder'=>'ASC')));
		if($catdata){
			foreach ($catdata as $mainkey => $mainvalue) {
				$parent_id=$mainvalue['MainCategory']['id'];
				$subcatids=$this->getCategoryids($parent_id);
				$subsubcats=array();
				$subcats=array();
				$subcatData=array();
				if($subcatids){
					foreach ($subcatids as $key => $value) {
						$subcatData[$key]=$value;
						$subsubcatids=array();
						$subsubcatids=$this->getsubssubCategoryids($value['Category']['categoryid']);
						if(count($subsubcatids) && $subsubcatids !==NULL){
							foreach ($subsubcatids as $keybox => $valuebox) {
								$subcatData[$key]['subcategory'][$keybox]=$valuebox;
							}
						}
					}
				}
				$catdata[$mainkey]['MainCategory']['category']=$subcatData;	
			}
		}
		return $catdata;
	}
	
	public function needProduct() {
		$this -> Session -> setFlash(__('To select and edit the theme you need to post products.'), '/message/error');
		$this->redirect(array('action'=>'selecttheme'));
	}
	
	public function connectpayment(){
		$userid = $this -> Auth -> user('User.userid');
		if (empty($userid)) {
			$this -> redirect($this -> referer());
		}
		$data = $this -> Page -> find('first', array('conditions' => array('id' => 1)));
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		$pageVar['name'] = 'connect payment';
		$this -> layout = '/settings/setting_default';
				
		$this -> set('pageVar', $pageVar);
	} 	
	
	public function connectdomain(){
		$userid = $this -> Auth -> user('User.userid');
		if (empty($userid)) {
			$this -> redirect($this -> referer());
		}
		$data = $this -> Page -> find('first', array('conditions' => array('id' => 1)));
		$domaindata = $this -> Domain -> find('first', array('conditions' => array('userid' => $userid)));
		if($domaindata){ $id = $domaindata['Domain']['id'];}else{$id = '';}
		if(isset($this -> request -> data['Settings']['domaincheck']) && $this -> request -> data['Settings']['domaincheck'] !=""){ $check = $this -> request -> data['Settings']['domaincheck'];}else{$check = '0';}
		if($this -> request -> data){
			$data['Domain']['userid']		= $userid; 
			$data['Domain']['id']			= $id;
			$data['Domain']['storedomain']	= $this -> request -> data['Settings']['storedomain'].'.shoptt.co';  
			$data['Domain']['customdomain']	= $this -> request -> data['Settings']['customdomain'];  
			$data['Domain']['analytics']	= ($this -> request -> data['Settings']['analytics'])?$this -> request -> data['Settings']['analytics']:"";  
			
			$data['Domain']['domaincheck']	= $check;  
			
			$this->Domain->save($data);
			$this -> redirect(array('controller' => 'settings', 'action' => 'mywebsite'));
			$this -> Session -> setFlash(__('Record has been updated successfully.'), '/message/success');
		}
		
		
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		
		$pageVar['name'] = 'connect domain';
		$this -> layout = '/settings/setting_default';
		$this -> set('pageVar', $pageVar);
		$this -> redirect(array('controller' => 'settings', 'action' => 'mywebsite'));
	} 	
	
	public function set_shipping_cost(){
		$userid = $this -> Auth -> user('User.userid');
		if (empty($userid)) {
			$this -> redirect($this -> referer());
		}
		$data = $this -> Page -> find('first', array('conditions' => array('id' => 1)));
		if($this -> request -> data){
			$data['Shipping']['userid']					= $userid; 
			$data['Shipping']['national_single']		= $this -> request -> data['Settings']['national_single'];  
			$data['Shipping']['national_multiple']		= $this -> request -> data['Settings']['national_multiple'];  
			$data['Shipping']['international_single']	= $this -> request -> data['Settings']['international_single'];  
			$data['Shipping']['international_multiple']	= $this -> request -> data['Settings']['international_multiple'];  
			$data['Shipping']['national_delivery_note']	= $this -> request -> data['Settings']['national_delivery_note'];  
			$data['Shipping']['international_delivery_note']	= $this -> request -> data['Settings']['international_delivery_note'];  
			
			$this->Shipping->save($data);
			$this -> redirect(array('controller' => 'settings', 'action' => 'mywebsite'));
			$this -> Session -> setFlash(__('Record has been updated successfully.'), '/message/success');
		}
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		//$userData = $this -> User -> find('first', array('conditions' => array('User.name' => $username)));		
		$pageVar['name'] = 'set shipping cost';
		$this -> layout = '/settings/setting_default';
				
		$this -> set('pageVar', $pageVar);
	} 
	public function analyticgraph(){
		$pageVar['menu']='Analytic';
		$userid = $this -> Auth -> user('User.userid');
		$this -> set('pageVar', $pageVar);
		$analytics = $this->getService();
		$this->set('data',$analytics);
		$this->render('analyticgraph');
	}
	function oauth2callback(){
		App::import('Vendor','google-api-php-client/src/Google/autoload');
		$client = new Google_Client();
		$client->setAuthConfigFile('/var/www/html/webapp/client_secret_424480929196-rsnrimtnqgs6nqaqa0h2at2tcqs7qm68.apps.googleusercontent.com.json');
		$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/settings/oauth2callback');
		$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

		if (! isset($_GET['code'])) {
		  $auth_url = $client->createAuthUrl();
		  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
		} else {
		  $client->authenticate($_GET['code']);
		  $_SESSION['access_token'] = $client->getAccessToken();
		  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
		  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}
		die;
	}
	function getService()
	{
		// Creates and returns the Analytics service object.
		// Load the Google API PHP Client Library.
		App::import('Vendor','google-api-php-client/src/Google/autoload');
	 
		// Use the developers console and replace the values with your
		// service account email, and relative location of your key file.
		$client_id = '424480929196-rsnrimtnqgs6nqaqa0h2at2tcqs7qm68.apps.googleusercontent.com'; //Client ID
		$service_account_name = 'shoptt-project@appspot.gserviceaccount.com'; //Email Address
		$key_file_location = '/var/www/html/webapp/Shoptt-Project-dabc08377906.p12'; //key.p12

		$client = new Google_Client();
		$client->setApplicationName("shoptt-project");
		$service = new Google_Service_Analytics($client);
		$analytics = new Google_Service_Analytics($client);
		
		if (isset($_SESSION['service_token'])) {
		  $client->setAccessToken($_SESSION['service_token']);
		} 
		$key = file_get_contents($key_file_location);
		 $cred = new Google_Auth_AssertionCredentials(
			  $service_account_name,
			  array(Google_Service_Analytics::ANALYTICS_READONLY),
			  $key
		  );
		$client->setAssertionCredentials($cred);
		if ($client->getAuth()->isAccessTokenExpired()) {
			$client->getAuth()->refreshTokenWithAssertion($cred);
		}
		$_SESSION['service_token'] = $client->getAccessToken();
		
		/************************************************
		  We're just going to make the same call as in the
		  simple query as an example.
		 ************************************************/
		 
		$results = $service->management_accountSummaries->listManagementAccountSummaries();
		$items = $results->getItems();
		$uaid = $this -> Auth -> user('User.UA-ID');
		$resultsNew = $items[0]->getWebProperties(); 
		foreach ($resultsNew as $item) {
			if($item->id ==  $uaid){
				$profiles = $service->management_profiles->listManagementProfiles($items[0]->getId(), $uaid);
				$proitems  = $profiles->getItems();	
				$profileid  = $proitems[0]->getId();
			}	
		} 
		$results = $analytics->data_ga->get(
		  'ga:'.$profileid,
		  '30daysAgo',
		  'today',
		  'ga:sessions,ga:users,ga:pageviews',
		  array(
			'dimensions'  => 'ga:date',
			'sort'        => '-ga:sessions',
			'max-results' => 15
		  ) );
		$rows = $results->getRows();

		/**
		 * Format and output data as JSON
		 */
		$data = array();
		foreach( $rows as $row ) {
		  $data[] = array(
			'date'   => date('M d',strtotime($row[0])),
			'sessions'  => $row[1],
		 );
		}
		
		return json_encode( $data );
	
	}
	
	public function invoicedetails(){
		$pageVar['menu']='Invoices';
		$userid = $this -> Auth -> user('User.userid');
		$this -> layout = '/settings/setting_default';
		$this -> request -> data = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));
		Stripe::setApiKey(Configure::read('Stripe.Secret'));
		$paymentdata = $this -> Card -> find('first', array('conditions' => array('id' => $userid)));
		$cust_id = $paymentdata['Card']['customer_id'];
		if(isset($cust_id) && !empty($cust_id)){
			try{
				$customer = Stripe_Customer::retrieve($cust_id);
				$invoice = Stripe_Invoice::all(
					array(
					'customer' => $customer->id,
					)
				);
				$upcoming_invoice = Stripe_Invoice::upcoming(
					array(
					'customer' => $customer->id,
					)
				);
				
				$this->set('invoices',$invoice);
				
				$this->User->bindModel(
					array('hasOne' => array(
							'Card' => array(
								'className' => 'Card',
								'foreignKey' => 'id',
								'conditions' => array('Card.id' => $this->Auth->user('User.userid'))
							)
						)
					)
				);	
				$this -> request -> data  = $this -> User -> find('first', array('conditions' => array('userid' => $userid)));	
				
				if(isset($this -> request -> data['Card']['card_random'])){
					$cardNumber  = base64_decode($this -> request -> data['Card']['card_number'])/$this -> request -> data['Card']['card_random'];
					$this -> request -> data['Card']['card_number'] = $this->MaskCreditCard($this->FormatCreditCard($cardNumber));
				}
				
				$this -> set('pageVar', $pageVar);
			//	$this->set('upcoming_invoice',$upcoming_invoice);
			} catch (Exception $e) {
				$this -> Session -> setFlash(__($e->jsonBody['error']['message']), '/message/error');
				$this -> redirect(array('controller' => 'settings', 'action' => 'invoicedetails'));
			}
		}
		
		
	}
}
