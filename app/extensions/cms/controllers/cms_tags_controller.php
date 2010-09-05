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
        $this->_admin_index();
    }

    public function admin_edit() {
        $this->_admin_edit();

        $this->set('cmsTagCategories', $this->CmsTag->CmsTagCategory->find('list'));
        $this->set('cmsNodes', $this->CmsTag->CmsNode->find('treelist'));
    }

    public function admin_delete() {
        $this->_admin_delete();
    }

    public function admin_add() {
        $this->_admin_add();
    }
}
