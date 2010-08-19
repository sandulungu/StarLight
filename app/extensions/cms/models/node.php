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
        'Markdown' => array('title', 'body'),
        'Tree',
        'Translate' => array('title', 'body', 'markdown_title', 'markdown_body'),
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
            'className' => 'Cms.Image',
            'foreignKey' => 'node_id',
            'dependent' => true,
        ),
        'Attachment' => array(
            'className' => 'Cms.Attachment',
            'foreignKey' => 'node_id',
            'dependent' => true,
        ),
        'Block' => array(
            'className' => 'Cms.Block',
            'foreignKey' => 'node_id',
            'dependent' => true,
        ),
        'NavigationLink' => array(
            'className' => 'Cms.NavigationLink',
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
        ),
    );

	function saveAll($data = null, $options = array()) {
        $controller = Sl::getInstance()->controller;
        $isNew = !$controller->id;

        // set associated model info
        if ($controller->modelClass != 'Node') {
            $data['Node'] += array(
                'model' => $controller->modelClass,
                'plugin' => $controller->plugin,
            );
        }

        if (!empty($data['Node']['model'])) {
            if (empty($options['validation']) || $options['validation'] != 'only') {
                if (!parent::saveAll($data, array('validation' => 'only', 'atomic' => true) + $options)) {
                    return false;
                }
            }

            $modelObject = ClassRegistry::init("{$data['Node']['plugin']}.{$data['Node']['model']}");
            $modelObject->saveAll($data, $options);
            $data['Node'] += array(
                'foreign_key' => $modelObject->id,
            );
        }

        // remove empty Images, Attachments from data to be saved
        if (!empty($data['Image'])) {
            if (empty($data['Image']['id']) && empty($data['Image']['filename']['name'])) {
                unset($data['Image']);
            }
        }
        if (!empty($data['Attachment'])) {
            foreach ($data['Attachment'] as $i => $image) {
                if (empty($image['id']) && empty($image['filename']['name'])) {
                    unset($data['Attachment'][$i]);
                }
            }
        }
        if (!empty($data['ImageGallery'])) {
            foreach ($data['ImageGallery'] as $i => $image) {
                if (empty($image['id']) && empty($image['filename']['name'])) {
                    unset($data['ImageGallery'][$i]);
                }
            }
        }

        if ($isNew) {
            $data['Node']['user_id'] = SlAuth::user('id');
        }

        return parent::saveAll($data, $options);
    }
    
}
