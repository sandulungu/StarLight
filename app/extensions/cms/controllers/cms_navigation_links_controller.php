<?php

/**
 *
 * @property-read CmsNavigationLink $CmsNavigationLink
 */
class CmsNavigationLinksController extends AppController {

    public function admin_index() {
        $this->_admin_index();
    }

    public function admin_edit() {
        $this->_admin_edit();
        $this->set('parents', $this->CmsNavigationLink->find('treelist', array('conditions' => array('CmsNavigationLink.id !=' => $this->id))));
    }

    public function admin_delete() {
        $this->_admin_delete();
    }

    public function admin_add() {
        $this->admin_edit();

        if (empty($this->data) && !empty($this->params['named']['cms_node_id'])) {
            $nodeId = $this->params['named']['node'];
            $node = $this->CmsNavigationLink->CmsNode->read(null, $nodeId);
            if ($node) {
                // set link title to node title
                $locales = SlConfigure::read('I18n.locales');
                foreach ($locales as $locale) {
                    $this->data['CmsNavigationLink']['title_' . $locale] =
                        $node["CmsNode"]['short_title_' . $locale] ?
                            $node["CmsNode"]['short_title_' . $locale] :
                            $node["CmsNode"]['title_' . $locale];
                }

                $this->data['CmsNavigationLink']['cms_node_id'] = $nodeId;
                $this->data['CmsNavigationLink']['url'] = SlNode::url($node, array('base' => false, 'lang' => false));
            }
        }

        $this->render('admin_edit');
    }
}
