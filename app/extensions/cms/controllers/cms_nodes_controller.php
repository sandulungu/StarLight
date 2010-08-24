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
        $this->set('node', $node = $this->CmsNode->read(null, $id));
        if (!$node) {
            $this->cakeError();
        }
        $this->set('title', __t('Article "{$title}"', array('title' => $node['CmsNode']['title'])));
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

        if (!empty($this->params['named']['parent'])) {
            $this->set('parentId', $parentId = $this->params['named']['parent']);
            $options['conditions']['CmsNode.parent_id'] = $parentId;
        }

        $this->set('nodes', $this->CmsNode->find('all', $options));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        if ($this->data) {
            if ($this->CmsNode->saveAll($this->data)) {
                $this->redirect(array('action' => 'view', $this->CmsNode->id));
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsNode->read(null, $this->id);
        }

        $this->set('cmsTags', SlNode::getTagList());

        if (!empty($this->params['named']['parent'])) {
            $this->data['CmsNode']['parent_id'] = $this->params['named']['parent'];
        }
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
        
        $this->CmsNode->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
