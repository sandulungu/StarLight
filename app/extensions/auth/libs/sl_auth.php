<?php

class SlAuth {
    static public function isAuthorized($what, $options = array()) {
        
        // root user
        if (self::user('id') == 1) {
            return true;
        }

        if (is_string($options)) {
            $options = array('default' => $options);
        }
        $options += array(
            'default' => 'deny',
            'who' => self::user('roles'),
        );

        foreach ($options['who'] as $role) {
            $result = SlConfigure::read2("Auth.acl.$role.$what");
            if ($result !== null) {
                return $result === true || $result === 'allow';
            }
        }
        return $options['default'] === true || $options['default'] === 'allow';
    }

    static public function isAdministrator() {
        return in_array('Group1', self::user('roles'));
    }

    static public function isCollaborator() {
        return in_array('Group2', self::user('roles'));
    }

    static public function user($field = null) {
        $currentUser = SlSession::read('Auth.user');
        if (empty($currentUser)) {
            self::login();
            $currentUser = SlSession::read('Auth.user');
        }
        return $field ? (isset($currentUser[$field]) ? $currentUser[$field] : null) : $currentUser;
    }

    static protected function _login($username, $password) {
        App::import('Core', 'ClassRegistry');
        $user = ClassRegistry::init('Auth.AuthUser')->find('first', array(
            'conditions' => array(
                'AuthUser.active' => true,
                'or' => array('AuthUser.username' => $username, 'AuthUser.email' => $username),
            ),
        ));
        if (!$user) {
            return;
        }
        if (self::password($password, array(), $user['AuthUser']['password'])) {
            return false;
        }
        
        $roles = array("AuthUser{$user['AuthUser']['id']}");
        foreach ($user['AuthGroup'] as $group) {
            $roles[] = "Group{$group['id']}";
            $roles[] = $group['name'];
        }
        $roles[] = "users";
        $roles[] = "everyone";
        $user['AuthUser']['roles'] = $roles;

        SlSession::write('Auth.user', $user['AuthUser']);
        SlSession::write('Auth.groups', $user['AuthGroup']);
        return true;
    }

    /**
     *
     * @param string $username
     * @param string $password
     * @param array $options
     * @return mixed True = logged in, false = invalid password, null = not logged in
     */
    static public function login($username = null, $password = null, $options = array()) {
        $options += array(
            'remember' => true,
        );
        $remember = SlConfigure::read('Auth.options.remember');

        if ($username) {
            // normal login
            $success = self::_login($username, $password);
            if ($success && $options['remember'] && $remember) {
                SlCookie::write('Auth.user', compact('username', 'password'), true, $remember);
            }
            return $success;
        }
        else {
            // autologin
            $user = SlCookie::read('Auth.user');
            if ($user) {
                if (self::_login($user['username'], $user['password'])) {
                    SlConfigure::setCollections();
                }
            }
        }

        self::logout();
    }

    static public function logout() {
        $guest = SlConfigure::read('Auth.guest');
        $guest['fullname'] = __t($guest['fullname']);
        //$guest['roles'] = Set::normalize($guest['roles']);

        SlSession::write('Auth.user', $guest);
        SlSession::write('Auth.groups', array());
    }

    static public function loggedIn() {
        return (bool)self::user('id');
    }

    static public function password($password, $options = array(), $match = null) {
        $options += array(
            'hash' => 'sha1',
            'salt' => Configure::read('Security.salt'),
        );

        if ($match) {
            if (preg_match('/^(md5|sha):[a-f0-9]+$/', $match)) {
                list($options['hash']) = explode(':', $match);
            } else {
                $options['hash'] = false;
            }
        }

        $passAndSalt = $password;
        if ($options['salt']) {
            $passAndSalt .= $options['salt'];
        }

        switch ($options['hash']) {
            case 'md5':
                $password = "md5:" . md5($passAndSalt);
                break;

            case 'sha1':
                $password = "sha1:" . sha1($passAndSalt);
                break;

            case 'sha256':
                $password = "sha256:" . bin2hex(mhash(MHASH_SHA256, $passAndSalt));
                break;
        }
        return $match ? $password == $match : $password;
    }
}
