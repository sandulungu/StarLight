<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    Pheme::init('JqueryUi', 'JqueryPrettyLoader', 'JqueryColorbox');
    SlConfigure::append('Asset.js.ready', '$("#tabs").tabs()');

?>
<div id="tabs">
<?php

    echo $this->Html->nestedList(array(
        $this->SlHtml->link(__t('General info'), '#tab-general-info'),
        $this->SlHtml->link(__t('Images'), array('controller' => 'cms_images', 'node' => $node['CmsNode']['id'])),
        $this->SlHtml->link(__t('Attachments'), array('controller' => 'cms_attachments', 'node' => $node['CmsNode']['id'])),
        $this->SlHtml->link(__t('Child nodes'), array('action' => 'index', 'parent' => $node['CmsNode']['id'])),
        $this->SlHtml->link(__t('Blocks showing this node'), array('controller' => 'cms_blocks', 'node' => $node['CmsNode']['id'])),
        $this->SlHtml->link(__t('Back links'), array('controller' => 'cms_navigation_links', 'node' => $node['CmsNode']['id'])),
    ));

?>
    <div id="tab-general-info">
    <?php

        echo $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
            $this->SlHtml->actionLink('index', null, array('title' => __t('View all'))),
            $this->SlHtml->actionLink('clone', $node['CmsNode']['id']),
            $this->SlHtml->actionLink('edit', $node['CmsNode']['id']),
            $this->SlHtml->actionLink('delete', $node['CmsNode']['id']),
        )));
        echo $this->SlHtml->div('.sl-clear');

        $this->viewVars['title'] .= $node['CmsNode']['visible'] ? '' : ' ' . $this->SlHtml->em(__t('draft'));

        echo Pheme::parseSimple('
{if("var":"CmsImage.id")}
    {JqueryColorbox/}
    <a class="sl-uploaded-image" rel="colorbox" href="{webroot}files/cms_images/{$CmsImage.filename}{/webroot}">
        <img src="{webroot}files/cms_images/thumb/icon/{$CmsImage.filename}{/webroot}"
            title="{$CmsImage.title}" alt="{t}Thumbnail{/t}" />
    </a>
{/if}
<div class="sl-level-{$CmsNode.level}">
    <a name="CmsNode{$CmsNode.id}"></a>
    {if("var":"CmsNode.short_title")} {t}Short title{/t}: <b>{e}{$CmsNode.short_title}{/e}</b><br /> {/if}
    {if("var":"CmsNode.meta_keywords")} {t}Meta keywords{/t}: <b>{e}{$CmsNode.meta_keywords}{/e}</b><br /> {/if}
    {if("var":"CmsNode.meta_description")} {t}Meta description{/t}: <b>{e}{$CmsNode.meta_description}{/e}</b><br /> {/if}
    {t}Skin{/t}: <b>{$CmsNode.skin}</b><br />
    {t}Created{/t}: <b>{$CmsNode.created}</b><br />
    {t}Modified{/t}: <b>{$CmsNode.created}</b><br />
    <div class="sl-clear"></div>

    <h3>{t}Raw markdown content{/t}</h3>
    <blockquote>{e}{$CmsNode.teaser}{/e}</blockquote>
    <blockquote>{e}{$CmsNode.body}{/e}</blockquote>
    
    <h3>{t}Preview{/t}</h3>
    {if("var":"CmsNode.teaser")}{$CmsNode.markdown_teaser} <hr />{/if}
    {if("var":"CmsNode.body")}
        {$CmsNode.markdown_body} {else}
        {t}Body is empty, creating child nodes will make this node list their teasers as its content.{/t}
    {/if}
</div>
        ', $node + compact('draft'));

        echo $this->SlHtml->div('.sl-clear');
        echo $actions;
        echo $this->SlHtml->div('.sl-clear');

    ?>
    </div>
</div>