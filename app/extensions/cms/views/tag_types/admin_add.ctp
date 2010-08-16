<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2(__t('Add tag group'));
    echo $this->SlForm->create('TagType');

    echo $this->SlForm->input('name', array('label' => __t('Group Name')));
    echo $this->SlForm->input('tag_names', array('type' => 'textbox', 'after' => 'You may add multiple tags to this category, separating them with ","'));

    echo $this->SlForm->end(__t('Add'));
