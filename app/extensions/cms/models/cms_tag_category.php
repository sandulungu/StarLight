<?php

/**
 *
 * @property-read CmsTag $CmsTag
 */
class CmsTagCategory extends AppModel {

    public $actsAs = array(
        'Translate' => array('name'),
    );

    public $order = 'CmsTagCategory.name';

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
