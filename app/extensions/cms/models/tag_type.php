<?php

/**
 *
 * @property-read Tag $Tag
 */
class TagType extends AppModel {
    public $useTable = 'cms_tag_types';

    public $actsAs = array(
        'Translate' => array('name'),
    );

    public $order = 'TagType.name';

    public $hasMany = array(
        'Tag' => array(
            'className' => 'Cms.Tag',
            'foreignKey' => 'tag_type_id',
            'dependent' => true,
        ),
    );

    public $validate = array(
        'name' => array(
        ),
    );

}
