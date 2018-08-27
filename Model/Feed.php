<?php
class Feed extends AppModel {
    public $validate = array(
        'feed_title' => array(
            'required' => array(
                'rule' => array('notBlank'),
                'message' => 'The feed title cannot be blank.'
            )
         )
    );

    function beforeSave($options = array()) {
        $this->data[$this->alias]['created_date'] = date('Y-m-d H:i:s', time());
        return true;
    }
}
