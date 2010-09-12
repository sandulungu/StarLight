<?php

/**
 * Contact form node body
 */

Pheme::init('Cms.NodeView');

?>
<div class="sl-node-body">
	{if("var":"CmsNode.body")}
		{var}CmsNode.markdown_body{/var}
	{else}
        {ContactForm/}
	{/if}
</div>
<?php

Pheme::registerOutputBuffer('NodeBodyCmsContactForm', new NodeBodyParser(), 'NodeView');



// ================================ ContactForm ===================================

?>
<div class="sl-contact-form">
    <form method="post" action="{$actionUrl}" id="{$domId}">
        {loop}
            {$inputHtml}
        {/loop}
        <button>{t}Send{/t}</button>
    </form>
</div>
<?php

class ContactFormParser extends PhemeParser {
    function __construct($rules = array(), $options = array()) {
        $this->blocks["loop"] = new PhemeLoopParser();
        parent::__construct($rules, $options);
    }

    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'fields' => $this->_getVar('fields'),
            'domId' => Sl::uniqid(),
        );

        if (!is_array($blockParams['fields'])) {
            return;
        }
        $form = $this->_getHelper('SlForm');

        $fields = array();
        $form->create('CmsContactForm');
        foreach ($blockParams['fields'] as $f => $o) {
            $fields[] = array(
                'inputName' => $f,
                'inputOptions' => $o,
                'inputHtml' => $form->input($f, $o),
            );
        }

        if ($fields) {
            $this->blocks['loop']->params[0] = $fields;
            $this->vars['domId'] = $blockParams['domId'];
            $this->vars['actionUrl'] = Sl::url();
            return parent::parse($html, $blockName);
        }
    }
}

Pheme::registerOutputBuffer('ContactForm', new ContactFormParser(), 'NodeBodyCmsContactForm');
