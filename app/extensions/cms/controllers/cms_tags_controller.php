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
        }

        // get all nodes associated with the CmsTag
        $this->set('nodes', SlNode::find('all', array(
            'conditions' => array('TagFilter.id' => $id),
            'link' => array('CmsNodesTags' => array('TagFilter' => array(
                'class'	=> 'CmsTag',
                'conditions' => 'TagFilter.id = CmsNodesTags.tag_id', // Join condition (LEFT JOIN x ON ...)
                'fields' => array('TagFilter.id')
            ))),
        )));
    }

    public function admin_index() {
        $this->set('tagCategories', $this->CmsTag->CmsTagCategory->find('all'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        if ($this->data) {
            if ($this->CmsTag->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsTag->read();
        }

        if (!empty($this->params['named']['tag_category'])) {
            $this->data['CmsTag']['cms_tag_category_id'] = $this->params['named']['tag_category'];
        }
        $this->set('cmsTagCategories', $this->CmsTag->CmsTagCategory->find('list'));

        $this->set('cmsNodes', $this->CmsTag->CmsNode->find('treelist'));
    }

    public function admin_delete($id) {
        $this->CmsTag->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
