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
    foreach ($aclRules as $aclRule) {
        $edit = $this->SlHtml->actionLink('edit', $aclRule['AuthAclRule']['id']);
        $delete = $this->SlHtml->actionLink('delete', $aclRule['AuthAclRule']['id']);

        $verb = $aclRule["AuthAclRule"]["allow"] ? __t('Allow') : __t('Deny');
        
        if ($aclRule["AuthAclRule"]["user_id"]) {
            $who = $this->SlHtml->link(h($aclRule['AuthUser']['username']), array('controller' => 'auth_users', '#' => "AuthUser{$aclRule['AuthUser']['id']}"));
        }
        elseif ($aclRule["AuthAclRule"]["group_id"]) {
            $who = $this->SlHtml->link(h($aclRule['AuthGroup']['name']), array('controller' => 'auth_groups', '#' => "AuthUser{$aclRule['AuthGroup']['id']}"));
        }
        else {
            $who = "<b>{$aclRule['AuthGroup']['who']}</b>";
        }

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <a name="AuthAclRule{$aclRule["AuthAclRule"]["id"]}"></a>
        <h3>$verb $who to <b>{$aclRule["AuthAclRule"]["what"]}</b> (<b>{$aclRule["AuthAclRule"]["collection"]}</b> context)</h3>
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

