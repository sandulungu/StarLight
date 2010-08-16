<?php

/**
 *
 * @property-read Node $Node
 */
class NavigationLink extends AppModel {
    public $useTable = 'cms_navigation_links';

    public $order = 'NavigationLink.lft';

    public $actsAs = array(
        'Tree',
        'Translate' => array('title', 'hint'),
        'Mirrored' => array(
            'findOptions' => array('conditions' => array('NavigationLink.visible' => true)),
            'collectionField' => 'collection',
            'name' => 'Navigation.cms',
            'indexField' => 'name',
            'valueFields' => array('title', 'class', 'target', 'rel', 'hint', 'url'),
        ),
    );

    public $belongsTo = array(
        'Cms.Node',
    );

    public $validate = array(
        'title' => array(
        ),
        'target' => array(
            'rule' => '/^[a-z0-9_]*$/i',
        ),
        'rel' => array(
            'rule' => '/^[a-z0-9_]*$/i',
        ),
//        'url' => array(
//            'rule' => 'url',
//        ),
        'name' => array(
            'rule' => 'alphaNumeric',
        ),
        'collection' => array(
            'rule' => 'alphaNumeric',
        ),
        'visible' => array(
            'rule' => 'validBool',
            'required' => true,
        ),
    );

}
