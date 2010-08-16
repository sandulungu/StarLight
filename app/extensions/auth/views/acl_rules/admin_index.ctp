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
        $edit = $this->SlHtml->actionLink('edit', $aclRule['AclRule']['id']);
        $delete = $this->SlHtml->actionLink('delete', $aclRule['AclRule']['id']);

        $verb = $aclRule["AclRule"]["allow"] ? __t('Allow') : __t('Deny');
        
        if ($aclRule["AclRule"]["user_id"]) {
            $who = $this->SlHtml->link(h($aclRule['User']['username']), array('controller' => 'users', '#' => "User{$aclRule['User']['id']}"));
        }
        elseif ($aclRule["AclRule"]["group_id"]) {
            $who = $this->SlHtml->link(h($aclRule['Group']['name']), array('controller' => 'groups', '#' => "User{$aclRule['Group']['id']}"));
        }
        else {
            $who = "<b>{$aclRule['Group']['who']}</b>";
        }

        $row = Pheme::parseTranslate(
<<<end
    <tr><td>
        <a name="AclRule{$aclRule["AclRule"]["id"]}"></a>
        <h3>$verb $who to <b>{$aclRule["AclRule"]["what"]}</b> (<b>{$aclRule["AclRule"]["collection"]}</b> context)</h3>
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

