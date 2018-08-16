<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Blogger_steps extends CI_Controller {
	public function __construct() {
					parent::__construct();
					$this->load->library('form_validation');
					$this->load->model('blogdash_blogger_model', 'Blogger');
					$this->load->model('publication_model', 'Publication', TRUE);
					$this->load->model('writer_model',"Writer");
					$this->load->model('messages_model',"Message");
					$this->load->library('captcha');
					$this->load->library('tweet');
					$this->template->plugin('jquery');
					$this->template->plugin('colorbox');  
	  		        $this->template->plugin('colorbox_confirm');
					$this->template->js('jsvalidation') ;
			        $this->template->js('custom-elements-filter') ;  
					$this->load->helper('email_message');	
    }
   public function index() {
   
			$this->blogger_referrer_code = $this->input->cookie('refered_claim_code');  
			$this->data['manager']=intval($this->input->cookie('manager_invite_id'));     
			$this->template->title('Registration');
			$this->template->plugin('regbox_out');
			$this->template->plugin('colorbox'); 
			$this->load->model('Users_Model', 'Users');
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('username', 'Username', 'htmlspecialchars|trim|required|alpha_dash|min_length[3]|max_length[20]|xss_clean|callback_username_check');
			$this->form_validation->set_rules('email', 'Email', 'htmlspecialchars|required|valid_email|min_length[5]|max_length[100]|xss_clean|callback_email_check');
			$this->form_validation->set_rules('password', 'Password', 'required|htmlspecialchars|min_length[6]|max_length[100]|xss_clean');
			$this->form_validation->set_rules('passconf', 'Passconf', 'required|htmlspecialchars|min_length[6]|max_length[100]|xss_clean|matches[password]');
			$this->form_validation->set_rules('first_name', 'First Name', 'trim|htmlspecialchars|min_length[2]|max_length[50]|required|xss_clean|callback_alpha_dash_space');
			$this->form_validation->set_rules('last_name', 'Last Name', 'trim|htmlspecialchars|min_length[2]|max_length[50]|required|xss_clean|callback_alpha_dash_space');
			/*  if signin with twitter then not chek that validation */
			$twitter_session = $this->tweet->logged_in();
			if(!$twitter_session){
					$this->form_validation->set_rules('security_code', 'Security_code', 'trim|htmlspecialchars|required|xss_clean|callback_securitycode');
			}
			$hash=$this->input->get('hash',true);     
			$invitation=intval($this->input->get('invitation',true));  
			$this->data['invitation']=$invitation;	
			$this->data['hash']=$hash;        
			$scribnia_claimed=-1;
			$possible_scribnia_id = -1;
			$pitch_id=0;     
			
			// hash or invitation id is present
			if((strlen($hash)==32)OR($invitation)){   
				$this->load->model('Messages_model','Messages');    
				// if invitation id
				if ($invitation){  
					$scribnia_claimed=$invitation;  
					$possible_scribnia_id = $invitation;
				}else{  
					$conditions = array(         
                       'link'    => $hash        
					);    
					$pitch = $this->Messages->Get_pitch($conditions);       
					//if there is pitch with those hash
					if($pitch[0]['blogger_id']){         
						$scribnia_claimed=$pitch[0]['blogger_id'];         
						$pitch_id=$pitch[0]['id'];         
					}    
				} 
				$conditions=array(          
					'writer_id' => array($scribnia_claimed)          
				);     
				$writer=$this->Messages->getScribniaWriter($conditions);          
			} 
			//if there is writer with scribnia_claimed-id than assign his names to form inputs
			if(isset($writer[0])){          
				$this->data['first_name']=str_replace(' ','_',$writer[0]['first_name']);          
				$this->data['last_name']=$writer[0]['last_name'];  
			}else{        
				$this->data['first_name']='';        
				$this->data['last_name']='';        
			}
			if ($this->form_validation->run()  === FALSE) {
				if(!$twitter_session){
					$this->session->unset_userdata('captcha');
				}
				// hash or invitation id is present than using view for them (with solutions block)
				if((strlen($hash)==32)OR($invitation)){  
					$this->template->view('blogger_steps/index_hash',$this->data);  
				}else{  
					$this->template->view('blogger_steps/index',$this->data);  
				}
			} else{
				$post=$this->input->post();
				$user_data = array();
				foreach ($post as $key => $val)
			    $user_data[$key] = mysql_real_escape_string($val);
				$bloggerData = array(
				'username' => $user_data['username'],
				'password' => $user_data['password'],
				'first_name' => $user_data['first_name'],
				'last_name' => $user_data['last_name'],
				'email' => $user_data['email'],
				'possible_scribnia_id' => $possible_scribnia_id,
				'invited_by_managerid'    => $this->data['manager'],
				);
				$blogger_id=$this->Blogger->doRegisterBlogger($bloggerData);
				$this->clearCookie('manager_invite_id');
				if ($this->blogger_referrer_code) {
					
					$this->Blogger->check_blogger_referrer($this->blogger_referrer_code, $blogger_id, $user_data['email'],0);
					$blogger=$this->Blogger->getBloggerByHash($this->blogger_referrer_code);  
					$this->clearCookie('refered_claim_code');
				}
				$this->Message->updateMessSystemBloggerStatus($scribnia_claimed,$act='register');  
				if ((strlen($hash)==32)OR($invitation)) 
				{
					$this->Blogger->sync_blogdash_blogger($blogger_id, $scribnia_claimed);
					$badgeRegistrationInfo=$this->Blogger->getBadgeSignup(array('registered_account_id'=>$blogger_id,'type'=>0)); 
					//if blogger was registered with badge
					if(isset($badgeRegistrationInfo[0]['referer_blogger_id'])){
						$tmpBloggerInfo=$this->Blogger->getBloggerData($badgeRegistrationInfo[0]['referer_blogger_id']);
						if(isset($tmpBloggerInfo['id'])){
							if($tmpBloggerInfo['price_blogger']>0){  
								$amount=$tmpBloggerInfo['price_blogger'];  
							}else{  
								$this->load->config('api');  
								$amount=$this->config->item('hasoffers_per_blogger');  
							}
							$paymentInfo=$this->Blogger->getBloggerPayment(array('blogger_id'=>$tmpBloggerInfo['id'],'type'=>0,'referal_id'=>$blogger_id));    
							//to prevent double paid for claiming  
							if(!isset($paymentInfo[0])){  
								$this->Blogger->saveBloggerPayment(array('blogger_id'=>$tmpBloggerInfo['id'],'amount'=>$amount,'date'=>date('Y-m-d H:i:s'),'status'=>0,'type'=>0,'referal_id'=>$blogger_id));    
							}   
						}
					}
				}	
				//clearing pitch anonymous link after registration  
				if($pitch_id){        
					$updateData=array(      
						'link' => '',      
					);    
					$whereData=array(      
						'id'=>$pitch_id      
					);    
					$this->messages->Update_pitch($updateData,$whereData);      
				}
				/* email to blogger after successfuly registration */
				$this->config->load('email');
				$to = $user_data['email'];
				$from = $this->config->item('blogger_message_from');
				$from_name = $this->config->item('blogger_message_from_name');
				$subject = $this->config->item('blogger_registration_message_subject');
				$message = $this->config->item('blogger_registration_message');
				
				$this->load->library('email');
				$this->email->initialize(array('mailtype' => 'html'));
				$this->email->from($from, $from_name);
				$this->email->to($to);
				$this->email->subject($subject);
				
				$linkstoMessage["BLOGGER-NAME"] = $user_data['first_name']." ".$user_data['last_name'];
				$linkstoMessage["LINK-PITCH-PREFERENCES"] = base_url()."edit_profile/pitches";
				$linkstoMessage["LINK-PITCH-PREFERENCES-TEXT"] = base_url()."edit_profile/pitches";
				$linkstoMessage["LINK-MY-PITHCES"] = base_url()."messages";
				$linkstoMessage["LINK-MY-PITHCES-TEXT"] = base_url()."messages";
				$linkstoMessage["LINK-FREE-BUSINESS-REGISTRATION"] = base_url()."userregistration/free"; 
				$linkstoMessage["LINK-FREE-BUSINESS-REGISTRATION-TEXT"] = base_url()."userregistration/free"; 
				$email_data['title']=$subject; 
				$email_data['text']=filterMessage($message, $linkstoMessage);  
				$message = $this->load->view('default_email',$email_data,true);                         
				
				$this->email->message($message);
				$this->email->send();				
				
				
				/* for direct login */
				$this->uac->auth($user_data['username'],$user_data['password'],'blogger');

			}

		 
		 }
		/* check for username */
		function username_check() {
				$username = $this->input->post('username');
			if ($this->Blogger->checkField('username', $username) == false) {
						$this->form_validation->set_message('username_check', 'This username is already taken');
				if($this->input->post('test')) echo "false";
						return false;
			}else { 
					if($this->input->post('test')) echo "true";		 
					return true;
			} 

		}

		/*  check for email  */
		function email_check() {
				$email = $this->input->post('email');
		if ($this->Blogger->checkField('email', $email) == false) {
					$this->form_validation->set_message('email_check', 'This email is already taken');
				if($this->input->post('test')) echo "false";
					return false;
			}else{   if($this->input->post('test'))echo "true"; return true;}
		}

		/*   captcha check     */
		 function securitycode(){
						$security_code = $this->input->post('security_code');
						if($security_code!=$this->session->userdata('captcha')){
							$this->session->unset_userdata('captcha');
						}
						$twitter_val=$this->session->userdata('captcha');
						if(!empty($twitter_val)){
							if($this->input->post('test')) echo "true";
							return true;
						}else{
							$session_code= $this->session->userdata('security_code');
							if($security_code==$session_code){
								if($this->input->post('test')) echo "true";
								return true;
							}else{
								$this->form_validation->set_message('securitycode', 'Please Enter Correct Code');
								if($this->input->post('test')) echo "false";
								return false;
							} 
						} 
		}
		/* call  captcha image */
		function captcha(){
					$this->captcha->CaptchaSecurityImages('110','35','6');
		}
		 function alpha_dash_space($field_value)  
		{ 
	  		     if (! preg_match("/^([-a-z_' ])+$/i", $field_value))  
	  		      { 
	  		               $this->form_validation->set_message('alpha_dash_space', 'The %s field may only contain characters, spaces, underscores,and dashes.');  
	  		               return FALSE;  
	  		       } 
	  		        else  
	  		         {  return TRUE; } 
		}  

	  
		
}

?>