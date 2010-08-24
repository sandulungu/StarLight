<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->actionLink('add', !empty($nodeId) ? array('node' => $nodeId) : null),
    )));

    echo Pheme::parseTranslate(
<<<end
    $actions
    <table>
        <th>{t}Link{/t}</th><th>{t}Attributes{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($navigationLinks as $navigationLink) {
        $clone = $this->SlHtml->actionLink('clone', $navigationLink['CmsNavigationLink']['id']);
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
        <h3>{e}{$CmsNavigationLink.title}{/e} {$hidden}</h3>
        {if("var":"CmsNavigationLink.url")}
            {t}Url{/t}: <a href="{url}{$CmsNavigationLink.url}{/url}">{$CmsNavigationLink.url}</a>
        {/if}
    </div>
</td><td>
        {if("var":"CmsNavigationLink.hint")}
            {t}Hint{/t}: <b>{$CmsNavigationLink.hint}</b><br />
        {/if}
        {if("var":"CmsNavigationLink.class")}
            {t}Class{/t}: <b>{$CmsNavigationLink.class}</b><br />
        {/if}
        {if("var":"CmsNavigationLink.rel")}
            {t}Rel{/t}: <b>{$CmsNavigationLink.rel}</b><br />
        {/if}
        {t}Collection{/t}: <b>{$CmsNavigationLink.collection}
        {if("CmsNavigationLink.name")}
            {t}Name{/t}: <b>{$CmsNavigationLink.name}</b>
        {/if}
</td><td class="actions">
    {$clone} {$edit} {$delete}
</td></tr>
        ', $navigationLink + compact('clone', 'edit', 'delete', 'hidden'));

        $rows[] = $row;
    }
    echo implode('', $rows);

    echo Pheme::parseTranslate(
<<<end
    </table>
    $actions
end
    );

