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
        <th>{t}Attachment info{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($attachments as $attachment) {
        $edit = $this->SlHtml->actionLink('edit', $attachment['CmsAttachment']['id']);
        $delete = $this->SlHtml->actionLink('delete', $attachment['CmsAttachment']['id']);

        $hidden = $attachment['CmsAttachment']['visible'] ? '' : $this->SlHtml->em(__t('hidden'));

        if (empty($attachment["CmsAttachment"]["name"])) {
            $attachment["CmsAttachment"]["name"] = '?';
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
        ', $attachment + compact('edit', 'delete', 'hidden'));

        $rows[] = $row;
    }
    echo implode('', $rows);

    echo Pheme::parseTranslate(
<<<end
    </table>
    $actions
end
    );

