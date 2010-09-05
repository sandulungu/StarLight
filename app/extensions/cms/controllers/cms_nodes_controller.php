<?php

/**
 *
 * @property-read CmsNode $CmsNode
 */
class CmsNodesController extends AppController {

    public function view($id) {
        $this->set('node', $node = SlNode::read($id));
        if (!$node) {
            $this->cakeError();
        }
        $this->set('title', $node['CmsNode']['title']);
    }

    public function admin_view($id) {
        $this->_admin_view();
    }

    public function admin_index() {
        $options = array(
            'contain' => array(
//                'AuthUser',
                'CmsImage',
//                'CmsBlock',
//                'CmsNavigationLink',
//                'CmsImage',
                'CmsTag' //=> array('TagCategory'),
//                'ImageGallery',
//                'ParentNode',
            ),
            'conditions' => $this->postConditions($this->_getPassedDefaults()),
        );

        if (!empty($this->params['named']['tag'])) {
            $tagId = $this->params['named']['tag'];

            $this->set('title', __t('Node tagged as "{$tag}"', array(
                'tag' => $this->CmsNode->CmsTag->field('CmsTag.name', array('CmsTag.id' => $tagId))
            )));

            $options = array(
                // the LinkableBehavior messes around with the Containable on belongsTo associations...
                'link' => array(
                    'CmsImage' => array('conditions' => 'CmsImage.id = CmsNode.cms_image_id'),
                    'CmsNodesCmsTags' => array(
                        'TagFilter' => array(
                            'class'	=> 'CmsTag',
                            'conditions' => 'TagFilter.id = CmsNodesCmsTags.cms_tag_id', // Join condition (LEFT JOIN x ON ...)
                            'fields' => array('TagFilter.id'),
                        ),
                    ),
                ),
                'contain' => array(
                    'CmsTag',
                ),
                'conditions' => array('TagFilter.id' => $tagId),
			);
        }

        $this->_admin_index($options);
    }

    public function admin_edit() {
        $this->_admin_edit();

        $this->set('cmsTags', SlNode::getTagList());
        $this->set('parents', $this->CmsNode->find('treelist', array('conditions' => array('CmsNode.id !=' => $this->id))));
    }

    public function admin_delete($id) {

        // delete associated item
        $this->CmsNode->id = $id;
        $model = $this->CmsNode->field('CmsNode.model');
        if ($model) {
            $plugin = $this->CmsNode->field('CmsNode.plugin');
            $foreignKey = $this->CmsNode->field('CmsNode.foreign_key');
            ClassRegistry::init("$plugin.$model")->delete($foreignKey, true);
        }
        
        $this->_admin_delete();
    }

    public function admin_add() {
        $this->_admin_add();
    }
}
