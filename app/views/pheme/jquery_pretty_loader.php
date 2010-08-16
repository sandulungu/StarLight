<?php
/** 
 * PrettyLoader
 */

if (false) {
    //$this = new SlView(); // for IDE
}

$imgSrc = $this->SlHtml->assetUrl('prettyLoader/images/prettyLoader/ajax-loader.gif');

SlConfigure::write('Asset.css.prettyLoader', 'prettyLoader/css/prettyLoader');
SlConfigure::write('Asset.js.footer.prettyLoader', 'prettyLoader/js/jquery.prettyLoader');
SlConfigure::write('Asset.js.ready.prettyLoader', "$.prettyLoader({loader:'$imgSrc'})");
