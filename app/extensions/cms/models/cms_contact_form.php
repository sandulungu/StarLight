<?php

class CmsContactForm extends AppModel {
    
    public $actsAs = array(
        'Translate' => array('fields'),
    );

    public $validate = array(
        'email' => array(
            'rule' => 'email',
            'required' => true,
        ),
    );

}
