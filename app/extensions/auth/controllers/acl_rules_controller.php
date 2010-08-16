<?php

/**
 *
 * @property-read AclRule $AclRule
 */
class AclRulesController extends AppController {

    public function admin_index() {
        $this->set('aclRules', $this->AclRule->find('all'));
        $this->set('title', __t('ACL rules'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        $this->set('users', $this->AclRule->User->find('list'));
        $this->set('groups', $this->AclRule->Group->find('list'));

        if ($this->data) {
            if ($this->AclRule->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif($this->id) {
            $this->data = $this->AclRule->read(null, $this->id);
        }

        $this->set('title', !$this->id ?
            __t('Add rule') :
            __t('Edit rule')
        );
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }

    public function admin_delete($id) {
        $this->AclRule->id = $id;
        $this->AclRule->delete();
        $this->redirect(array('action' => 'index'));
    }
    
}
