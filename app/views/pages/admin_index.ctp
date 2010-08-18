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
    foreach ($pages as $page) {
        $view = $this->SlHtml->url(array('action' => 'view', $page['Page']['id'], 'admin' => false));
        $edit = $this->SlHtml->actionLink('edit', $page['Page']['id']);
        $delete = $this->SlHtml->actionLink('delete', $page['Page']['id']);

        $row = Pheme::parseTranslate(
<<<end
    {!preserveWhitespace}
    <tr><td>
        <h3><a href="$view">{$page["Page"]["title"]}</a></h3>
        {e}{$page["Page"]["content"]}{/e}
    </td><td>
        {$page["Page"]["markdown_content"]}
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
