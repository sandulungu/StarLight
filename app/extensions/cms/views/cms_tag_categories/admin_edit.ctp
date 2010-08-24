<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('CmsTagCategory');
    if ($this->params['action'] != 'admin_add') {
        echo $this->SlForm->hidden('id');
    }

    echo $this->SlForm->input('name');

    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
