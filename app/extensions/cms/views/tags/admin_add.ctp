<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2(__t('Add tags'));
    echo $this->SlForm->create('Tag');

    echo $this->SlForm->input('name', array('after' => 'You may add multiple tags, separated by ","'));
    echo $this->SlForm->input('tag_type_id', array('empty' => true));

    echo $this->SlForm->end(__t('Add'));
