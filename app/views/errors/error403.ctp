<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->p(".sl-msg-error", __t('Access to <b>{$url}</b> is forbidden. You don\'t have enough permissions.', compact('url')));

    SlSession::write('Auth.url.afterLogin', Sl::url(false));
    $loginBox = Sl::requestAction(array('plugin' => 'auth', 'controller' => 'auth_users', 'action' => 'login'));
    
    echo $this->SlHtml->h3(SlConfigure::read('View.lastRenderTitle'));
    echo $loginBox;
