<?php

/**
 *
 * @property-read Group $Group
 */
class User extends AppModel {
    public $useTable = 'auth_users';

    public $displayField = 'username';

    public $actsAs = array(
        'Translate', // needed by Group
    );

    public $hasMany = array(
        'AclRule' => array(
            'className' => 'Auth.AclRule',
            'foreignKey' => 'user_id',
            'dependent' => true,
        ),
    );

    public $hasAndBelongsToMany = array(
        'Group' => array(
            'className' => 'Auth.Group',
            'joinTable' => 'auth_groups_users',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'group_id',
        ),
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
