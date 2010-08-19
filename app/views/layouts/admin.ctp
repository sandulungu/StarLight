<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    SlConfigure::delete('Asset.css.theme', false, '*');
    SlConfigure::write('Asset.css.cakeGeneric', 'cake.generic');
    SlConfigure::write('Asset.css.slGeneric', 'sl.generic');

//    if (!class_exists('Pheme')) {
//        echo "<html><head><title>$title_for_layout</title>$scripts_for_layout</head><body>$content_for_layout</body></html>";
//        return;
//    }

    echo Pheme::parseLayout(compact(
        'title_for_layout',
        'scripts_for_layout',
        'content_for_layout'
    ));
