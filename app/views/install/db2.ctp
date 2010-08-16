<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (!$sql) {
        echo $this->SlHtml->div('.sl-msg-message', __t('The database is up-to-date. No changes required.'));
    }

    echo $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->link(h(__t('Step 3: Create administrative account >')), array('action' => 'auth'))
    )));

    if ($sql) {
        if ($success) {
            echo $this->SlHtml->div('.sl-msg-success', __t('{$n} queries executed succesfully:', array('n' => count($sql))));
        }
        echo $this->Html->nestedList($sql);
        echo "<hr />$actions";
    }