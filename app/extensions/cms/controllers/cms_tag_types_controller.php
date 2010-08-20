<?php

/**
 *
 * @property-read CmsTagType $CmsTagType
 */
class CmsTagTypesController extends AppController {

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->CmsTagType;

        if ($this->data) {
            if ($this->CmsTagType->saveAll($this->data)) {
                $this->redirect(!$this->id ?
                    array('controller' => 'tags', 'action' => 'add', 'tag_type' => $this->CmsTag->id) :
                    array('action' => 'index')
                );
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsTagType->read();
        }

        $this->set('title', __t(!$this->id ? 'Add tag group' : 'Edit "{$name}" tag group', array('name' => h($this->data['CmsTagType']['name']))));
    }

    public function admin_delete($id) {
        $this->CmsTagType->id = $id;
        $this->CmsTagType->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
