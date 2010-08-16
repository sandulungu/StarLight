<?php

/**
 * Simplified SessionHelper
 */
class SessionHelper extends AppHelper {

    public function read($name = '') {
        return SlSession::read($name);
    }

    public function check($name) {
        return SlSession::read($name) !== null;
    }

    public function write($name, $value) {
        SlSession::write($name, $value);
    }

    public function delete($name) {
        return SlSession::delete($name);
    }

    public function del($name) {
        return SlSession::delete($name);
    }

    /**
     * For compatibility with Cake
     */
    function flash($key = 'flash') {
        $out = false;
        $flash = SlSession::read('Message.' . $key);

        if ($flash['element'] == 'default') {
            if (!empty($flash['params']['class'])) {
                $class = $flash['params']['class'];
            } else {
                $class = 'message';
            }
            $out = '<div id="' . $key . 'Message" class="' . $class . '">' . $flash['message'] . '</div>';
        } elseif ($flash['element'] == '' || $flash['element'] == null) {
            $out = $flash['message'];
        } else {
            $view =& ClassRegistry::getObject('view');
            $tmpVars = $flash['params'];
            $tmpVars['message'] = $flash['message'];
            $out = $view->element($flash['element'], $tmpVars);
        }

        SlSession::delete('Message.' . $key);
        return $out;
	}
}
