<?php

/**
 *
 * @property-read CmsNode $CmsNode
 */
class CmsNavigationLink extends AppModel {
    public $useTable = 'cms_navigation_links';

    public $order = 'CmsNavigationLink.lft';

    public $actsAs = array(
        'Tree',
        'Translate' => array('title', 'hint'),
        'Mirrored' => array(
            'findOptions' => array('conditions' => array('CmsNavigationLink.visible' => true)),
            'collectionField' => 'collection',
            'name' => 'Navigation.cms',
            'indexField' => 'name',
            'valueFields' => array('title', 'class', 'target', 'rel', 'hint', 'url'),
        ),
    );

    public $belongsTo = array(
        'Cms.CmsNode',
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
        ),
    );

}
