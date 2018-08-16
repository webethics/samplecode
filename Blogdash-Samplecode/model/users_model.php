<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Users_Model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function checkAuth($login, $password, $type) {
        $login = $this->db->escape($login);

        $sql = 'SELECT * FROM login';
        $sql .= " WHERE (username = {$login} OR email = {$login})";
        $sql .= " AND password = '{$password}'";
        if($type != 0) {
          $sql .= " AND type = {$type}";
        }

        $query = $this->db->query($sql);

        $result = array(
            'quantity'  => $query->num_rows(),
            'results'   => $query->result()
        );

        return $result;
    }
	
	  public function doRegisterBlogger($bloggerData) {
        unset($bloggerData['passconf']);
        unset($bloggerData['captcha_code']);
        $bloggerData['password'] = sha1($bloggerData['password']);
        $now = date('Y-m-d H:i:s');
        $blogger_table = array(
            'username'      => $bloggerData['username'],
            'password'      => $bloggerData['password'],
            'first_name'    => $bloggerData['first_name'],
            'last_name'     => $bloggerData['last_name'],
            'email'         => $bloggerData['email'],
            'possible_scribnia_id'         => $bloggerData['possible_scribnia_id'],
            'register_date' => $now,
			'invited_by_managerid'    =>$bloggerData['invited_by_managerid']
        );
		 $blogger_table['category_id'] = (isset($bloggerData['category_id']) && $bloggerData['category_id']) ? $bloggerData['category_id'] : $this->config->item('default_category_id');  
	    $blogger_table['subcateg_id'] = (isset($bloggerData['subcateg_id']) && $bloggerData['subcateg_id']) ? $bloggerData['subcateg_id'] : $this->config->item('default_author_subcategory_id');
		
		$this->db->insert('blogdash_blogger', $blogger_table);
        $id_blogger = $this->db->insert_id();
        $data['activation_code'] = md5(sha1($id_blogger * microtime()));
        $this->db->update('blogdash_blogger', $data, array('id' => $id_blogger));
		return $id_blogger; 
    }

  } ?>