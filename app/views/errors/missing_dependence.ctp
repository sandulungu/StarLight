<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->p(".sl-msg-error", __t('The <b>{$extension}</b> Extension requires <b>{$dependence}</b> Extension to be active. Install and/or activate the {$dependence}Extension.', compact('extension', 'dependence')));
