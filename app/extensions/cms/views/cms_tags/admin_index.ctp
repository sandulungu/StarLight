<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->actionLink('add', null, array('title' => 'Add Tag')),
        $this->SlHtml->actionLink('add', array('controller' => 'cms_tag_categories'), array('title' => 'Add Tag Category')),
    )));

    echo Pheme::parseTranslate(
<<<end
    $actions
    <table>
        <th>{t}Tag categories{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($tagCategories as $tagCategory) {
        $edit = $this->SlHtml->actionLink('edit', array('controller' => 'cms_tag_categories', $tagCategory['CmsTagCategory']['id']));
        $delete = $this->SlHtml->actionLink('delete', array('controller' => 'cms_tag_categories', $tagCategory['CmsTagCategory']['id']));

        $tags = array();
        foreach ($tagCategory['CmsTag'] as $tag) {
            $tags[] = sprintf('%s [ %s | %s ]',
                $this->SlHtml->link(h($tag['name']), array('controller' => 'cms_nodes', 'tag' => $tag['id'])),
                $this->SlHtml->actionLink('edit', $tag['id']),
                $this->SlHtml->actionLink('delete', $tag['id'])
            );
        }
        $tags = $tags ? $this->Html->nestedList($tags) : '';

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <a name="CmsTag{$tagCategory["CmsTagCategory"]["id"]}"></a>
        <h3>{e}{$tagCategory["CmsTagCategory"]["name"]}{/e}</h3>
        $tags
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

