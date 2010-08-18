<?php

/**
 *
 * @property-read Node $Node
 * @property-read TagType $TagType
 */
class Tag extends AppModel {
    public $useTable = 'cms_tags';

    public $actsAs = array(
        'Translate' => array('name'),
    );

    public $order = array('Tag.tag_type_id', 'Tag.title');

    public $belongsTo = array(
        'Cms.TagType',
        'Cms.Node',
    );

    public $hasAndBelongsToMany = array(
        'TaggedNode' => array(
            'className' => 'Cms.Node',
            'joinTable' => 'cms_nodes_tags',
            'associationForeignKey' => 'node_id',
            'foreignKey' => 'tag_id',
        ),
    );

    public $validate = array(
        'title' => array(
        ),
    );

}
