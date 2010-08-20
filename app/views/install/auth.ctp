<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('AuthUser', array('url' => array('controller' => 'install', 'action' => 'auth')));
    echo $this->SlForm->input('username');
    echo $this->SlForm->input('password');
    echo $this->SlForm->input('confirm_password', array('type' => 'password'));
    echo $this->SlForm->input('fullname');
    echo $this->SlForm->input('email');
    echo $this->SlForm->end(__t('Create user >'));
