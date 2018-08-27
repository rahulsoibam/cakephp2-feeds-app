<?php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
public $validate = array(
    'username' => array(
        'required' => array(
            'rule' => array('notBlank'),
            'message' => 'You must enter a username.'
        ),
        'length' => array(
            'rule' => array('between', 3, 15),
            'message' => 'Your username must be between 3 and 15 characters long.'
        ),
        'unique' => array(
            'rule'    => 'isUnique',
			'required' => 'create',
            'message' => 'This username has already been taken.'
        )
    ),
    'password' => array(
        'required' => array(
            'rule' => array('notBlank'),
            'message' => 'You must enter a password.'
        ),
        'length' => array(
            'rule' => array('minLength', '6'),
            'message' => 'Your password must be at least 6 characters long.'
        )
    )
);
	function beforeSave($options = array()) {
    	if (isset($this->data[$this->alias]['password'])) {
        	$passwordHasher = new BlowfishPasswordHasher();
        	$this->data[$this->alias]['password'] = $passwordHasher->hash(
            	$this->data[$this->alias]['password']
        	);
    	}
    	return true;
	}
    
}
