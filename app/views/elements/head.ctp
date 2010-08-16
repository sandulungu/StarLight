<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (empty($this->Html)) {
        return;
    }

    if (SlConfigure::read('Asset.js.jquery') == 'head') {
        SlConfigure::write(
            'Asset.js.head.jquery',
            array('weight' => -100, 'url' => 'jquery/jquery.min')
        );
    }

    echo $this->Html->charset();
    echo $this->SlHtml->title($title_for_layout);

    echo $this->Html->meta('icon');

	echo $scripts_for_layout;

    $cssFiles = SlConfigure::read2('Asset.css');
    foreach($cssFiles as $cssFile) {
        echo $this->SlHtml->css($cssFile);
    }

    // import JS files
    echo $this->Javascript->multi(SlConfigure::read2('Asset.js.head'));

    // custom head stuff
    $contents = SlConfigure::read2('View.html.head');
    foreach($contents as $content) {
        echo $content;
    }

