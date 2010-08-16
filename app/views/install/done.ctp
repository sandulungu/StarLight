<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (Configure::read()) {
        echo $this->SlHtml->div('.sl-msg-notice', __t("<b>Security warning</b>: Before deploymont set <b>debug</b> to <b>0</b> in <em>/app/config/core.php</em>."));
    }

    echo $this->SlHtml->div(__t("That's all, your site is now up and ready."));
    echo $this->SlHtml->div(__t('You should set all the remaining configuration settings from the administrative panel.'));
    echo $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->link(h(__t('Goto site configuration')), array('controller' => 'config', 'admin' => true))
    )));
