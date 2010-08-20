<?php

class InstallController extends AppController {
    public $uses = array();

    public function beforeFilter() {
        // security check
        if (!Configure::read() && SlConfigure::read('Mirror.version')) {
            if ($this->action == 'migrate') {
                return;
            }
            if (SlExtensions::loaded('Auth') && SLAuth::user('id') == 1) {
                return;
            }
            $this->cakeError();
        }
        
        parent::beforeFilter();
    }

    public function index() {
        $this->set('title', __t('StarLight installation: Hello and welcome!'));
    }

    public function db() {
        $this->set('title', __t('StarLight installation: Database setup'));

        if ($this->data) {
            $this->data['persistent'] = (int)$this->data['persistent'];
            SlConfigure::write('Db.default', $this->data, true);
        } else {
            $this->data = SlConfigure::read('Db.default');
            unset($this->data['password']);
        }

        App::import('Core', 'ConnectionManager');
        $db = @ConnectionManager::getDataSource('default');
        $this->set('connected', $db->isConnected());
    }

    protected function _getMigrationSql($oldSchema, $schema) {
        $db =& ConnectionManager::getDataSource($schema->connection);
        $tables = $db->listSources();

        $locales = SlConfigure::read('I18n.locales');
        $localesPreg = '/_' . implode('$|_', $locales) . '$/';

        $compare = $schema->compare($oldSchema, $schema);
        
        $instructions = array();
        foreach ($compare as $table => &$operations) {
            if (!in_array($db->config['prefix'] . $table, $tables)) {
                $instructions[] = array(
                    'sql' => $db->createSchema($schema, $table),
                    'event' => array('create' => $table)
                );
                continue;
            }
            foreach ($operations as $operation => &$fields) {
                if ($operation == 'drop') {
                    foreach ($fields as $field => $declaration) {
                        if (preg_match($localesPreg, $field)) {
                            unset($fields[$field]);
                        }
                    }
                }
                if (empty($fields)) {
                    unset($operations[$operation]);
                }
            }
            if (!empty($operations)) {
                $instructions[] = array(
                    'sql' => $db->alterSchema(array($table => $operations)),
                    'event' => array('update' => $table)
                );
            }
        }
        return $instructions;
    }

    public function db2() {
        $this->set('title', __t('StarLight installation: Database setup'));

        App::import('Core', 'CakeSchema');
        require CONFIGS . 'schema/sl.php';
        $schema = new SlSchema();

        $db =& ConnectionManager::getDataSource($schema->connection);
        $oldSchema = $schema->read(array('models' => false));

        $instructions = $this->_getMigrationSql($oldSchema, $schema);
        $sql = array();
        if ($instructions) {
            $success = true;
            foreach ($instructions as $i) {
                $sql[] = $i['sql'];
                $success = $db->execute($i['sql']) && $success;
                if ($success) {
                    $schema->after($i['event']);
                }
            }
            $this->set('success', $success);
        }
        $this->set('sql', $sql);
    }

    public function auth() {
        $this->helpers[] = 'JsValidate.Validation';
        
        $this->set('title', __t('StarLight installation: Administrator profile'));

        if (!SlExtensions::loaded('Auth')) {
            $this->Session->setFlash(__t('Auth extension is disabled. All security settings will be ignored.'));
            $this->redirect(array('action' => 'done'));
        }

        $this->loadModel('Auth.AuthUser');
        $user = $this->AuthUser->read(null, 1);
        if ($user) {
            $this->Session->setFlash(
                __t('Administrator profile step skipped. A root user (<b>{$username}</b>) is already registered.', array('username' => $user['AuthUser']['username'])),
                array('class' => 'message')
            );
            $this->redirect(array('action' => 'done'));
        }

        if ($this->data) {
            $success = $this->data['AuthUser']['password'] == $this->data['AuthUser']['confirm_password'];
            if (!$success) {
                $this->AuthUser->invalidate('password', __t('Passwords do not match'));
                return;
            }

            $this->data['Group']['Group'] = array(1, 2);
            if ($this->AuthUser->saveAll($this->data)) {
                SlAuth::login($this->data['AuthUser']['username'], $this->data['AuthUser']['password']);
                $this->redirect(array('action' => 'done'));
            }
        }
    }

    public function done() {
        $this->set('title', __t('StarLight installation: Finished successfully'));
    }

    public function migrate() {
        $this->set('title', __t('StarLight installation: Database upgrade'));

        $form = Sl::version('core');
        $to = SlConfigure::read('Sl.version');
        Sl::version('core', $to);
    }
}