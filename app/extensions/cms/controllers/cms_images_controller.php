<?php

/**
 *
 * @property-read CmsImage $CmsImage
 */
class CmsImagesController extends AppController {

    public function admin_index() {
        $options = array();

        if (!empty($this->params['named']['node'])) {
            $this->set('nodeId', $nodeId = $this->params['named']['node']);
            $options['conditions']['CmsImage.cms_node_id'] = $nodeId;
        }

        $this->set('images', $this->CmsImage->find('all', $options));
        $this->set('title', __t('Images'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->CmsImage;

        if ($this->data) {
            if ($this->CmsImage->saveAll($this->data)) {
                $nodeId = $this->CmsImage->field('cms_node_id');
                $this->redirect(
                    $nodeId ?
                    SlNode::url($nodeId, array('admin' => true, 'route' => false)) :
                    array('action' => 'index')
                );
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsImage->read();
        }

        if (!empty($this->params['named']['node'])) {
            $this->data['CmsImage']['cms_node_id'] = $this->params['named']['node'];
        }
    }

    public function admin_delete($id) {
        $this->CmsImage->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_set_as_thumb($id) {
        $this->CmsImage->CmsNode->id = $nodeId = $this->CmsImage->field('cms_node_id');
        $this->CmsImage->CmsNode->saveField('cms_image_id', $id);
        $this->redirect(SlNode::url($nodeId, array('admin' => true, 'route' => false)));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
