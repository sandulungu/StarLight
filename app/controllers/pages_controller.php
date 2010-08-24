<?php

/**
 *
 * @property-read Page $Page
 */
class PagesController extends AppController {

    public function view($id = null) {
        $this->set('page', $page = $this->Page->read());
        if (empty($page)) {
            $this->cakeError();
        }
        $this->set('title', h($page['Page']['title']));
    }

    public function admin_index() {
        $this->set('pages', $this->Page->find('all'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->Page;

        if ($this->data) {
            if ($this->Page->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        } 
        elseif ($this->id) {
            $this->data = $this->Page->read();
        }
    }

    public function admin_delete($id) {
        $this->Page->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }

}
