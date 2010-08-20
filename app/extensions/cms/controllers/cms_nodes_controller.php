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
//                'ImageGallery',
//                'CmsNode',
//                'CmsBlock',
//                'CmsNavigationLink',
//                'CmsTag' => array('TagCategory'),
            ),
        );

        if (!empty($this->params['named']['parent'])) {
            $this->set('parentId', $parentId = $this->params['named']['parent']);
            $options['conditions']['CmsNode.parent_id'] = $parentId;
        }

//        if (isset($this->params['named']['tag'])) {
//            $tagId = $this->params['named']['tag'];
//            $this->set('tag', $this->CmsNode->CmsTag->field('CmsTag.name', array('CmsTag.id' => $tagId)));
//
//            $query['conditions']['TagFilter.id'] = $tagId;
//            $query['link']['CmsNodesTags']['TagFilter'] = array(
//                'class'	=> 'CmsTag',
//                'conditions' => 'TagFilter.id = CmsNodesTags.tag_id', // Join condition (LEFT JOIN x ON ...)
//                'fields' => array('TagFilter.id')
//			);
//        }

        $this->set('nodes', $this->CmsNode->find('all', $options));
        $this->set('title', __t('Content nodes'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        //$this->set('tags', SlNode::getTagList());
        $this->set('parents', $this->CmsNode->find('treelist', array('conditions' => array('CmsNode.id !=' => $this->id))));

        if ($this->data) {
            if ($this->CmsNode->saveAll($this->data)) {
                $this->redirect(array('action' => 'view', $this->CmsNode->id));
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsNode->read(null, $this->id);
        }

        if (!empty($this->params['named']['parent'])) {
            $this->data['CmsNode']['parent_id'] = $this->params['named']['parent'];
        }
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
