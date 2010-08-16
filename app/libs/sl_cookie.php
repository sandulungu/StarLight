<?php

/**
 * Simplified, more flexible analog to Cake CookieComponent
 */

class SlCookie {
    static protected $_key;

    static protected $_cookieName;

    static protected $_cookies = array();

    static protected $_decrypted = false;

    static public function ready() {
        if (self::$_decrypted === false) {
            self::$_cookieName = SlConfigure::read('Sl.cookie.name');
            self::$_key = Configure::read('Security.salt');

            // load cookies
            if (!empty($_COOKIE[self::$_cookieName])) {
                foreach ($_COOKIE[self::$_cookieName] as $name => $value) {
                    
                    // use Cakeish encryption
                    if ($value{0} === '?') {
                        App::import('core', 'security');
                        $value = substr($value, 1);
                        $value = Security::cipher(base64_decode($value), self::$_key);
                    } else {
                        $value = base64_decode($value);
                    }
                    
                    $value = unserialize($value);
                    self::$_cookies[] = $name;
                    SlConfigure::write($name, $value, false, 'cookie');
                }
            }
            
            self::$_decrypted = true;
        }
        return self::$_decrypted;
    }

    static public function read($name = null) {
        self::ready();
        if (empty($name)) {
            return self::$_cookies;
        }
        return SlConfigure::read($name, 'cookie');
    }

    static public function write($name, $value = null, $encrypt = true, $expires = null, $path = null, $domain = null, $secure = null) {
        self::ready();
        SlConfigure::write($name, $value, false, 'cookie');
        
        self::$_cookies[] = $name;
        self::$_cookies = array_unique(self::$_cookies);

        if (empty($path)) {
            $path = SlConfigure::read('Sl.cookie.path');
        }
        if ($domain === null) {
            $domain = SlConfigure::read('Sl.cookie.domain');
        }
        if ($secure === null) {
            $secure = SlConfigure::read('Sl.cookie.secure');
        }
        
        $now = time();
        if (is_int($expires) || is_numeric($expires)) {
			$expires = $now + intval($expires);
		} 
        elseif (is_string($expires)) {
            $expires = strtotime($expires, $now);
        }

        $value = serialize($value);
        if ($encrypt) {
            App::import('core', 'security');
            $value = "?" .base64_encode(Security::cipher($value, self::$_key));
        } else {
            $value = base64_encode($value);
        }

        setcookie(self::$_cookieName . "[$name]", $value, $expires, $path, $domain, $secure);
    }

    static public function delete($name, $path = null, $domain = null, $secure = null) {
        self::ready();
        SlConfigure::delete($name, false, 'cookie');
        
        if (empty($path)) {
            $path = SlConfigure::read('Sl.cookie.path');
        }
        if ($domain === null) {
            $domain = SlConfigure::read('Sl.cookie.domain');
        }
        if ($secure === null) {
            $secure = SlConfigure::read('Sl.cookie.secure');
        }

        setcookie(self::$_cookieName . "[$name]", '', time() - 42000, $path, $domain, $secure);
    }

    static public function clear() {
        self::ready();

        foreach (self::$_cookies as $name) {
            self::delete($name);
        }
        self::$_cookies = array();
    }
}