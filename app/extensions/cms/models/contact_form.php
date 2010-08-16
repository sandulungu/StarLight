<?php

class ContactForm extends AppModel {
    public $useTable = 'cms_contact_forms';

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
