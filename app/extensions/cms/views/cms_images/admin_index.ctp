<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (!empty($nodeId)) {
        $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
            $this->SlHtml->actionLink('add', array('node' => $nodeId)),
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
    foreach ($images as $image) {
        $setActive = $image['CmsNode']['cms_image_id'] == $image['CmsImage']['id'] ?
            '' :
            $this->SlHtml->actionLink('set_as_thumb', $image['CmsImage']['id'], array('title' => __t('Set as node thumb')));
        $edit = $this->SlHtml->actionLink('edit', $image['CmsImage']['id']);
        $delete = $this->SlHtml->actionLink('delete', $image['CmsImage']['id']);

        $hidden = $image['CmsImage']['visible'] ? '' : $this->SlHtml->em(__t('hidden'));

        if (empty($image["CmsImage"]["name"])) {
            $image["CmsImage"]["name"] = '?';
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
        ', $image + compact('setActive', 'edit', 'delete', 'hidden'));

        $rows[] = $row;
    }
    echo implode('', $rows);

    echo Pheme::parseTranslate(
<<<end
    </table>
    $actions
end
    );

