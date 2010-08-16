<?php

/**
 *
 * @property-read TagTypeType $TagTypeType
 */
class TagTypesController extends AppController {

    public function admin_edit($id = null) {
        $this->helpers[] = 'JsValidate.Validation';
        $this->TagType;
        
        if ($this->data) {
            if ($this->TagType->saveAll($this->data)) {
                $this->redirect(array('controller' => 'tags'));
            }
        } else {
            $this->data = $this->TagType->read();
        }
    }

    public function admin_delete($id) {
        $this->TagType->id = $id;
        $this->TagType->delete();
        $this->redirect(array('controller' => 'tags'));
    }

    public function admin_add() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->TagType;
        
        if ($this->data) {
            if ($this->data['TagType']['tag_names']) {
                $tags = Set::normalize(
                    r(array("\r", "\n"), '', $this->data['TagType']['tag_names']),
                    false
                );
                foreach ($tags as $tag) {
                    if (!trim($tag)) {
                        continue;
                    }
                    $this->data['Tag'][] = array('name' => trim($tag));
                }
            }
            if ($this->TagType->saveAll($this->data)) {
                $this->redirect(array('controller' => 'tags'));
            }
        }
    }
}
