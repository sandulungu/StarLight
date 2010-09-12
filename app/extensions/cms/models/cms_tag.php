<?php

/**
 *
 * @property-read CmsNode $CmsNode
 * @property-read CmsTagCategory $CmsTagCategory
 */
class CmsTag extends AppModel {

    public $actsAs = array(
        'Translate' => array('name'),
    );

    public $order = array(
        'CmsTag.cms_tag_category_id' => 'ASC',
        'CmsTag.name' => 'ASC'
    );

    public $belongsTo = array(
        'Cms.CmsTagCategory',
        'Cms.CmsNode',
    );

    public $hasAndBelongsToMany = array(
        'TaggedNode' => array(
            'className' => 'Cms.CmsNode',
        ),
    );

    public $validate = array(
        'name' => array(
        ),
    );

}
