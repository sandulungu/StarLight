<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlForm->create('Group');
    if ($this->id) {
        echo $this->SlForm->hidden('id');
    }

    if (!$this->id || $this->id > 2) {
        echo $this->SlForm->input('name');
    } else {
        $this->SlForm->hidden('name');
        echo $this->SlHtml->p(__t('Name') . ": <b>{$this->data['Group']['name']}</b>");
    }
    echo $this->SlForm->input('description');
    
    echo $this->SlForm->end(__t(!$this->id ? 'Add' : 'Save'));
