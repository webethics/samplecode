<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
    public function __construct() {
        parent::__construct();
		$this->template->plugin('jquery');
		$this->template->js('custom-elements-filter');
		$this->load->library('form_validation');
		$this->template->plugin('colorbox');    
		$this->template->js('jsvalidation');    
		$this->template->plugin('popup_pricing');    
		$this->template->plugin('regbox_out');
        $this->template->plugin('colorbox_alert');
    }

    public function index() {
        $this->template->title('Login');

        $data = array(
            'login'             => '',
            'selectType'        => FALSE,
            'accountTypes'      => array(
                'auto'      => '-- AUTO --',
                'user'      => 'User',
                'blogger'   => 'Blogger'
            ),
            'accountType'       => 'auto',
            'error'             => '',
            'form_attributes'   => array(
                'id'        => 'loginForm'
            ),
            'redirect'          => '',
			'remember_me'		=> $this->input->post('remember_me')
        );

        /* Set custom redirect to hidden input field */
        $data['redirect'] = $this->input->get_post('redirect'); 
        $data['same_user_login'] = ($this->session->userdata('same_user_login')===true) ? 'A user is actually using the same credentials.' : '';
        $this->session->set_userdata(array('same_user_login'=>false));
		
		$data['theft_attempt'] = ($this->session->userdata('theft_attempt')===true) ? 'Automatic login session has been interrupted - possible theft attempt.<br>Password change is recommended.' : '';
        $this->session->set_userdata(array('theft_attempt'=>false));

        $this->form_validation->set_rules('login', 'Username', 'trim|required|xss_clean');            
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|xss_clean');
        $this->form_validation->set_error_delimiters('<span class="error">', '</span>');

		$login = $this->input->post('login');
		$password = $this->input->post('password');
		$type = $this->input->post('type');

		if ($data['remember_me']) {
			// flag for uac->rememberUser()
			$this->session->set_userdata('autologin', 'new');
		}
		
			if($login) {
				$data['login'] = $login;
			}

			if($type) {
				$data['accountType'] = $type;
			}

        if ($this->form_validation->run()==false) {
				if ($this->input->is_ajax_request()) { 
					if ($this->input->post('business_login')) {
						$this->load->view('login/business_login', $data);
					} else {
						$data['is_popup'] = true;
						$this->load->view('login/login', $data);
					}
				} else {
					$data['is_popup'] = false;
					$this->template->page($data, 'login', 'simple-wide');
				}
        } else {    //if login form is valid
            /* Try to login */
            $auth = $this->uac->auth($login, $password, $type);
            $data['error'] = $auth['message'];

            $selectType = $this->input->post('selectType');

            if($selectType) {
                if($selectType == 'no') {
                    $data['selectType'] = FALSE;
                }
            }
            if ($this->input->is_ajax_request()) { 
                if ($this->input->post('business_login')) {
                    $this->load->view('login/business_login', $data);
                } else {
                    $data['is_popup'] = true;
                    $this->load->view('login/login', $data);
                }
            } else {
                $data['is_popup'] = false;
                $this->template->page($data, 'login', 'simple-wide');
            }
		}
    }


    public function choose() {
        $multipleLogin = $this->session->userdata('multiple_login');

        if($multipleLogin && count($multipleLogin) > 0) {
            $login = intval($this->input->post('login'));

            if($login > 0) {
                $this->uac->multipleAuth($login);
            } else {
                $data = array(
                    'items'             => $multipleLogin,
                    'form_attributes'   => array(
                        'id'        => 'loginForm'
                    ),
                    'redirect'          => ''
                );
                
                /* Set custom redirect to hidden input field */
                if($this->input->get('redirect') && ($this->input->get('redirect') != '')) {
                    $this->data['redirect'] = $this->input->get('redirect');
                } elseif($this->input->post('redirect') && ($this->input->post('redirect') != '')) {
                    $this->data['redirect'] = $this->input->post('redirect');
                }

                $this->template->page($data, 'multiple', 'simple-wide');
            }
        } else {
            $this->uac->redirect('login');
        }
    }

    public function user() {
        $this->template->title('Login as user');

        $data = array(
            'login'             => '',
            'selectType'        => FALSE,
            'accountTypes'      => array(
                'auto'      => '-- AUTO --',
                'user'      => 'User',
                'blogger'   => 'Blogger'
            ),
            'accountType'       => 'auto',
            'error'             => '',
            'form_attributes'   => array(
                'id'        => 'loginForm'
            ),
            'redirect'          => ''
        );

        /* Set custom redirect to hidden input field */
        if($this->input->get('redirect') && ($this->input->get('redirect') != '')) {
            $this->data['redirect'] = $this->input->get('redirect');
        } elseif($this->input->post('redirect') && ($this->input->post('redirect') != '')) {
            $this->data['redirect'] = $this->input->post('redirect');
        }

        $this->template->page($data, 'login', 'simple-wide');
    }

    public function blogger() {
        $this->template->title('Login as blogger');

        $data = array(
            'login'             => '',
            'selectType'        => FALSE,
            'accountTypes'      => array(
                'auto'      => '-- AUTO --',
                'user'      => 'User',
                'blogger'   => 'Blogger'
            ),
            'accountType'       => 'auto',
            'error'             => '',
            'form_attributes'   => array(
                'id'        => 'loginForm'
            ),
            'redirect'          => ''
        );

        /* Set custom redirect to hidden input field */
        if($this->input->get('redirect') && ($this->input->get('redirect') != '')) {
            $this->data['redirect'] = $this->input->get('redirect');
        } elseif($this->input->post('redirect') && ($this->input->post('redirect') != '')) {
            $this->data['redirect'] = $this->input->post('redirect');
        }

        $this->template->page($data, 'login', 'simple-wide');
    }
	
	public function business() {
		$data = array(
            'login'			=> $this->input->post('login'),
			'redirect'		=> substr($this->input->get('redirect'),1),
			'remember_me'	=> $this->input->post('remember_me')
		);
		$this->load->view('login/business_login', $data);
	}
	
}

?>