<?php

/**
 * Flash embeds support
 */

SlConfigure::write('Asset.js.ready.oembed',
<<<end
$('.sl-oembed').each(function() {
    var el = $(this);
    var url = el.html().trim();
    if (url) {
        var width = $(this).parent().width();
        var apiUrl = 'http://api.embed.ly/v1/api/oembed?url=' + url + '&callback=?&maxwidth=' + width;
        $.getJSON(apiUrl, function(json) {
            el.addClass('sl-oembedded').removeClass('sl-oembed').html(json.html);
        });
    } else {
        $(this).remove();
    }
});
end
);

?>
<div class="sl-oembed">{$url}</div>
<?php

class JqueryOembedParser extends PhemeParser {

    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
//        $blockParams += array(
//        );
        if ($html && !preg_match('/[\n]/', $html)) {
            $blockParams['url'] = $html;
            $html = null;
        }

        $this->vars = $blockParams;
        return parent::parse($html, $blockName);
    }
}

Pheme::registerOutputBuffer('JqueryOembed', new JqueryOembedParser(), true);
