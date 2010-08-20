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
        <th>{t}NavigationLink info{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($navigationLinks as $navigationLink) {
        $edit = $this->SlHtml->actionLink('edit', $navigationLink['CmsNavigationLink']['id']);
        $delete = $this->SlHtml->actionLink('delete', $navigationLink['CmsNavigationLink']['id']);

        $hidden = $navigationLink['CmsNavigationLink']['visible'] ? '' : $this->SlHtml->em(__t('hidden'));

        if (empty($navigationLink["CmsNavigationLink"]["name"])) {
            $navigationLink["CmsNavigationLink"]["name"] = '?';
        }

        $row = Pheme::parseSimple('
<tr><td>
    <div class="sl-level-{$CmsNavigationLink.level}">
        <a name="CmsNavigationLink{$CmsNavigationLink.id}"></a>
        <h3>
            <a href="{webroot}files/cms_attachments/{$CmsNavigationLink.filename}{/webroot}" target="_blank">
                {e}{$CmsNavigationLink.title}{/e}</a>
            {$hidden}
        </h3>
        Modified on: <b>{$CmsNavigationLink.modified}</b>
        {if("var":"CmsNode.id")}
            | {t}Node{/t}: <a href="{url}/admin/cms/cms_nodes/view/{$CmsNode.id}{/url}">{$CmsNode.title}</a>
        {/if}
    </div>
</td><td class="actions">
    {$setActive} {$edit} {$delete}
</td></tr>
        ', $navigationLink + compact('edit', 'delete', 'hidden'));

        $rows[] = $row;
    }
    echo implode('', $rows);

    echo Pheme::parseTranslate(
<<<end
    </table>
    $actions
end
    );

