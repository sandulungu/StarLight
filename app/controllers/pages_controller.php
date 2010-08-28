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
        $options = array();
        $this->set('pages', $this->Page->find('all', $options));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
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
            $this->data = $this->Page->read(null, $this->id);
        }
    }

    public function admin_delete($id) {
        $this->Page->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

}
