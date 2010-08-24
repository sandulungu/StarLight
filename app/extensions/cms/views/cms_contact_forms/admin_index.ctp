<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    foreach ($this->viewVars['nodes'] as &$node) {
        $node['info'] = Pheme::parseSimple('
            <p>
                {t}Recipient email{/t}: <a href="mailto:{$CmsContactForm.email}">{$CmsContactForm.email}</a>
            </p>
            {if("var":"CmsContactForm.fields")}
                {t}Custom fields{/t}: <blockquote>{e}{$CmsContactForm.fields}{/e}</blockquote>
            {/if}
        ', $node);
    }

    echo $this->element('admin_node_index', array('plugin' => 'cms'));
    