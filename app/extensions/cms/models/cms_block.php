<?php

/**
 *
 * @property-read CmsNode $CmsNode
 */
class CmsBlock extends AppModel {
    public $useTable = 'cms_blocks';

    public $order = array(
        'CmsBlock.placement' => 'ASC',
        'CmsBlock.weight' => 'ASC'
    );

    public $actsAs = array(
        'Translate' => array('title', 'body', 'content'),
        'Markdown' => array('content' => 'body'),
        'Mirrored' => array(
            'findOptions' => array('conditions' => array('CmsBlock.visible' => true)),
            'collectionField' => 'collection',
            'name' => 'Block',
            'groupField' => 'placement',
            'indexField' => 'name',
            'valueFields' => array('title', 'url', 'body', 'cache_time'),
        ),
    );

    public $belongsTo = array(
        'Cms.CmsNode',
    );

    public $validate = array(
        'url' => array(
            'rule' => 'url',
            'allowEmpty' => true,
        ),
        'name' => array(
            'rule' => 'alphaNumeric',
            'allowEmpty' => true,
        ),
        'cache_time' => array(
            'rule' => '/^(\+[0-9]+ (seconds?|minutes?|hours?|days?|weeks?|months?|years?) ?)*$/i',
            'message' => 'Please use a strtotime() compatible string',
        ),
        'placement' => array(
            'rule' => 'alphaNumeric',
            'required' => true,
        ),
        'collection' => array(
            'rule' => 'alphaNumeric',
            'allowEmpty' => true,
        ),
        'weight' => array(
            'rule' => 'numeric',
        ),
    );

}
