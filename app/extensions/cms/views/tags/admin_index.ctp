<style type="text/css">
</style>


<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2(__t('Tags'));
    $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->actionLink('add'),
        $this->SlHtml->actionLink('add', null, array(
            'url' => array('controller' => 'tag_types'),
            'title' => __t('New tag group'))
        ),
    )));

    // show items
    echo Pheme::parseTranslate(
<<<end
    $actions
    <table>
        <th>{t}Tag name{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($tags as $tagTypeId => $tag) {
        $edit = $this->SlHtml->actionLink('edit', $tagTypeId, array('url' => array('controller' => 'tag_types')));
        $delete = $this->SlHtml->actionLink('delete', $tagTypeId, array('url' => array('controller' => 'tag_types')));

        $tags = array();
        foreach ($tag as $tagId => $tagName) {
            $tagEdit = $this->SlHtml->actionLink('edit', $tagId);
            $tagDelete = $this->SlHtml->actionLink('delete', $tagId);
            $tags[] = $this->SlHtml->link($tagName, array('controller' => 'books', 'tag' => $tagId)).
                " [ $tagEdit | $tagDelete ]";
        }
        $tags = $this->Html->nestedList($tags);

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <h3>{$tagTypes[$tagTypeId]}</h3>
        $tags
    </td><td class="actions">
        $edit $delete
    </td></tr>
end
        );

        $rows[] = $row;
    }

    echo implode('', $rows);
    echo "</table>";
    echo $actions;
