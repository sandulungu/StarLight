<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->actionLink('add'),
    )));

    echo Pheme::parseTranslate(
<<<end
    $actions
    <table>
        <th>{t}Block title, preview{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($cmsBlocks as $i) {
        $edit = $this->SlHtml->actionLink('edit', $i['CmsBlock']['id']);
        $delete = $this->SlHtml->actionLink('delete', $i['CmsBlock']['id']);

        $disabled = $i['CmsBlock']['visible'] ? '' : $this->SlHtml->em(__t('hidden'));

        if (empty($i["CmsBlock"]["name"])) {
            $i["CmsBlock"]["name"] = '?';
        }

        $content = $i["CmsBlock"]["url"] ?
            __t('Url') . ': ' . $this->SlHtml->link($i["CmsBlock"]["url"], $i["CmsBlock"]["url"]) :
            $i["CmsBlock"]["body"];

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <a name="CmsBlock{$i["CmsBlock"]["id"]}"></a>
        <h3>
            <b>{e}{$i["CmsBlock"]["title"]}{/e}</b>
            (id: <b>{$i["CmsBlock"]["placement"]}.{$i["CmsBlock"]["name"]}</b>,
            {t}collection{/t}: <b>{$i["CmsBlock"]["collection"]}</b>) $disabled
        </h3>
        {$content}
    </td><td class="actions">
        $edit $delete
    </td></tr>
end
        );

        $rows[] = $row;
    }
    echo implode('', $rows);

    echo Pheme::parseTranslate(
<<<end
    </table>
    $actions
end
    );

