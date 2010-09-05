<?php

/**
 *
 * @property-read CmsAttachment $CmsAttachment
 */
class CmsAttachmentsController extends AppController {

    public function admin_index() {
        $this->_admin_index();
    }

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
