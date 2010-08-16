<?php

/**
 * Simplified analog to CakeSession
 */

class SlSession {
    static public $data = null;

    static protected $_started = null;

    static public function started() {
        if (self::$_started === null) {
            $options = SlConfigure::read('Sl.session');
            
            session_write_close();
            foreach ($options as $option => $value) {
                ini_set("session.$option", $value);
            }

            self::$_started = session_start();
            if (!self::$_started) {
                return false;
            }
            self::$data =& $_SESSION;

            // prevent proxy-jumping and session hijacks
            $ip = self::read('Security.remoteAddr');
            if ($ip && env('REMOTE_ADDR') != $ip) {
                session_write_close();
                session_regenerate_id(true);
                self::$_started = session_start();
            }
            if (empty($ip)) {
                self::write('Security.remoteAddr', env('REMOTE_ADDR'));
            }
        }
        return self::$_started;
    }

    static public function read($name = '') {
        if (!self::started()) {
            return;
        }

        if (empty($name)) {
            return self::$data;
        }
        $addr =& self::$data;

        $keys = explode('.', $name);
        foreach ($keys as $key) {
            if (!is_array($addr) || !isset($addr[$key])) {
                return null;
            }
            $addr =& $addr[$key];
        }
        return $addr;
    }

    static public function write($name, $value) {
        if (!self::started()) {
            return;
        }

        $addr =& self::$data;

        $keys = explode('.', $name);
        foreach ($keys as $key) {
            if (!is_array($addr)) {
                $addr = array();
            }
            if (!isset($addr[$key])) {
                $addr[$key] = array();
            }
            $addr =& $addr[$key];
        }
        $addr = $value;
    }

    static public function delete($name) {
        if (!self::started()) {
            return;
        }

        $addr =& self::$data;

        $keys = explode('.', $name);
        foreach ($keys as $key) {
            if (!is_array($addr) || !isset($addr[$key])) {
                return false;
            }
            $lastAddr =& $addr;
            $addr =& $addr[$key];
        }
        unset($lastAddr[$key]);
        return true;
    }

    static public function clear() {
        if (!self::started()) {
            return;
        }

        session_unset();
    }

}
