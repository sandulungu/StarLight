<?php

/**
 *
 * @property-read Node $Node
 */
class NodesController extends AppController {

    public function view($id) {
        $this->set('node', $node = SlNode::read($id));
        if (!$node) {
            $this->cakeError();
        }
        $this->set('title', $node['Node']['title']);
    }

    public function admin_view($id) {
        $this->set('node', $node = SlNode::read($id));
        if (!$node) {
            $this->cakeError();
        }
        $this->set('title', __t('Article "{$title}"', array('title' => $node['Node']['title'])));
    }

    public function admin_index() {
        $query = array(
            'contain' => array(
//                'User',
                'Image',
//                'ImageGallery',
//                'Node',
//                'Block',
//                'NavigationLink',
//                'Tag' => array('TagCategory'),
            ),
        );

//        if (isset($this->params['named']['tag'])) {
//            $tagId = $this->params['named']['tag'];
//            $this->set('tag', $this->Node->Tag->field('Tag.name', array('Tag.id' => $tagId)));
//
//            $query['conditions']['TagFilter.id'] = $tagId;
//            $query['link']['CmsNodesTags']['TagFilter'] = array(
//                'class'	=> 'Tag',
//                'conditions' => 'TagFilter.id = CmsNodesTags.tag_id', // Join condition (LEFT JOIN x ON ...)
//                'fields' => array('TagFilter.id')
//			);
//        }

        $this->set('nodes', $this->Node->find('all', $query));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        $this->set('tags', SlNode::getTagList());

        if ($this->data) {
            if ($this->Node->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = SlNode::read($this->id);
        }

        $this->set('title', __t(!$this->id ? 'Add article' : 'Edit article "{$title}"', array('title' => $this->data['Node']['title'])));
    }

    public function admin_delete($id) {
        $this->Node->id = $id;
        $model = $this->Node->field('Node.model');
        if ($model) {
            $plugin = $this->Node->field('Node.plugin');
            $foreignKey = $this->Node->field('Node.foreign_key');
            ClassRegistry::init("$plugin.$model")->delete($foreignKey, true);
        }
        
        $this->Node->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
        $this->Node->plugin;
    }
}
