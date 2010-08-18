<?php

/**
 *
 * @property-read Attachment $Attachment
 */
class AttachmentsController extends AppController {

    public function admin_index() {
        $this->set('attachments', $this->Attachment->find('all'));
        $this->set('title', __t('Attachments'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->Attachment;

        if (!empty($this->params['named']['node'])) {
            $this->data['Attachment']['node_id'] = $this->params['named']['node'];
        }

        if ($this->data) {
            if ($this->Attachment->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->Attachment->read();
        }

        $this->set('title', __t(!$this->id ? 'Add attachment' : 'Edit attachment "{$name}"', array('name' => h($this->data['Attachment']['title']))));
    }

    public function admin_delete($id) {
        $this->Attachment->id = $id;
        $this->Attachment->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
