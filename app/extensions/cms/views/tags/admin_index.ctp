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
        <th>{t}Users{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($users as $user) {
        $edit = $this->SlHtml->actionLink('edit', $user['User']['id']);
        $delete = $user['User']['id'] > 1 ? $this->SlHtml->actionLink('delete', $user['User']['id']) : '';

        if ($user['User']['params']) {
            $params = json_decode($user['User']['params'], true);
            $params = Sl::krumo(is_array($params) ? $params : $user['User']['params'], array('debug' => false));
        } else {
            $params = '';
        }

        $groups = array();
        if ($user['User']['id'] == 1) {
            $groups[] = __t('Root');
        }
        foreach ($user['Group'] as $group) {
            $groups[] = $this->SlHtml->link(h($group['name']), array('controller' => 'groups', '#' => "Group{$group['id']}"));
        }
        $groups = $groups ? implode(', ', $groups) : __t('none');

        $disabled = $user['User']['active'] ? '' : $this->SlHtml->em(__t('disabled'));

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <a name="User{$user["User"]["id"]}"></a>
        <h3>{e}{$user["User"]["fullname"]}{/e} ({$user["User"]["username"]}, <a href="mailto:{$user["User"]["email"]}">{$user["User"]["email"]}</a>) $disabled</h3>
        {t}Groups{/t}: $groups
        {$params}
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

