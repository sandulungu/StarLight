<?php

/**
 * Simplified SessionComponent
 */
class CookieComponent extends SlComponent {

    public function read($name = '') {
        return SlCookie::read($name);
    }

    public function check($name) {
        return SlCookie::read($name) !== null;
    }

    public function write($name, $value, $encrypt = true, $expires = null) {
        SlCookie::write($name, $value, $encrypt, $expires);
    }

    public function delete($name) {
        return SlCookie::delete($name);
    }

    public function del($name) {
        return SlCookie::delete($name);
    }

    public function destroy() {
        return SlCookie::clear();
    }

    public function initialize() {
    }

    public function startup() {
    }

    public function type() {
        trigger_error('Not implemented', E_USER_WARNING);
    }

}
