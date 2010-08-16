<?php

class FacebookComponent extends SlComponent {

    public $cookies = array();

    public $accessToken = false;

    public function graph($objectId = 'me', $params = array()) {
        if (empty($objectId)) {
            return;
        }
        $params += array(
            'decode' => true,
        );
        $decode = $params['decode'];
        unset($params['decode']);

        App::import('Core', 'HttpSocket');
        $socket = new HttpSocket();
        $protocol = SlConfigure::read('Api.facebook.secure') && SlConfigure::read('Sl.options.sslTransport') ? 'https' : 'http';
        $result = $socket->get(
            "$protocol://graph.facebook.com/$objectId",
            $this->accessToken ? $params + array('access_token' => $this->accessToken) : $params
        );
        return $decode ? json_decode($result, true) : $result;
    }

    public function initialize($controller) {
        $this->_initialize($controller, SlConfigure::read('Api.facebook'));
        // $this->cookies = $this->_getFacebookCookie(SlConfigure::read('Api.facebook.appId'), SlConfigure::read('Api.facebook.secret'));
    }

    public function getSession($scope = null) {
        $this->accessToken = SlSession::read('Api.facebook.accessToken');
        if ($this->accessToken) {
            return;
        }

        if (is_array($scope)) {
            $scope = implode(',', $scope);
        }

        $appId = SlConfigure::read('Api.facebook.appId');
        $redirectUrl = array('plugin' => 'api', 'controller' => 'facebook', 'action' => 'oauth');
        if (!empty($this->params['prefix'])) {
            $redirectUrl += array($this->params['prefix'] => false);
        }
        $redirectUrl = urlencode(Sl::url($redirectUrl, true));
        $this->controller->redirect("https://graph.facebook.com/oauth/authorize?client_id=$appId&redirect_uri=$redirectUrl&scope=$scope");
    }

    protected function _getFacebookCookie($app_id, $application_secret) {
        if (empty($_COOKIE['fbs_' . $app_id])) {
            //Sl::krumo('No fbs cookie');
            return;
        }

        $args = array();
        parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
        ksort($args);
        $payload = '';
        foreach ($args as $key => $value) {
            if ($key != 'sig') {
                $payload .= $key . '=' . $value;
            }
        }
        if (md5($payload . $application_secret) != $args['sig']) {
          return;
        }
      return $args;
    }

}
