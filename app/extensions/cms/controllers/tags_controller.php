<?php

/**
 *
 * @property-read Tag $Tag
 */
class TagsController extends AppController {

    public function admin_index() {
        $this->set('tagTypes', $this->Tag->TagType->find('list', array(
            'fields' => array('TagType.id', 'TagType.name'),
        )));
        $this->set('tags', $this->Tag->find('list', array(
            'fields' => array('Tag.id', 'Tag.name', 'Tag.tag_type_id'),
        )));
    }

    public function admin_edit($id = null) {
        $this->helpers[] = 'JsValidate.Validation';
        $this->Tag;
        
        if ($this->data) {
            if ($this->Tag->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->data = $this->Tag->read();
        }
    }

    public function admin_delete($id) {
        $this->Tag->id = $id;
        $this->Tag->delete();
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->set('tagTypes', $this->Tag->TagType->find('list'));
        
        if ($this->data) {
            $tags = Set::normalize($this->data['Tag']['name'], false);
            if (count($tags) > 1) {
                $data = array();
                foreach ($tags as $tag) {
                    if (!trim($tag)) {
                        continue;
                    }
                    $item = $this->data;
                    $item['Tag']['name'] = trim($tag);
                    $data[] = $item;
                }
            } else {
                $data = $this->data;
            }
            if ($this->Tag->saveAll($data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
    }
}
