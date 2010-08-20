<?php

/**
 *
 * @property-read CmsAttachment $CmsAttachment
 */
class CmsAttachmentsController extends AppController {

    public function admin_index() {
        $options = array();

        if (!empty($this->params['named']['node'])) {
            $this->set('nodeId', $nodeId = $this->params['named']['node']);
            $options['conditions']['CmsAttachment.cms_node_id'] = $nodeId;
        }

        $this->set('attachments', $this->CmsAttachment->find('all', $options));
        $this->set('title', __t('Attachments'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->CmsAttachment;

        if ($this->data) {
            if ($this->CmsAttachment->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsAttachment->read();
        }

        if (!empty($this->params['named']['node'])) {
            $this->data['CmsAttachment']['cms_node_id'] = $this->params['named']['node'];
        }

        $this->set('title', __t(!$this->id ? 'Add attachment' : 'Edit attachment'));
    }

    public function admin_delete($id) {
        $this->CmsAttachment->id = $id;
        $this->CmsAttachment->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
