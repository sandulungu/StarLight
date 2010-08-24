<?php

/**
 *
 * @property-read CmsAttachment $CmsAttachment
 * @property-read CmsBlock $CmsBlock
 * @property-read CmsImage $CmsImage
 * @property-read CmsNavigationLink $CmsNavigationLink
 * @property-read CmsTag $CmsTag
 * @property-read AuthUser $AuthUser
 */
class CmsNode extends AppModel {

    public $actsAs = array(
        'Markdown' => array('teaser', 'body'),
        'Translate' => array('title', 'short_title', 'teaser', 'body', 'markdown_teaser', 'markdown_body', 'meta_keywords', 'meta_description'),
        'Linkable.Linkable',
        'Containable',
        'Tree',
        'Cacheable',
    );

    public $order = 'CmsNode.lft';

    public $belongsTo = array(
        'Auth.AuthUser',
        'Cms.CmsImage',
        'ParentNode' => array(
            'className' => 'Cms.CmsNode',
            'foreignKey' => 'parent_id',
        ),
    );

    public $hasMany = array(
        'ImageGallery' => array(
            'className' => 'Cms.CmsImage',
            'dependent' => true,
        ),
        'CmsAttachment' => array(
            'className' => 'Cms.CmsAttachment',
            'dependent' => true,
        ),
        'CmsBlock' => array(
            'className' => 'Cms.CmsBlock',
            'dependent' => true,
        ),
        'CmsNavigationLink' => array(
            'className' => 'Cms.CmsNavigationLink',
            'dependent' => true,
        ),
    );

    public $hasAndBelongsToMany = array(
        'Cms.CmsTag',
    );

    public $validate = array(
        'model' => array(
            'rule' => 'alphaNumeric',
        ),
        'title' => array(
        ),
//        'body' => array(
//        ),
        'skin' => array(
            'rule' => 'alphaNumeric',
        ),
    );

    function read($fields = null, $id = null) {
         $data = parent::read($fields, $id);
         if ($data) {
             if (!empty($data['CmsNode']['model'])) {
                 $data2 = ClassRegistry::init("{$data['CmsNode']['plugin']}.{$data['CmsNode']['model']}")->read(null, $data['CmsNode']['foreign_key']);
                 if ($data2) {
                     $data += $data2;
                 } else {
                     $data = null;
                 }
             }
         }
         return $data;
    }

	function saveAll($data = null, $options = array()) {
        $controller = Sl::getInstance()->controller;
        $isNew = !$controller->id;

        // set associated model info
        if ($controller->modelClass != 'CmsNode') {
            $data['CmsNode'] += array(
                'model' => $controller->modelClass,
                'plugin' => $controller->plugin,
            );
        }

        // remove empty Images, Attachments from data to be saved
        if (!empty($data['CmsImage'])) {
            if (empty($data['CmsImage']['id']) && empty($data['CmsImage']['filename']['name'])) {
                unset($data['CmsImage']);
            }
        }
        if (!empty($data['CmsAttachment'])) {
            foreach ($data['CmsAttachment'] as $i => $image) {
                if (empty($image['id']) && empty($image['filename']['name'])) {
                    unset($data['CmsAttachment'][$i]);
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
            $data['CmsNode']['auth_user_id'] = SlAuth::user('id');
        }

        if (!empty($data['CmsNode']['model'])) {
            if (empty($options['validation']) || $options['validation'] != 'only') {
                if (!parent::saveAll($data, array('validate' => 'only', 'atomic' => true) + $options)) {
                    return false;
                }
            }

            $modelObject = ClassRegistry::init("{$data['CmsNode']['plugin']}.{$data['CmsNode']['model']}");
            if (!$modelObject->saveAll($data, $options)) {
                return false;
            }
            $data['CmsNode'] += array(
                'foreign_key' => $modelObject->id,
            );
        }

        $result = parent::saveAll($data, $options);
        if ($result && $isNew && $this->CmsImage->id) {
            $this->CmsImage->saveField('cms_node_id', $this->id);
        }
        return $result;
    }
    
}
