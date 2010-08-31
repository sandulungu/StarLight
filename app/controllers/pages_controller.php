<?php

/**
 *
 * @property-read Page $Page
 */
class PagesController extends AppController {

    public function view($id = null) {
        $this->_view();
    }

    public function admin_index() {
        $this->_admin_index();
    }

    public function admin_add() {
        $this->_admin_add();
    }

    public function admin_edit() {
        $this->_admin_edit();
    }

    public function admin_delete() {
        $this->_admin_delete();
    }

}
