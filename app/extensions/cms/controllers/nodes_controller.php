<?php

/**
 *
 * @property-read Node $Node
 */
class NodesController extends AppController {

    public function index() {
        /*$query = array(
            'contain' => array(
                'Tag',
                'User',
                'Borrow' => array('User'),
            ),
        );

        if (isset($this->params['named']['user'])) {
            $userId = $this->params['named']['user'];
            $this->set('user', $this->Node->User->field('User.fullname', array('User.id' => $userId)));

            $query['conditions']['User.id'] = $userId;
        }

        if (isset($this->params['named']['tag'])) {
            $tagId = $this->params['named']['tag'];
            $this->set('tag', $this->Node->Tag->field('Tag.name', array('Tag.id' => $tagId)));

            $query['conditions']['TagFilter.id'] = $tagId;
            $query['link']['BcNodesTags']['TagFilter'] = array(
                'class'	=> 'Tag',
                'conditions' => 'TagFilter.id = BcNodesTags.tag_id', // Join condition (LEFT JOIN x ON ...)
                'fields' => array('TagFilter.id')
			);
        }

        if (!empty($this->params['url']['q'])) {
            $q = $this->params['url']['q'];
            $query['conditions']['Node.title LIKE'] = "%$q%";
        }
        
        $this->set('books', $this->Node->find('all', $query));
        $this->set('title_for_layout', $q ? "Cautare cărți cu titlul \"$q\"" : 'Cărți');*/
    }

    public function admin_index($id = null) {
        $query = array(
            'contain' => array(
                'Tag',
                'User',
                'Borrow' => array('User'),
            ),
        );

        if (isset($this->params['named']['tag'])) {
            $tagId = $this->params['named']['tag'];
            $this->set('tag', $this->Node->Tag->field('Tag.name', array('Tag.id' => $tagId)));

            $query['conditions']['TagFilter.id'] = $tagId;
            $query['link']['BcNodesTags']['TagFilter'] = array(
                'class'	=> 'Tag',
                'conditions' => 'TagFilter.id = BcNodesTags.tag_id', // Join condition (LEFT JOIN x ON ...)
                'fields' => array('TagFilter.id')
			);
        }

        if (isset($this->params['named']['user'])) {
            $userId = $this->params['named']['user'];
            $this->set('user', $this->Node->User->field('User.fullname', array('User.id' => $userId)));

            $query['conditions']['User.id'] = $userId;
        }

        if (isset($id)) {
            $query['conditions']['Node.id'] = $id;
        }

        $this->set('books', $this->Node->find('all', $query));
    }

    public function admin_edit($id = null) {
        $this->helpers[] = 'JsValidate.Validation';
        
        $this->set('users', $this->Node->User->find('list'));
        $this->set('tags', $this->Node->Tag->find('list', array(
            'fields' => array('Tag.id', 'Tag.name', 'TagType.name'),
            'recursive' => 0,
        )));
        
        if ($this->data) {
            if ($this->Node->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->data = $this->Node->read();
        }
    }

    public function admin_delete($id) {
        $this->Node->id = $id;
        $this->Node->delete();
        $this->redirect(array('action' => 'index'));
    }

    public function admin_add() {
        $this->helpers[] = 'JsValidate.Validation';
        
        $this->set('users', $this->Node->User->find('list'));
        $this->set('tags', $this->Node->Tag->find('list', array(
            'fields' => array('Tag.id', 'Tag.name', 'TagType.name'),
            'recursive' => 0,
        )));

        if ($this->data) {
            if ($this->Node->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
    }
}
