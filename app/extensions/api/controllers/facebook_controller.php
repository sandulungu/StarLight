<?php

/**
 * A dummy controller to quickly test new features
 */
class FacebookController extends AppController {

    public $uses = array();

    public $components = array('Api.Facebook');

    public function index() {
        $this->redirect('http://apps.facebook.com/'.SlConfigure::read('Api.facebook.canvasPageName').'/');
    }

    public function login() {
        $this->Facebook->getSession(array("publish_stream", "offline_access", "email"));
        $this->redirect(SlConfigure::read('Api.facebook.oauthSuccess'));
    }

    public function oauth() {
        if (isset($this->params['url']['code'])) {
            $code = $this->params['url']['code'];
        }
        SlSession::write('Api.facebook.accessToken', r('access_token=', '',
            $this->Facebook->graph("oauth/access_token", array(
                'client_id' => SlConfigure::read('Api.facebook.appId'),
                'redirect_uri' => Sl::url(true),
                'client_secret' => SlConfigure::read('Api.facebook.secret'),
                'code' => $code,
                'decode' => false,
            ))
        ));
        $this->redirect(SlConfigure::read('Api.facebook.oauthSuccess'));
    }
}
