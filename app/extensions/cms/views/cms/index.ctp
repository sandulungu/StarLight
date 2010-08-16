<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    $createContent = $this->SlHtml->link(__t('create some content'), array('admin' => true, 'controller' => 'nodes', 'action' => 'add'));
    echo $this->SlHtml->p(__t('StarLight has been succesfully installed and the Cms extension is being active.'));
    echo $this->SlHtml->p(__t('Next {$createContent} to change the homepage of your site.', compact('createContent')));
