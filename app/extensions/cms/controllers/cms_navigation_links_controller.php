<?php

/**
 *
 * @property-read CmsNavigationLink $CmsNavigationLink
 */
class CmsNavigationLinksController extends AppController {

    public function admin_index() {
        $options = array();

        if (!empty($this->params['named']['node'])) {
            $this->set('nodeId', $nodeId = $this->params['named']['node']);
            $options['conditions']['CmsNavigationLink.cms_node_id'] = $nodeId;
        }

        $this->set('navigationLinks', $this->CmsNavigationLink->find('all'));
        $this->set('title', __t('Navigation'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        if ($this->data) {
            if ($this->CmsNavigationLink->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->CmsNavigationLink->read();
        }

        if (!empty($this->params['named']['node'])) {
            $nodeId = $this->params['named']['node'];
            $node = $this->CmsNavigationLink->CmsNode->read(null, $nodeId);
            if ($node) {
                // set link title to node title
                $locales = SlConfigure::read('I18n.locales');
                foreach ($locales as $locale) {
                    $this->data['CmsNavigationLink']['title' . $locale] =
                        $node["CmsNode"]['short_title_' . $locale] ?
                            $node["CmsNode"]['short_title_' . $locale] :
                            $node["CmsNode"]['title_' . $locale];
                }

                $this->data['CmsNavigationLink']['cms_node_id'] = $nodeId;
                $this->data['CmsNavigationLink']['url'] = SlNode::url($node, array('base' => false, 'lang' => false));
            }
        }

        if (!empty($this->params['named']['parent'])) {
            $this->data['CmsNavigationLink']['parent_id'] = $this->params['named']['parent'];
        }
        $this->set('parents', $this->CmsNavigationLink->find('treelist'));
    }

    public function admin_delete($id) {
        $this->CmsNavigationLink->id = $id;
        $this->CmsNavigationLink->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
