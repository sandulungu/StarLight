<?php

/**
 *
 * @property-read Group $Group
 */
class GroupsController extends AppController {

    public function admin_index() {
        $this->set('groups', $this->Group->find('all'));
        $this->set('title', __t('User groups'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->Group;

        if ($this->data) {
            if ($this->Group->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        } elseif($this->id) {
            $this->data = $this->Group->read(null, $this->id);
        }

        $this->set('title', !$this->id ?
            __t('Add group') :
            __t('Edit group "{$name}"', array('name' => h($this->data['Group']['name'])))
        );
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }

    public function admin_delete($id) {
        $this->Group->id = $id;
        $this->Group->delete();
        $this->redirect(array('action' => 'index'));
    }
    
}
