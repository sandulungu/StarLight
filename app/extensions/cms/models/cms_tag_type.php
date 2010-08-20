<?php

/**
 *
 * @property-read CmsTag $CmsTag
 */
class CmsTagType extends AppModel {
    public $useTable = 'cms_tag_types';

    public $actsAs = array(
        'Translate' => array('name'),
    );

    public $order = 'CmsTagType.name';

    public $hasMany = array(
        'CmsTag' => array(
            'className' => 'Cms.CmsTag',
            'dependent' => true,
        ),
    );

    public $validate = array(
        'name' => array(
        ),
    );

}
