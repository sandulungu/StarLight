<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

?>
<div id="tabs">
<?php

    $extraInfo = Pheme::parseSimple('
        <p>
            {t}Recipient email{/t}: <a href="mailto:{$CmsContactForm.email}">{$CmsContactForm.email}</a>
            {if("var":"CmsNode.short_title")} {t}Short title{/t}: <b>{e}{$CmsNode.short_title}{/e}</b><br /> {/if}
            {if("var":"CmsNode.meta_keywords")} {t}Meta keywords{/t}: <b>{e}{$CmsNode.meta_keywords}{/e}</b><br /> {/if}
            {if("var":"CmsNode.meta_description")} {t}Meta description{/t}: <b>{e}{$CmsNode.meta_description}{/e}</b><br /> {/if}
        </p>
        {if("var":"CmsContactForm.fields")}
            {t}Custom fields{/t}: <blockquote>{e}{$CmsContactForm.fields}{/e}</blockquote>
        {/if}

        <h3>{t}Raw markdown contents{/t}</h3>
        <blockquote>{e}{$CmsNode.body}{/e}</blockquote>

        <h3>{t}Preview contents{/t}</h3>
        {if("var":"CmsNode.body")}
            {$CmsNode.markdown_body} {else}
            {t}Body is empty, so the contact form itself will be shown to the user.{/t}
        {/if}
    ', $cmsNode);

    echo $this->element('admin_node_view', array(
        'plugin' => 'cms',
        'generalInfo' => $extraInfo,
//        'tabs' => array(),
    ));
    
?>
</div>
