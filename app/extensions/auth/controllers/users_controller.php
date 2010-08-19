<?php

/**
 *
 * @property-read User $User
 */
class UsersController extends AppController {

    public function index() {
        $id = SlAuth::user('id');
        if (!$id) {
            $this->cakeError('error403');
        }
        $this->User->id = $id;
        
        if (isset($this->data['User']['password'])) {
            if ($this->_passwordMatch()) {
                if ($this->User->saveField('password', SlAuth::password($this->data['User']['password']), true)) {
                    $this->Session->setFlash(__t('Password changed'), array('class' => 'success'));
                }
            }
        }
        
        elseif (isset($this->data['User']['email'])) {
            // email validation
        }

        else {
            // security
            unset($this->data['User']['id']);
            unset($this->data['User']['password']);
            unset($this->data['User']['email']);

            $this->User->save($this->data);
        }
        
        $this->set('user', $user = $this->User->read());

        $this->set('title', __t('Welcome, {$name}', array('name' => h($user['User']['fullname']))));
    }

    public function login() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->User;

        $this->set('title', __t('Login'));

        if (!empty($this->data['User']['username'])) {
            if (SlAuth::login(
                    $this->data['User']['username'],
                    $this->data['User']['password'],
                    array('remember' => $this->data['User']['remember'])
                )) {
                $key = empty($this->params['admin']) ? 'Auth.url.afterLogin' : 'Auth.url.afterAdminLogin';
                $url = SlConfigure::read2($key);
                SlSession::delete($key);
                $this->redirect($url);
            } else {
                $this->Session->setFlash(__t('Login error. Check username and password'));
            }
        }
    }

    public function logout() {
        SlAuth::logout();
        $this->redirect(SlConfigure::read('Auth.url.afterLogout'));
    }

    public function admin_index() {
        $this->set('users', $this->User->find('all'));
        $this->set('title', __t('Users'));
    }

    public function admin_login() {
        $this->login();
        $this->render('login');
    }

    public function admin_logout() {
        $this->logout();
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        $this->set('groups', $this->User->Group->find('list'));

        if ($this->data) {
            if ($this->_passwordMatch()) {
                $data = $this->data;
                if ($data['User']['password']) {
                    $data['User']['password'] = SlAuth::password($data['User']['password']);
                } else {
                    unset($data['User']['password']);
                }

                if ($this->User->saveAll($data)) {
                    $this->redirect(array('action' => 'index'));
                }
            }
        } elseif($this->id) {
            $this->data = $this->User->read(null, $this->id);
            unset($this->data['User']['password']);
        }

        $this->set('title', !$this->id ? __t('Add user account') : __t('Edit user account'));
    }

    public function admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
    }

    public function admin_delete($id) {
        $this->User->id = $id;
        $this->User->delete($id, true);
        $this->redirect(array('action' => 'index'));
    }

    protected function _passwordMatch() {
        $success = $this->data['User']['password'] == $this->data['User']['confirm_password'];
        if (!$success) {
            $this->User->invalidate('password', __t('Passwords do not match'));
        }
        return $success;
    }
    
}
