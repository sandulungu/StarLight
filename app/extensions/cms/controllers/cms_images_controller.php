<?php

/**
 *
 * @property-read CmsImage $CmsImage
 */
class CmsImagesController extends AppController {

    public function admin_index() {
        $this->_admin_index(array(
            'paginate' => array(
                'limit' => 100,
            ),
        ));
    }

    public function admin_edit() {
        $this->_admin_edit();
    }

    public function admin_delete() {
        $this->_admin_delete();
    }

    public function admin_set_as_thumb($id) {
        $this->CmsImage->CmsNode->id = $nodeId = $this->CmsImage->field('cms_node_id');
        $this->CmsImage->CmsNode->saveField('cms_image_id', $id);
        $this->redirect(SlNode::url($nodeId, array('admin' => true, 'route' => false)));
    }

    public function admin_add() {
        $this->_admin_add();
    }
}
