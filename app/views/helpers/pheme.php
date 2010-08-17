<?php

/**
 * A cakeish flavour for Pheme parser
 */
class PhemeHelper extends AppHelper {
    public function __call($method, $args) {
        return call_user_func_array(array('Pheme', $method), $args);
    }
}