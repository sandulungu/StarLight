<?php

/**
 *
 * @property-read AuthUser $AuthUser
 * @property-read AuthGroup $AuthGroup
 */
class AuthAclRule extends AppModel {
    public $useTable = 'auth_acl_rules';

    public $actsAs = array(
        'Translate', // needed by AuthGroup
        'Mirrored' => array(
            'name' => "Auth.acl",
            'collectionField' => 'collection',
            'indexField' => 'what',
            'groupField' => 'who',
            'valueField' => 'allow',
        ),
    );

    public $belongsTo = array(
        'Auth.AuthUser',
        'Auth.AuthGroup',
    );

    public $validate = array(
        'who' => array(
            'rule' => array('alphaNumeric'),
            'required' => true,
        ),
        'what' => array(
            'rule' => array('alphaNumeric'),
            'required' => true,
        ),
        'collection' => array(
            'rule' => array('alphaNumeric'),
        ),
    );

}
