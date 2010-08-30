<?php

/**
 *
 * @property-read AuthAclRule $AuthAclRule
 */
class AuthAclRulesController extends AppController {

    public function admin_index() {
        $this->_admin_index();
    }

    public function admin_add() {
        $this->_admin_add();
    }

    public function admin_edit() {
        $this->_admin_edit();

        $this->set('authUsers', $this->AuthAclRule->AuthUser->find('list'));
        $this->set('authGroups', $this->AuthAclRule->AuthGroup->find('list'));
    }

    public function admin_delete($id) {
        $this->_admin_delete();
    }
    
}
