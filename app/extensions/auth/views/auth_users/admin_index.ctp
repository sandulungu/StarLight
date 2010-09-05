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
    foreach ($authUsers as $i) {
        $edit = $this->SlHtml->actionLink('edit', $i['AuthUser']['id']);
        $delete = $i['AuthUser']['id'] > 1 ? $this->SlHtml->actionLink('delete', $i['AuthUser']['id']) : '';

        if ($i['AuthUser']['params']) {
            $params = json_decode($i['AuthUser']['params'], true);
            $params = Sl::krumo(is_array($params) ? $params : $i['AuthUser']['params'], array('debug' => false));
        } else {
            $params = '';
        }

        $groups = array();
        if ($i['AuthUser']['id'] == 1) {
            $groups[] = __t('Root');
        }
        foreach ($i['AuthGroup'] as $group) {
            $groups[] = $this->SlHtml->link(h($group['name']), array('controller' => 'auth_groups', '#' => "AuthGroup{$group['id']}"));
        }
        $groups = $groups ? implode(', ', $groups) : __t('none');

        $disabled = $i['AuthUser']['active'] ? '' : $this->SlHtml->em(__t('disabled'));

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <a name="AuthUser{$i["AuthUser"]["id"]}"></a>
        <h3>{e}{$i["AuthUser"]["fullname"]}{/e} ({$i["AuthUser"]["username"]}, <a href="mailto:{$i["AuthUser"]["email"]}">{$i["AuthUser"]["email"]}</a>) $disabled</h3>
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

