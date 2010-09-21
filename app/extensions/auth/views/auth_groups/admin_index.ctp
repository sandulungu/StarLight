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
    foreach ($authGroups as $i) {
        $edit = $this->SlHtml->actionLink('edit', $i['AuthGroup']['id']);
        $delete = $i['AuthGroup']['id'] > 2 ? $this->SlHtml->actionLink('delete', $i['AuthGroup']['id']) : '';

        $users = array();
        foreach ($i['AuthUser'] as $user) {
            $users[] = $this->SlHtml->link(h($user['username']), array('controller' => 'auth_users', '#' => "AuthUser{$user['id']}"));
        }
        $users = $users ? implode(', ', $users) : __t('none');

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        {!preserveWhitespace}
        <a name="AuthGroup{$i["AuthGroup"]["id"]}"></a>
        <h3>{$i["AuthGroup"]["id"]}. {e}{$i["AuthGroup"]["name"]}{/e}</h3>
        {t}Members{/t}: $users
        <blockquote>{e}{$i["AuthGroup"]["description"]}{/e}</blockquote>
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

