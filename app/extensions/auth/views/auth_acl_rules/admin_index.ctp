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
        <th>{t}AclRules{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($authAclRules as $i) {
        $edit = $this->SlHtml->actionLink('edit', $i['AuthAclRule']['id']);
        $delete = $this->SlHtml->actionLink('delete', $i['AuthAclRule']['id']);

        $verb = $i["AuthAclRule"]["allow"] ? __t('Allow') : __t('Deny');
        
        if ($i["AuthAclRule"]["user_id"]) {
            $who = $this->SlHtml->link(h($i['AuthUser']['username']), array('controller' => 'auth_users', '#' => "AuthUser{$i['AuthUser']['id']}"));
        }
        elseif ($i["AuthAclRule"]["group_id"]) {
            $who = $this->SlHtml->link(h($i['AuthGroup']['name']), array('controller' => 'auth_groups', '#' => "AuthUser{$i['AuthGroup']['id']}"));
        }
        else {
            $who = "<b>{$i['AuthGroup']['who']}</b>";
        }

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <a name="AuthAclRule{$i["AuthAclRule"]["id"]}"></a>
        <h3>$verb $who to <b>{$i["AuthAclRule"]["what"]}</b> (<b>{$i["AuthAclRule"]["collection"]}</b> context)</h3>
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

