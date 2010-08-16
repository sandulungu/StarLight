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
        <th>{t}Groups{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($groups as $group) {
        $edit = $this->SlHtml->actionLink('edit', $group['Group']['id']);
        $delete = $group['Group']['id'] > 2 ? $this->SlHtml->actionLink('delete', $group['Group']['id']) : '';

        $users = array();
        foreach ($group['User'] as $user) {
            $users[] = $this->SlHtml->link(h($user['username']), array('controller' => 'users', '#' => "User{$user['id']}"));
        }
        $users = $users ? implode(', ', $users) : __t('none');

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        {!preserveWhitespace}
        <a name="Group{$group["Group"]["id"]}"></a>
        <h3>{e}{$group["Group"]["name"]}{/e}</h3>
        {t}Members{/t}: $users
        <blockquote>{e}{$group["Group"]["description"]}{/e}</blockquote>
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

