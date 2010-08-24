<?php

/**
 *
 * @property-read AuthAclRule $AuthAclRule
 */
class AuthAclRulesController extends AppController {

    public function admin_index() {
        $this->set('aclRules', $this->AuthAclRule->find('all'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        $this->set('users', $this->AuthAclRule->AuthUser->find('list'));
        $this->set('groups', $this->AuthAclRule->AuthGroup->find('list'));

        if ($this->data) {
            if ($this->AuthAclRule->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif($this->id) {
            $this->data = $this->AuthAclRule->read(null, $this->id);
        }
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }

    public function admin_delete($id) {
        $this->AuthAclRule->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }
    
}
