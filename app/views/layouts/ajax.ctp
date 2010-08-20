<?php

if (false) {
    //$this = new SlView(); // for IDE
}

echo $content_for_layout;

echo $this->SlHtml->div('.sl-clear');

$jsCode = SlConfigure::read2('Asset.js.ready');
if($jsCode) {
    echo $this->Javascript->ready($jsCode);
}
