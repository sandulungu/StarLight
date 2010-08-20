<?php

/**
 *
 * @property-read CmsBlock $CmsBlock
 */
class CmsBlocksController extends AppController {

    public function admin_index() {
        $this->set('blocks', $this->CmsBlock->find('all'));
        $this->set('title', __t('Blocks'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->CmsBlock;

        if ($this->data) {
            if ($this->CmsBlock->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsBlock->read();
        }

        if (!empty($this->params['named']['node'])) {
            $nodeId = $this->params['named']['node'];
            $node = $this->CmsBlock->CmsNode->read(null, $nodeId);
            if ($node) {
                // set link title to node title
                $locales = SlConfigure::read('I18n.locales');
                foreach ($locales as $locale) {
                    $this->data['CmsBlock']['title' . $locale] =
                        $node["CmsNode"]['short_title_' . $locale] ?
                            $node["CmsNode"]['short_title_' . $locale] :
                            $node["CmsNode"]['title_' . $locale];
                }

                $this->data['CmsBlock']['cms_node_id'] = $nodeId;
                $this->data['CmsBlock']['url'] = SlNode::url($node, array('base' => false, 'slug' => false, 'lang' => false));
            }
        }

        $this->set('title', __t(!$this->id ? 'Add block' : 'Edit block'));
    }

    public function admin_delete($id) {
        $this->CmsBlock->id = $id;
        $this->CmsBlock->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
