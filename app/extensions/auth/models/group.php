<?php

/**
 *
 * @property-read User $User
 */
class Group extends AppModel {
    public $useTable = 'auth_groups';

    //public $displayField = 'name';

    public $actsAs = array(
        'Translate' => array('name', 'description'),
    );

    public $hasMany = array(
        'AclRule' => array(
            'className' => 'Auth.AclRule',
            'foreignKey' => 'group_id',
            'dependent' => true,
        ),
    );

    public $hasAndBelongsToMany = array(
        'User' => array(
            'className' => 'Auth.User',
            'joinTable' => 'auth_groups_users',
            'associationForeignKey' => 'user_id',
            'foreignKey' => 'group_id',
        ),
    );

    public $validate = array(
        'name' => array(
        ),
    );

}
