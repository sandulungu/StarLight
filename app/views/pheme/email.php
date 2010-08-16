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
