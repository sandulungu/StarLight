<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    SlConfigure::write('Asset.css.cakeGeneric', 'cake.generic');
    SlConfigure::write('Asset.css.slGeneric', 'sl.generic');

    echo Pheme::parseLayout(compact(
        'title_for_layout',
        'scripts_for_layout',
        'content_for_layout'
    ));
    