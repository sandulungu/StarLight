<?php

/**
 *
 * @property-read AuthAclRule $AuthAclRule
 */
class AuthAclRulesController extends AppController {

    public function admin_index() {
        $this->set('aclRules', $this->AuthAclRule->find('all'));
        $this->set('title', __t('ACL rules'));
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

        $this->set('title', !$this->id ?__t('Add rule') : __t('Edit rule'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }

    public function admin_delete($id) {
        $this->AuthAclRule->id = $id;
        $this->AuthAclRule->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }
    
}
