<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->link(h(__t('Goto site configuration')), array('controller' => 'config', 'admin' => true))
    )));
