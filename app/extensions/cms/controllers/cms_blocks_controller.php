<?php

/**
 *
 * @property-read CmsBlock $CmsBlock
 */
class CmsBlocksController extends AppController {

    public function admin_index() {
        $this->_admin_index();
    }

    public function admin_edit() {
        $this->_admin_edit();
    }

    public function admin_delete() {
        $this->_admin_delete();
    }

    public function admin_add() {
        $this->admin_edit();

        if (empty($this->data) && !empty($this->params['named']['cms_node_id'])) {
            $nodeId = $this->params['named']['cms_node_id'];
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

        $this->render('admin_edit');
    }
}
