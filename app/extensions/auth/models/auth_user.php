<?php

/**
 *
 * @property-read AuthGroup $AuthGroup
 */
class AuthUser extends AppModel {
    public $useTable = 'auth_users';

    public $displayField = 'username';

    public $actsAs = array(
        'Translate', // needed by AuthGroup
    );

    public $hasMany = array(
        'AuthAclRule' => array(
            'className' => 'Auth.AuthAclRule',
            'dependent' => true,
        ),
    );

    public $hasAndBelongsToMany = array(
        'Auth.AuthGroup',
    );

    public $validate = array(
        'username' => array(
            array(
                'rule' => 'alphaNumeric',
                'required' => true,
                'message' => 'Only alphanumerical characters allowed',
            ),
            array(
                'rule' => 'isUnique',
                'required' => true,
                'message' => 'Username taken',
            ),
        ),
        'password' => array(
            'rule' => array('minLength', '1'),
            'on' => 'create',
            'required' => true,
        ),
        'fullname' => array(
            'rule' => array('minLength', '1'),
            'required' => true,
        ),
        'email' => array(
            array(
                'rule' => 'email',
                'required' => true,
            ),
            array(
                'rule' => 'isUnique',
                'required' => true,
                'message' => 'Email already registered',
            ),
        ),
    );

}
