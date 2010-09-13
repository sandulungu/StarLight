<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (!empty($this->params['named']['cms_node_id'])) {
        $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
            $this->SlHtml->actionLink('add'),
        )));
    } else {
        $actions = '';
    }

    echo Pheme::parseTranslate(
<<<end
    $actions
    <table>
        <th width="100">{t}Thumbnail{/t}</th><th>{t}Image info{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($cmsImages as $i) {
        $setActive = $i['CmsNode']['cms_image_id'] == $i['CmsImage']['id'] ?
            '' :
            $this->SlHtml->actionLink('set_as_thumb', $i['CmsImage']['id'], array('title' => __t('Set as node thumb')));
        $edit = $this->SlHtml->actionLink('edit', $i['CmsImage']['id']);
        $delete = $this->SlHtml->actionLink('delete', $i['CmsImage']['id']);

        $hidden = $i['CmsImage']['visible'] ? '' : $this->SlHtml->em(__t('hidden'));

        if (empty($i["CmsImage"]["name"])) {
            $i["CmsImage"]["name"] = '?';
        }

        $row = Pheme::parseSimple('
<tr><td>
    {if("var":"CmsImage.id")}
        {init}JqueryColorbox{/init}
        {JqueryColorbox/}
        <a rel="colorbox" href="{webroot}files/cms_images/{$CmsImage.filename}{/webroot}">
            <img src="{webroot}files/cms_images/thumb/icon/{$CmsImage.filename}{/webroot}"
                title="{$CmsImage.title}" alt="{t}Thumbnail{/t}" />
        </a>
    {/if}
</td><td>
    <a name="CmsImage{$CmsImage.id}"></a>
    <h3>{e}{$CmsImage.title}{/e} {$disabled}</h3>
    {if("var":"CmsNode.id")}
        <p>{t}Node{/t}: <a href="{url}/admin/cms/cms_nodes/view/{$CmsNode.id}{/url}">{$CmsNode.title}</a></p>
    {/if}
    {if("var":"CmsImage.href")}
        <p>{t}Custom link{/t}: <a href="{$CmsImage.href}" target="_blank">{$CmsImage.href}</a></p>
    {/if}
    <p>{t}Weight{/t}: <b>{$CmsImage.weight}</b></p>
</td><td class="actions">
    {$setActive} {$edit} {$delete}
</td></tr>
        ', $i + compact('setActive', 'edit', 'delete', 'hidden'));

        $rows[] = $row;
    }
    echo implode('', $rows);

    $pagination = $this->element('pagination');

    echo Pheme::parseTranslate(
<<<end
    </table>
    $pagination
    $actions
end
    );

