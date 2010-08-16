<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2($name);
    echo $this->SlHtml->p(".sl-msg-error", __t('The requested address <b>{$url}</b> was not found on this server.', array('url' => $message)));
