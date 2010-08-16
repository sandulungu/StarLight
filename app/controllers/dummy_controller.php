<?php

/**
 * A dummy controller to quickly test new features
 */
class DummyController extends AppController {

    public $components = array('CsvIo', 'Api.hqSms', 'Api.Facebook');

    public $helpers = array('JsValidate.Validation');

    public $uses = array();

    public function beforeFilter() {
        if (!Configure::read()) {
            $this->cakeError();
        }
    }

    public function index() {
    }

    public function admin_index() {
    }
}
