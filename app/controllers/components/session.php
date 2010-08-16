<?php

/**
 * Simplified SessionComponent
 */
class SessionComponent extends SlComponent {

    public function read($name = '') {
        return SlSession::read($name);
    }

    public function check($name) {
        return SlSession::read($name) !== null;
    }

    public function write($name, $value) {
        SlSession::write($name, $value);
    }

	function setFlash($message, $layout = 'default', $params = array(), $key = 'flash') {
        if (is_array($layout)) {
            $key = $params;
            $params = $layout;
        }
    	SlSession::write('Message.' . $key, compact('message', 'layout', 'params'));
	}
    
    public function delete($name) {
        return SlSession::delete($name);
    }

    public function del($name) {
        return SlSession::delete($name);
    }

    public function initialize() {
    }

    public function startup() {
    }

    public function activate() {
        SlSession::started();
    }

    public function start() {
        return SlSession::started();
    }

    public function started() {
        return SlSession::started();
    }

    public function id($id = null) {
        return session_id($id);
    }

    public function error() {
        trigger_error('Not implemented', E_USER_WARNING);
    }

    public function destroy() {
        trigger_error('Not implemented', E_USER_WARNING);
    }

    public function renew() {
        trigger_error('Not implemented', E_USER_WARNING);
    }

    public function ignore() {
        trigger_error('Not implemented', E_USER_WARNING);
    }

    public function watch() {
        trigger_error('Not implemented', E_USER_WARNING);
    }

}
