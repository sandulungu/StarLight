<?php

/**
 *
 * @property-read CmsTag $CmsTag
 */
class CmsTagsController extends AppController {

    public function view($id) {
        $this->set('tag', $tag = $this->CmsTag->read(null, $id));
        if (!$tag) {
            $this->cakeError();
        }
        
        if ($tag['CmsTag']['node_id']) {
            $this->set('node', $node = SlNode::get($tag['CmsTag']['node_id']));
        }
        
        if ($node) {
            $this->set('title', $node['CmsNode']['title']);
        }
        else {
            $this->set('title', $tag['CmsTag']['name']);
            
            // get all nodes associated with the CmsTag
            $this->set('nodes', $this->CmsTag->CmsNode->findCached('all', array(
                'conditions' => array('TagFilter.id' => $id),
                'link' => array('CmsNodesTags' => array('TagFilter' => array(
                    'class'	=> 'CmsTag',
                    'conditions' => 'TagFilter.id = CmsNodesTags.tag_id', // Join condition (LEFT JOIN x ON ...)
                    'fields' => array('TagFilter.id')
                ))),
            )));
        }
    }

    public function admin_index() {
        $this->set('tags', $this->CmsTag->find('all'));
        $this->set('title', __t('Tags'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        if (!empty($this->params['named']['tag_type'])) {
            $this->data['CmsTag']['tag_type_id'] = $this->params['named']['tag_type'];
        }
        $this->set('tagTypes', $this->CmsTag->CmsTagType->find('list'));

        if ($this->data) {
            if ($this->CmsTag->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsTag->read();
        }

        $this->set('title', __t(!$this->id ? 'Add tag' : 'Edit tag "{$name}"', array('name' => h($this->data['CmsTag']['name']))));
    }

    public function admin_delete($id) {
        $this->CmsTag->id = $id;
        $this->CmsTag->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
