<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('AuthUser');
    echo $this->SlForm->input('username');
    echo $this->SlForm->input('password');
    echo $this->SlForm->input('remember', array('type' => 'checkbox'));
    echo $this->SlForm->end(__t('Login'));
