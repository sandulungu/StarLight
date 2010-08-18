<?php

/**
 *
 * @property-read NavigationLink $NavigationLink
 */
class NavigationLinksController extends AppController {

    public function admin_index() {
        $this->set('navigation_links', $this->NavigationLink->find('all'));
        $this->set('title', __t('Navigation'));
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        if (!empty($this->params['named']['node'])) {
            $nodeId = $this->params['named']['node'];
            $node = $this->NavigationLink->Node->read(null, $nodeId);
            if ($node) {
                // set link title to node title
                $locales = SlConfigure::read('I18n.locales');
                foreach ($locales as $locale) {
                    $this->data['NavigationLink']['title' . $locale] =
                        $node["Node"]['short_title_' . $locale] ?
                            $node["Node"]['short_title_' . $locale] :
                            $node["Node"]['title_' . $locale];
                }

                $this->data['NavigationLink']['node_id'] = $nodeId;
                $this->data['NavigationLink']['url'] = SlNode::url($node, array('base' => false, 'lang' => false));
            }
        }

        if (!empty($this->params['named']['parent'])) {
            $this->data['NavigationLink']['parent_id'] = $this->params['named']['parent'];
        }
        $this->set('parents', $this->NavigationLink->find('treelist'));

        if ($this->data) {
            if ($this->NavigationLink->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->NavigationLink->read();
        }

        $this->set('title', __t(!$this->id ? 'Add navigation link' : 'Edit navigation link "{$name}"', array('name' => h($this->data['NavigationLink']['title']))));
    }

    public function admin_delete($id) {
        $this->NavigationLink->id = $id;
        $this->NavigationLink->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }
}
