<?php
App::uses('AuthComponent', 'Controller/Component');
App::uses('AppModel', 'Model');

class Account extends AppModel {
	var $primaryKey='id';
	public $belongsTo = array(
        'User' => array(
            'className' => 'User',// association with the user table
            'foreignKey' => 'userid'
        )
    );

}

?>
