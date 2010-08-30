<?php

/**
 *
 * @property-read AuthUser $AuthUser
 */
class AuthUsersController extends AppController {

    public function index() {
        $id = SlAuth::user('id');
        if (!$id) {
            $this->cakeError('error403');
        }
        $this->AuthUser->id = $id;
        
        if (isset($this->data['AuthUser']['password'])) {
            if ($this->_passwordMatch()) {
                if ($this->AuthUser->saveField('password', SlAuth::password($this->data['AuthUser']['password']), true)) {
                    $this->Session->setFlash(__t('Password changed'), array('class' => 'success'));
                }
            }
        }
        
        elseif (isset($this->data['AuthUser']['email'])) {
            // email validation
        }

        else {
            // security
            unset($this->data['AuthUser']['id']);
            unset($this->data['AuthUser']['password']);
            unset($this->data['AuthUser']['email']);

            $this->AuthUser->save($this->data);
        }
        
        $this->set('user', $user = $this->AuthUser->read());

        $this->set('title', __t('Welcome, {$name}', array('name' => h($user['AuthUser']['fullname']))));
    }

    public function login() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->AuthUser;

        $this->set('title', __t('Login'));

        if (!empty($this->data['AuthUser']['username'])) {
            if (SlAuth::login(
                    $this->data['AuthUser']['username'],
                    $this->data['AuthUser']['password'],
                    array('remember' => $this->data['AuthUser']['remember'])
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
        $this->_admin_index();
    }

    public function admin_login() {
        $this->login();
        $this->render('login');
    }

    public function admin_logout() {
        $this->logout();
    }

    public function admin_add() {
        $this->_admin_add();
    }

    public function admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';

        if ($this->data) {
            if ($this->_passwordMatch()) {
                $data = $this->data;
                if ($data['AuthUser']['password']) {
                    $data['AuthUser']['password'] = SlAuth::password($data['AuthUser']['password']);
                } else {
                    unset($data['AuthUser']['password']);
                }

                if ($this->AuthUser->saveAll($data)) {
                    $this->redirect(array('action' => 'index'));
                }
            }
        } elseif($this->id) {
            $this->data = $this->AuthUser->read(null, $this->id);
            if (empty($this->data)) {
                $this->cakeError();
            }
            unset($this->data['AuthUser']['password']);
        }
        $this->_admin_edit();

        $this->set('authGroups', $this->AuthUser->AuthGroup->find('list'));
    }

    public function admin_delete() {
        $this->_admin_delete();
    }

    protected function _passwordMatch() {
        $success = $this->data['AuthUser']['password'] == $this->data['AuthUser']['confirm_password'];
        if (!$success) {
            $this->AuthUser->invalidate('password', __t('Passwords do not match'));
        }
        return $success;
    }
    
}
