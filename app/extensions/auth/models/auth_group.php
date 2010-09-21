<?php

/**
 *
 * @property-read AuthUser $AuthUser
 */
class AuthGroup extends AppModel {
    public $useTable = 'auth_groups';

    //public $displayField = 'name';

    public $actsAs = array(
        'Translate' => array('description'),
    );

    public $hasMany = array(
        'AuthAclRule' => array(
            'className' => 'Auth.AuthAclRule',
            'dependent' => true,
        ),
    );

    public $hasAndBelongsToMany = array(
        'Auth.AuthUser',
    );

    public $order = 'AuthGroup.name';

    public $validate = array(
        'name' => array(
            array(
                'rule' => 'alphaNumeric',
                'required' => true,
                'message' => 'Only alphanumerical characters allowed',
            ),
            array(
                'rule' => 'isUnique',
                'required' => true,
                'message' => 'This group already exists',
            ),
        ),
    );

}
