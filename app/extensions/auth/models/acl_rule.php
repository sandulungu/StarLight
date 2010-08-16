<?php

/**
 *
 * @property-read User $User
 * @property-read Group $Group
 */
class AclRule extends AppModel {
    public $useTable = 'auth_acl_rules';

    public $actsAs = array(
        'Translate', // needed by Group
        'Mirrored' => array(
            'name' => "Auth.acl",
            'collectionField' => 'collection',
            'indexField' => 'what',
            'groupField' => 'who',
            'valueField' => 'allow',
        ),
    );

    public $belongsTo = array(
        'Auth.User',
        'Auth.Group',
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
