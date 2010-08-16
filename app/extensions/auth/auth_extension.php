<?php

App::import('lib', 'auth.sl_auth');

class AuthExtension extends SlExtension {

    /**
     *
     * @param AppController $controller
     */
    public function constructClasses($controller) {
        $controller->components[] = 'Auth.Auth';
    }
}
