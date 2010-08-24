<?php

/**
 *
 * @property-read CmsBlock $CmsBlock
 */
class CmsBlocksController extends AppController {

    public function admin_index() {
        $options = array();

        if (!empty($this->params['named']['node'])) {
            $this->set('nodeId', $nodeId = $this->params['named']['node']);
            $options['conditions']['CmsBlock.cms_node_id'] = $nodeId;
        }

        $this->set('blocks', $this->CmsBlock->find('all', $options));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->CmsBlock;

        if ($this->data) {
            if ($this->CmsBlock->saveAll($this->data)) {
                $nodeId = $this->CmsBlock->field('cms_node_id');
                $this->redirect(
                    $nodeId ?
                    SlNode::url($nodeId, array('admin' => true, 'route' => false)) :
                    array('action' => 'index')
                );
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
                    $this->data['CmsBlock']['title_' . $locale] =
                        $node["CmsNode"]['short_title_' . $locale] ?
                            $node["CmsNode"]['short_title_' . $locale] :
                            $node["CmsNode"]['title_' . $locale];
                }

                $this->data['CmsBlock']['cms_node_id'] = $nodeId;
                $this->data['CmsBlock']['url'] = SlNode::url($node, array('base' => false, 'slug' => false, 'lang' => false));
            }
        }
    }

    public function admin_delete($id) {
        $this->CmsBlock->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
