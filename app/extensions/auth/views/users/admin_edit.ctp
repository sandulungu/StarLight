<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('User');
    if ($this->id) {
        echo $this->SlForm->hidden('id');
    }

    echo $this->SlForm->input('username');
    echo $this->SlForm->input('password');
    echo $this->SlForm->input('confirm_password', array('type' => 'password'));
    echo $this->SlForm->input('fullname');
    echo $this->SlForm->input('email');
    if ($this->id != 1) {
        echo $this->SlForm->input('active', array('checkedByDefault' => true));
    }
    echo $this->SlForm->input('Group', array('multiple' => 'checkbox'));
    
    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));

    SlConfigure::write('Asset.js.jquery', 'head');
    SlConfigure::write('Asset.js.head.jqueryValidation', 'jquery.validation.min');
    echo $this->Validation->bind('User');
