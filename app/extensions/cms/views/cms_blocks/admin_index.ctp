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
        <th>{t}Block title, preview{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($blocks as $block) {
        $edit = $this->SlHtml->actionLink('edit', $block['CmsBlock']['id']);
        $delete = $this->SlHtml->actionLink('delete', $block['CmsBlock']['id']);

        $disabled = $block['CmsBlock']['visible'] ? '' : $this->SlHtml->em(__t('hidden'));

        if (empty($block["CmsBlock"]["name"])) {
            $block["CmsBlock"]["name"] = '?';
        }

        $content = $block["CmsBlock"]["url"] ?
            __t('Url') . ': ' . $this->SlHtml->link($block["CmsBlock"]["url"]) :
            $block["CmsBlock"]["body"];

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <a name="CmsBlock{$block["CmsBlock"]["id"]}"></a>
        <h3>
            <b>{e}{$block["CmsBlock"]["title"]}{/e}</b>
            (id: <b>{$block["CmsBlock"]["placement"]}.{$block["CmsBlock"]["name"]}</b>,
            {t}collection{/t}: <b>{$block["CmsBlock"]["collection"]}</b>) $disabled
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

