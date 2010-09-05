<?php

/**
 *
 * @property-read CmsTagCategory $CmsTagCategory
 */
class CmsTagCategoriesController extends AppController {

    public function admin_edit() {
        $this->_admin_edit();
    }

    public function admin_delete() {
        $this->_admin_delete();
    }

    public function admin_add() {
        $this->_admin_add();
    }
}
