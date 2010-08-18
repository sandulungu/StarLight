<?php

/**
 *
 * @property-read Tag $Tag
 */
class TagsController extends AppController {

    public function view($id) {
        $this->set('tag', $tag = $this->Tag->read(null, $id));
        if (!$tag) {
            $this->cakeError();
        }
        
        if ($tag['Tag']['node_id']) {
            $this->set('node', $node = SlNode::get($tag['Tag']['node_id']));
        }
        
        if ($node) {
            $this->set('title', $node['Node']['title']);
        }
        else {
            $this->set('title', $tag['Tag']['name']);
            
            // get all nodes associated with the Tag
            $this->set('nodes', $this->Tag->Node->findCached('all', array(
                'conditions' => array('TagFilter.id' => $id),
                'link' => array('CmsNodesTags' => array('TagFilter' => array(
                    'class'	=> 'Tag',
                    'conditions' => 'TagFilter.id = CmsNodesTags.tag_id', // Join condition (LEFT JOIN x ON ...)
                    'fields' => array('TagFilter.id')
                ))),
            )));
        }
    }

    public function admin_index() {
        $this->set('tags', $this->Tag->find('all'));
        $this->set('title', __t('Tags'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        if (!empty($this->params['named']['tag_type'])) {
            $this->data['Tag']['tag_type_id'] = $this->params['named']['tag_type'];
        }
        $this->set('tagTypes', $this->Tag->TagType->find('list'));

        if ($this->data) {
            if ($this->Tag->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->Tag->read();
        }

        $this->set('title', __t(!$this->id ? 'Add tag' : 'Edit tag "{$name}"', array('name' => h($this->data['Tag']['name']))));
    }

    public function admin_delete($id) {
        $this->Tag->id = $id;
        $this->Tag->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
