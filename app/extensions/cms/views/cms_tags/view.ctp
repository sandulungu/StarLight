<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (!empty($node)) {
        echo Pheme::parse('NodeView', $node);
    }

    if (!empty($nodes)) {
        echo $this->SlHtml->h3(__t('Tagged as "{$tags}"', array($tag['Tag']['name'])));
        echo Pheme::parse('NodeIndex', $nodes);
    }
