<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (Configure::read()) {
        echo $this->SlHtml->div('.sl-msg-notice', __t("<b>Security warning</b>: Before deploymont set <b>debug</b> to <b>0</b> in <em>/app/config/core.php</em>."));
    }

    echo $this->SlHtml->p(__t("Seems like it'is the first time this system is being accessed."));
    echo $this->SlHtml->p(__t("It would be best to set static configuration options (like languages, debugging or administrative settings) and deploy any extensions you will require before continuing."));
    echo $this->SlHtml->p(__t("When ready please proceed to the one-time setup process."));
    echo $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->link(h(__t('Step 1: Check database conectivity >')), array('action' => 'db'))
    )));
