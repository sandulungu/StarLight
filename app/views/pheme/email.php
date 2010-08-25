<?php

/** 
 * Email obfuscator
 */

?>
<span class="sl-email">
    {$user}{t}( at ){/t}{$domain}{t}( dot ){/t}{$zone}
    {if("var":"params")}
        <span class="sl-email-params">{$params}</span>
    {/if}
</span>
<?php

class EmailParser extends PhemeParser {
    function parse($html = null, $blockName = 'document', $blockParams = null) {

        SlConfigure::write('Asset.js.footer.emailDefuscator.after',
<<<end
jQuery.fn.defuscate = function(settings) {
    settings = jQuery.extend({link: true}, settings);
    regex = /\b([A-Z0-9._%-]+)\([^)]+\)((?:[A-Z0-9-]+\.?))+\([^)]+\)([A-Z]{2,6})\b/gi;
    mailto = '<a href="mailto:$1@$2.$3">$1@$2.$3</a>';
    plain = "$1@$2.$3";
    return this.each(function() {
        defuscated = jQuery(this).html().replace(regex, settings.link ? mailto : plain)
        jQuery(this).html(defuscated);
    });
}
jQuery(function() { jQuery('.sl-email').defuscate(); });
end
        );

        if (empty($html)) {
            $html = $this->_getVar('CmsContactForm.email');
        }
        if (empty($html)) {
            return;
        }
        
        list($user, $domain) = explode('@', $html, 2);
        $parts = explode('.', $domain);
        $zone = array_pop($parts);
        $domain = implode('.', $parts);

        $params = isset($blockParams['params']) ? $blockParams['params'] : null;
        $this->vars = compact('user', 'domain', 'zone', 'params');
        return parent::parse(null, $blockName);
    }
}

Pheme::registerOutputBuffer('Email', new EmailParser(), true);
