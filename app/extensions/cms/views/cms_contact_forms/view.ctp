<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo Pheme::parse('NodeView', array('vars' => $cmsNode + compact('fields')));
