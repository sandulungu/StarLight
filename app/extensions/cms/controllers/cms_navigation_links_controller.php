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

        $this->set('navigationLinks', $this->CmsNavigationLink->find('all', $options));
        $this->set('title', __t('Navigation'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        if ($this->data) {
            if ($this->CmsNavigationLink->saveAll($this->data)) {
                $nodeId = $this->CmsNavigationLink->field('cms_node_id');
                $this->redirect(
                    $nodeId ?
                    SlNode::url($nodeId, array('admin' => true, 'route' => false)) :
                    array('action' => 'index')
                );
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
                    $this->data['CmsNavigationLink']['title_' . $locale] =
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
        $this->set('parents', $this->CmsNavigationLink->find('treelist', array('conditions' => array('CmsNavigationLink.id !=' => $this->id))));
    }

    public function admin_delete($id) {
        $this->CmsNavigationLink->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
