<?php

    if (false) {
        //$this = new SlView(); // for IDE
    }

    $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->actionLink('add'),
    )));

    // show items
    echo Pheme::parseTranslate(
<<<end
    $actions
    <table>
        <th>{t}Page title, content{/t}</th><th>Preview</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($pages as $i) {
        $actionClone = $this->SlHtml->actionLink('clone', $i['Page']['id']);
        $actionEdit = $this->SlHtml->actionLink('edit', $i['Page']['id']);
        $actionDelete = $this->SlHtml->actionLink('delete', $i['Page']['id']);

        $row = Pheme::parseTranslate(
<<<end
    {!preserveWhitespace}
    <tr><td>
        <h3><a href="{url}/admin/pages/view/{$i['Page']['id']}{/url}">{e}{$i["Page"]["title"]}{/e}</a></h3>
        {e}{$i["Page"]["content"]}{/e}
    </td><td>
        {$i["Page"]["markdown_content"]}
    </td><td class="actions">
        $actionClone $actionEdit $actionDelete
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
