<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    $theme = SlConfigure::read2('Asset.css.theme');
    if ($theme == 'auto' && SlConfigure::read2('View.theme')) {
        SlConfigure::write('Asset.css.theme', SlConfigure::read2('View.theme'));
    }
    elseif (!$theme || $theme == 'auto') {
        SlConfigure::write('Asset.css.cakeGeneric', 'cake.generic');
        SlConfigure::write('Asset.css.slGeneric', 'sl.generic');
    }

    echo Pheme::parseLayout(compact(
        'title_for_layout',
        'scripts_for_layout',
        'content_for_layout'
    ));
    