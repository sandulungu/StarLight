<?php

/**
 *
 * @property-read CmsTagCategory $CmsTagCategory
 */
class CmsTagCategoriesController extends AppController {

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->CmsTagCategory;

        if ($this->data) {
            if ($this->CmsTagCategory->saveAll($this->data)) {
                $this->redirect($this->action == 'admin_add' ?
                    array('controller' => 'cms_tags', 'action' => 'add', 'tag_category' => $this->CmsTagCategory->id) :
                    array('controller' => 'cms_tags')
                );
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsTagCategory->read();
        }
    }

    public function admin_delete($id) {
        $this->CmsTagCategory->delete($id, true);
        $this->redirect(array('controller' => 'cms_tags'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
