<?php

/**
 *
 * @property-read Image $Image
 */
class ImagesController extends AppController {

    public function admin_index() {
        $this->set('images', $this->Image->find('all'));
        $this->set('title', __t('Images'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->Image;

        if (!empty($this->params['named']['node'])) {
            $this->data['Image']['node_id'] = $this->params['named']['node'];
        }

        if ($this->data) {
            if ($this->Image->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->Image->read();
        }

        $this->set('title', __t(!$this->id ? 'Add image' : 'Edit image'));
    }

    public function admin_delete($id) {
        $this->Image->id = $id;
        $this->Image->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
