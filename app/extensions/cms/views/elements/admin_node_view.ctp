<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    Pheme::init('JqueryUi', 'JqueryPrettyLoader', 'JqueryColorbox');
    SlConfigure::append('Asset.js.ready', '$("#tabs").tabs()');

    if (empty($tabs)) {
        $tabs = array();
    }
    echo $this->Html->nestedList(array(
        'generalInfo' => $this->SlHtml->link(__t('General info'), '#tab-general-info'),
        'images' => $this->SlHtml->link(__t('Images'), array('controller' => 'cms_images', 'plugin' => 'cms', 'node' => $node['CmsNode']['id'])),
        'attachments' => $this->SlHtml->link(__t('Attachments'), array('controller' => 'cms_attachments', 'plugin' => 'cms', 'node' => $node['CmsNode']['id'])),
        'childNodes' => $this->SlHtml->link(__t('Child nodes'), array('action' => 'index', 'controller' => 'cms_nodes', 'plugin' => 'cms', 'parent' => $node['CmsNode']['id'])),
        'blocks' => $this->SlHtml->link(__t('Blocks showing this node'), array('controller' => 'cms_blocks', 'plugin' => 'cms', 'node' => $node['CmsNode']['id'])),
        'backLinks' => $this->SlHtml->link(__t('Back links'), array('controller' => 'cms_navigation_links', 'plugin' => 'cms', 'node' => $node['CmsNode']['id'])),
    ) + $tabs);

?>
    <div id="tab-general-info">
    <?php

        echo $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
            $this->SlHtml->actionLink('preview', $node['CmsNode']['id']),
            $this->SlHtml->actionLink('index', null, array('title' => __t('View all'))),
            $this->SlHtml->actionLink('clone', $node['CmsNode']['id']),
            $this->SlHtml->actionLink('edit', $node['CmsNode']['id']),
            $this->SlHtml->actionLink('delete', array('controller' => 'cms_nodes', 'plugin' => 'cms', $node['CmsNode']['id'])),
        )));
        echo $this->SlHtml->div('.sl-clear');

        $this->viewVars['title'] .= $node['CmsNode']['visible'] ? '' : ' ' . $this->SlHtml->em(__t('draft'));

        $parentLink = $node['ParentNode']['id'] ? SlNode::url($node['ParentNode'], array('admin' => true)) : '';

        $tags = array();
        if (!empty($node['CmsTag'])) {
            foreach ($node['CmsTag'] as $tag) {
                $tags[] = $this->SlHtml->link($tag['name'], array('plugin' => 'cms', 'controller' => 'cms_nodes', 'tag' => $tag['id']));
            }
        }
        $tags = implode(', ', $tags);

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
    {if("var":"ParentNode.id")} <p>{t}Parent{/t}: <a href="{$parentLink}">{$ParentNode.title}</a></p> {/if}
    {if("var":"tags")} <p>{t}Tags{/t}: {$tags}</p> {/if}

    {if:generalInfo("var":"generalInfo")}
        {$generalInfo}
    {else:generalInfo}
        <p>
            {if("var":"CmsNode.short_title")} {t}Short title{/t}: <b>{e}{$CmsNode.short_title}{/e}</b><br /> {/if}
            {if("var":"CmsNode.meta_keywords")} {t}Meta keywords{/t}: <b>{e}{$CmsNode.meta_keywords}{/e}</b><br /> {/if}
            {if("var":"CmsNode.meta_description")} {t}Meta description{/t}: <b>{e}{$CmsNode.meta_description}{/e}</b><br /> {/if}
            {t}Skin{/t}: <b>{$CmsNode.skin}</b><br />
            {t}Created{/t}: <b>{$CmsNode.created}</b><br />
            {t}Modified{/t}: <b>{$CmsNode.created}</b>
        </p>
        <div class="sl-clear"></div>

        <h3>{t}Raw markdown contents{/t}</h3>
        <blockquote>{e}{$CmsNode.teaser}{/e}</blockquote>
        <blockquote>{e}{$CmsNode.body}{/e}</blockquote>

        <h3>{t}Preview contents{/t}</h3>
        {if("var":"CmsNode.teaser")}{$CmsNode.markdown_teaser} <hr />{/if}
        {if("var":"CmsNode.body")}
            {$CmsNode.markdown_body} {else}
            {t}Body is empty, creating child nodes will make this node list their teasers as its content.{/t}
        {/if}
    {/if:generalInfo}
    <hr />
</div>
        ', $node + compact('tags', 'generalInfo', 'draft', 'parentLink'));

        echo $this->SlHtml->div('.sl-clear');
        echo $actions;
        echo $this->SlHtml->div('.sl-clear');

    ?>
    </div>
