<?php

/**
 *
 * @property-read Attachment $Attachment
 * @property-read Block $Block
 * @property-read Image $Image
 * @property-read NavigationLink $NavigationLink
 * @property-read Tag $Tag
 * @property-read User $User
 */
class Node extends AppModel {
    public $useTable = 'cms_nodes';

    public $actsAs = array(
        'Containable',
        'Linkable.Linkable',
    );

    public $order = 'Node.title';

    public $belongsTo = array(
        'Cms.User',
        'Cms.Image',
    );

    public $hasMany = array(
        'ImageGallery' => array(
            'className' => 'Cms.Borrow',
            'foreignKey' => 'node_id',
            'dependent' => true,
        ),
        'Attachment' => array(
            'className' => 'Cms.Attachment',
            'foreignKey' => 'node_id',
            'dependent' => true,
        ),
        'Block' => array(
            'className' => 'Cms.Attachment',
            'foreignKey' => 'node_id',
            'dependent' => true,
        ),
        'NavigationLink' => array(
            'className' => 'Cms.Attachment',
            'foreignKey' => 'node_id',
            'dependent' => true,
        ),
    );

    public $hasAndBelongsToMany = array(
        'Tag' => array(
            'className' => 'Cms.Tag',
            'joinTable' => 'cms_nodes_tags',
            'foreignKey' => 'node_id',
            'associationForeignKey' => 'tag_id',
        ),
    );

    public $validate = array(
        'model' => array(
            'rule' => 'alphaNumeric',
        ),
        'body' => array(
        ),
        'skin' => array(
            'rule' => 'alphaNumeric',
        ),
        'visible' => array(
            'rule' => 'validBool',
            'required' => true,
        ),
    );

}
