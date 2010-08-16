<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    if ($connected) {
        echo $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
            $this->SlHtml->link(h(__t('Step 2: Populate database >')), array('action' => 'db2'))
        )));
        echo $this->SlHtml->div('.sl-msg-success', __t("Connected to database."));
    }
    elseif (isset($this->params['data'])) {
        echo $this->SlHtml->div('.sl-msg-notice', __t("Cannot connect to database. Please check the parameters below."));
    }


    echo $this->SlForm->create(null);
    echo $this->SlForm->input('driver', array('options' => array(
        'mysql' => 'MySQL 4 & 5 (mysql)',
 		'mysqli' => 'MySQL 4 & 5 Improved Interface (mysqli)',
 		'postgres' => 'PostgreSQL 7 and higher (postgres)',
    )));
    echo $this->SlForm->input('persistent', array('type' => 'checkbox'));
    echo $this->SlForm->input('host', array('div' => 'input text required'));
    echo $this->SlForm->input('login', array('div' => 'input text required'));
    echo $this->SlForm->input('password', array());
    echo $this->SlForm->input('database', array('div' => 'input text required'));
    echo $this->SlForm->input('prefix', array());
    echo $this->SlForm->end(__t('Save'));

    if ($connected) {
        echo "<hr />$actions";
    }