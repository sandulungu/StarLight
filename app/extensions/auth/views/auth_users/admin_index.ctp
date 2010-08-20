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
        $edit = $this->SlHtml->actionLink('edit', $user['AuthUser']['id']);
        $delete = $user['AuthUser']['id'] > 1 ? $this->SlHtml->actionLink('delete', $user['AuthUser']['id']) : '';

        if ($user['AuthUser']['params']) {
            $params = json_decode($user['AuthUser']['params'], true);
            $params = Sl::krumo(is_array($params) ? $params : $user['AuthUser']['params'], array('debug' => false));
        } else {
            $params = '';
        }

        $groups = array();
        if ($user['AuthUser']['id'] == 1) {
            $groups[] = __t('Root');
        }
        foreach ($user['AuthGroup'] as $group) {
            $groups[] = $this->SlHtml->link(h($group['name']), array('controller' => 'auth_groups', '#' => "AuthGroup{$group['id']}"));
        }
        $groups = $groups ? implode(', ', $groups) : __t('none');

        $disabled = $user['AuthUser']['active'] ? '' : $this->SlHtml->em(__t('disabled'));

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <a name="AuthUser{$user["AuthUser"]["id"]}"></a>
        <h3>{e}{$user["AuthUser"]["fullname"]}{/e} ({$user["AuthUser"]["username"]}, <a href="mailto:{$user["AuthUser"]["email"]}">{$user["AuthUser"]["email"]}</a>) $disabled</h3>
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

