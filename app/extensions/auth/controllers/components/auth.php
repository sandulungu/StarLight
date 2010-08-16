<?php

class AuthComponent extends SlComponent {

    public function startup($controller) {
        if (!SlAuth::isAuthorized('action' . Inflector::camelize($controller->action))) {
            $controller->cakeError('error403');
        }
    }
    
}
