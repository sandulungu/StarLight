<?php

class CmsController extends AppController {

    public $uses = array();

    public function index() {
        $this->set('title', __t('StarLight CMS: Welcome to your site'));
    }

    public function admin_index() {
        $this->set('title', __t('StarLight CMS Dashboard'));
    }

}
