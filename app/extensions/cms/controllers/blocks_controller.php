<?php

/**
 *
 * @property-read Block $Block
 */
class BlocksController extends AppController {

    public function admin_index() {
        $this->set('blocks', $this->Block->find('all'));
        $this->set('title', __t('Blocks'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->Block;

        if (!empty($this->params['named']['node'])) {
            $nodeId = $this->params['named']['node'];
            $node = $this->Block->Node->read(null, $nodeId);
            if ($node) {
                // set link title to node title
                $locales = SlConfigure::read('I18n.locales');
                foreach ($locales as $locale) {
                    $this->data['Block']['title' . $locale] =
                        $node["Node"]['short_title_' . $locale] ?
                            $node["Node"]['short_title_' . $locale] :
                            $node["Node"]['title_' . $locale];
                }

                $this->data['Block']['node_id'] = $nodeId;
                $this->data['Block']['url'] = SlNode::url($node, array('base' => false, 'slug' => false, 'lang' => false));
            }
        }

        if ($this->data) {
            if ($this->Block->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->Block->read();
        }

        $this->set('title', __t(!$this->id ? 'Add block' : 'Edit block "{$name}"', array('name' => h($this->data['Block']['title']))));
    }

    public function admin_delete($id) {
        $this->Block->id = $id;
        $this->Block->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
