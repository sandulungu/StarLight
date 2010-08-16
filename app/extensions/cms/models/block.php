<?php

/**
 *
 * @property-read Node $Node
 */
class Block extends AppModel {
    public $useTable = 'cms_blocks';

    public $order = array('Block.placement', 'Block.weight');

    public $actsAs = array(
        'Translate' => array('title', 'body'),
        'Mirrored' => array(
            'findOptions' => array('conditions' => array('Block.visible' => true)),
            'collectionField' => 'collection',
            'name' => 'Block',
            'groupField' => 'placement',
            'indexField' => 'name',
            'valueFields' => array('title', 'url', 'body', 'cache_time'),
        ),
    );

    public $belongsTo = array(
        'Cms.Node',
    );

    public $validate = array(
        'name' => array(
            'rule' => 'alphaNumeric',
        ),
        'placement' => array(
            'rule' => 'alphaNumeric',
            'required' => true,
        ),
        'collection' => array(
            'rule' => 'alphaNumeric',
        ),
        'weight' => array(
            'rule' => 'numeric',
            'required' => true,
        ),
        'visible' => array(
            'rule' => 'validBool',
            'required' => true,
        ),
    );

}
