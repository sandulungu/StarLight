<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->p(__t('StarLight has been succesfully installed.'));
    echo $this->SlHtml->p(__t('Now (1) write your extension and set <em>Routing.home</em> to your homepage route or (2) use the <b>Cms</b> extension for generic web publishing functionality.'));
    echo $this->SlHtml->p($this->SlHtml->link(__t('Find out more...'), array('admin' => true)));
