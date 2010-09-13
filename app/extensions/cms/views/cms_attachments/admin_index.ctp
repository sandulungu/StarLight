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
        <th>{t}Attachment info{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($cmsAttachments as $i) {
        $edit = $this->SlHtml->actionLink('edit', $i['CmsAttachment']['id']);
        $delete = $this->SlHtml->actionLink('delete', $i['CmsAttachment']['id']);

        $hidden = $i['CmsAttachment']['visible'] ? '' : $this->SlHtml->em(__t('hidden'));

        if (empty($i["CmsAttachment"]["name"])) {
            $i["CmsAttachment"]["name"] = '?';
        }

        $row = Pheme::parseSimple('
<tr><td>
    <a name="CmsAttachment{$CmsAttachment.id}"></a>
    <h3>
        <a href="{webroot}files/cms_attachments/{$CmsAttachment.filename}{/webroot}" target="_blank">
            {e}{$CmsAttachment.title}{/e}</a>
        {$hidden}
    </h3>
    {t}Modified{/t}: <b>{$CmsAttachment.modified}</b>
    {if("var":"CmsNode.id")}
        | {t}Node{/t}: <a href="{url}/admin/cms/cms_nodes/view/{$CmsNode.id}{/url}">{$CmsNode.title}</a>
    {/if}
</td><td class="actions">
    {$setActive} {$edit} {$delete}
</td></tr>
        ', $i + compact('edit', 'delete', 'hidden'));

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

