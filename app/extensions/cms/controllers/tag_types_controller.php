<?php

/**
 *
 * @property-read TagType $TagType
 */
class TagTypesController extends AppController {

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->TagType;

        if ($this->data) {
            if ($this->TagType->saveAll($this->data)) {
                $this->redirect(!$this->id ?
                    array('controller' => 'tags', 'action' => 'add', 'tag_type' => $this->Tag->id) :
                    array('action' => 'index')
                );
            }
        }
        elseif ($this->id) {
            $this->data = $this->TagType->read();
        }

        $this->set('title', __t(!$this->id ? 'Add tag group' : 'Edit "{$name}" tag group', array('name' => h($this->data['TagType']['name']))));
    }

    public function admin_delete($id) {
        $this->TagType->id = $id;
        $this->TagType->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
