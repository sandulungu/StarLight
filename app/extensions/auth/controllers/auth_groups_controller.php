<?php

/**
 *
 * @property-read AuthGroup $AuthGroup
 */
class AuthGroupsController extends AppController {

    public function admin_index() {
        $this->set('groups', $this->AuthGroup->find('all'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->AuthGroup;

        if ($this->data) {
            if ($this->AuthGroup->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        } elseif($this->id) {
            $this->data = $this->AuthGroup->read(null, $this->id);
        }
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }

    public function admin_delete($id) {
        $this->AuthGroup->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }
    
}
