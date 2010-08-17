<?php

/**
 * Uses the new single-domain API
 */

class GoogleAnalyticsParser {

    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $id = SlConfigure::read('Api.google.analytics.id');
        if (empty($id)) {
            return;
        }

		SlConfigure::write("Asset.js.footer.$id", array(
            'weight' => 1000,
            'after' =>
<<<end
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$id']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
end
        ));
    }
}

Pheme::register('GoogleAnalytics', new GoogleAnalyticsParser(), null, true);