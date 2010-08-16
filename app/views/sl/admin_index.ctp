<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (Configure::read()) {
        echo $this->SlHtml->div('.sl-msg-notice', __t("<b>Security warning</b>: Before deploymont set <b>debug</b> to <b>0</b> in <em>/app/config/core.php</em>."));
    }

    $auth = $this->SlHtml->link(__t('Auth extension'), array('controller' => 'users', 'plugin' => 'auth'));
    $config = $this->SlHtml->link(__t('Configuration UI'), array('controller' => 'config'));
    $pages = $this->SlHtml->link(__t('Plain-text publishing'), array('controller' => 'pages'));

    echo $this->SlHtml->p(__t('System core includes basic {$config} and {$pages} functionality.', compact('config', 'pages')));
    echo $this->SlHtml->p(__t('It is also higly recommened to use {$auth} that enforces basic login/logout and ACL security.', compact('auth')));
    echo $this->SlHtml->p(__t('A few extensions and plugins are provided for easy 3rd party database and web service integration.'));
    echo $this->SlHtml->p(__t('Now (1) write your extension and set <em>Routing.prefixes.admin</em> to your dashboard route or (2) use the <b>Cms</b> extension for generic web publishing functionality.'));
    echo $this->SlHtml->p(__t('To activate the Cms extension, remove the leading "_" from its name (rename <em>app/extensions/_cms</em> to <em>app/extensions/cms</em>).'));
