<?php
App::uses('AppModel', 'Model');

class User extends AppModel {
	var $primaryKey='userid';
      
	/*----------------------------------------validation for the user -------------------------------*/	
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required'
            )
        ),
        
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            )
        ),'conf_password' => array(
           'isMatch' => array(
            'rule' => array('isMatch', 'password'),
            'message' => 'The passwords did not match'
        	),
            'required' => array(
       
                'rule' => array('notEmpty'),
                'message' => 'A confirm password is required'
            )
        ),
        'email' => array(
            'required' => array(
                'rule' => '/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i',
                'message' => 'Please enter valid  Email Address.',
                
            )
        )
        
    );
	// this is used by the auth component to turn the password into its hash before comparing with the DB
    function hashPasswords($data) {
         return md5($data);
    }		
	public function beforeSave($options = array()) {
 		if(!empty($this->data[$this->alias]['password'])){
			$this->data[$this->alias]['password'] = md5($this->data[$this->alias]['password']);  
        }
		return true;
	}
  
  
}

?>
