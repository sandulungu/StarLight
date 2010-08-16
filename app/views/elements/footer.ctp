<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (empty($this->Html)) {
        return;
    }

    if (SlConfigure::read('Asset.js.jquery')) {
        SlConfigure::write(
            'Asset.js.footer.jquery',
            array('weight' => -100, 'url' => 'jquery/jquery.min')
        );
    }

    // import JS files
    echo $this->Javascript->multi(array_diff_key(
        SlConfigure::read2('Asset.js.footer'),
        SlConfigure::read2('Asset.js.head')
    ));

    $jsCode = SlConfigure::read2('Asset.js.ready');
    if($jsCode) {
        echo $this->Javascript->ready($jsCode);
    }

    // custom footer stuff
    $contents = SlConfigure::read2('View.html.footer');
    foreach($contents as $content) {
        echo $content;
    }
