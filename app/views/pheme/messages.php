<?php

/**
 * Message:
 *      loop -> $message, params = array('class' => 'notice')
 */

?>
<div class="sl-messages">
    {$bufferedOutput}
    {loop}
        {if("var":"params.class")}
            <div class="sl-msg-{$params.class}">{$message}</div>
        {else}
            <div class="sl-msg-notice">{$message}</div>
        {/if}
    {/loop}
</div>
<?php

class MessagesLoopParser extends PhemeLoopParser {
    public function parse($html = null, $blockName = 'document', $blockParams = null, $noCycle = false) {
        $blockParams['var'] = 'message';
        return parent::parse($html, $blockName, $blockParams, $noCycle);
    }
}

class MessagesParser extends PhemeParser {

    function __construct($rules = array(), $options = array()) {
        $this->blocks["loop"] = new MessagesLoopParser();
        parent::__construct($rules, $options);
    }

    function parse($html = null, $blockName = 'document', $blockParams = null, $noCycle = false) {
        $messages = SlConfigure::read2('Message');
        if ($messages) {
            SlSession::delete('Message');
        } else {
            $messages = array();
        }

        $this->vars['bufferedOutput'] = SlConfigure::read('View.bufferedOutput');
        SlConfigure::delete('View.bufferedOutput');

        if ($messages || $this->vars['bufferedOutput']) {
            $this->blocks["loop"]->params[0] = $messages;
            return parent::parse($html, $blockName);
        }
    }
}

Pheme::registerOutputBuffer(new MessagesParser(), true);
