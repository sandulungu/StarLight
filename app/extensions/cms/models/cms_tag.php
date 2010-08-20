<?php

/**
 *
 * @property-read CmsNode $CmsNode
 * @property-read CmsTagType $CmsTagType
 */
class CmsTag extends AppModel {
    public $useTable = 'cms_tags';

    public $actsAs = array(
        'Translate' => array('name'),
    );

    public $order = array('CmsTag.cms_tag_type_id' => 'ASC', 'CmsTag.name' => 'ASC');

    public $belongsTo = array(
        'Cms.CmsTagType',
        'Cms.CmsNode',
    );

    public $hasAndBelongsToMany = array(
        'TaggedNode' => array(
            'className' => 'Cms.CmsNode',
        ),
    );

    public $validate = array(
        'title' => array(
        ),
    );

}
