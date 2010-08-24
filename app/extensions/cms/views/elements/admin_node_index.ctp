<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    $allNodeTypes = $this->params['controller'] == 'cms_nodes';

    if ($allNodeTypes) {
        $newActions = array();
        foreach (SlConfigure::read('Cms.nodeTypes') as $model => $data) {
            $url = $model != 'default' ?
                array('controller' => Inflector::tableize($model), 'plugin' => $data['plugin']) :
                array();
            $newActions[] = $this->SlHtml->actionLink('add',
                array('parent' => isset($parentId) ? $parentId : null) + $url,
                array('title' => __t($data['name']))
            );
        }

        $actions = $this->SlHtml->fieldset(
            $this->SlHtml->legend(__t('Add')) .
            $this->SlHtml->div('.actions', $this->Html->nestedList($newActions))
        );
    }
    else {
        $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
            $this->SlHtml->actionLink('add')
        )));
    }

    echo Pheme::parseTranslate(
<<<end
    $actions
    <table>
        <th width="100">{t}Thumbnail{/t}</th><th>{t}Nodes list{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($nodes as $node) {
        $url = $node['CmsNode']['model'] ? array(
            'plugin' => $node['CmsNode']['plugin'],
            'controller' => Inflector::tableize($node['CmsNode']['model']),
        ) : array();
        $view = $this->SlHtml->url($url + array('action' => 'view', $node['CmsNode']['id']));

        $clone = $this->SlHtml->actionLink('clone', $node['CmsNode']['id'], compact('url'));
        $edit = $this->SlHtml->actionLink('edit', $node['CmsNode']['id'], compact('url'));
        $delete = $this->SlHtml->actionLink('delete', array('controller' => 'cms_nodes', 'plugin' => 'cms', $node['CmsNode']['id']));

        $type = $allNodeTypes && $node["CmsNode"]["model"] ?
            SlConfigure::read("Cms.nodeTypes.{$node["CmsNode"]["model"]}.name") :
            '';

        if ($node['CmsNode']['params']) {
            $params = json_decode($node['CmsNode']['params'], true);
            $params = Sl::krumo(is_array($params) ? $params : $node['CmsNode']['params'], array('debug' => false));
        } else {
            $params = '';
        }

        $draft = $node['CmsNode']['visible'] ? '' : $this->SlHtml->em(__t('draft'));

        $tags = array();
        if (!empty($node['CmsTag'])) {
            foreach ($node['CmsTag'] as $tag) {
                $tags[] = $this->SlHtml->link($tag['name'], array('plugin' => 'cms', 'controller' => 'cms_nodes', 'tag' => $tag['id']));
            }
        }
        $tags = implode(', ', $tags);

        $row = Pheme::parseSimple('
<tr><td>
    {if("var":"CmsImage.id")}
        {init}JqueryColorbox{/init}
        {JqueryColorbox/}
        <a rel="colorbox" href="{webroot}files/cms_images/{$CmsImage.filename}{/webroot}">
            <img src="{webroot}files/cms_images/thumb/icon/{$CmsImage.filename}{/webroot}"
                title="{$CmsImage.title}" alt="{t}Thumbnail{/t}" />
        </a>
    {/if}
</td><td>
    <div class="sl-level-{$CmsNode.level}">
        <a name="CmsNode{$CmsNode.id}"></a>
        <h3><a href="{$view}">{e}{$CmsNode.title}{/e}</a> {$type} {$draft}</h3>
        {if("var":"tags")} <p>{t}Tags{/t}: {$tags}</p> {/if}
        {if:generalInfo("var":"info")}
            {$info}
        {else:generalInfo}
            {e}{var("max":500,"parse":false)}
                {if("var":"CmsNode.teaser")} CmsNode.teaser {else} CmsNode.body {/if}
            {/var}{/e}
        {/if:generalInfo}
    </div>
</td><td class="actions">
    {$clone} {$edit} {$delete}
</td></tr>
        ', $node + compact('tags', 'type', 'clone', 'view', 'edit', 'delete', 'draft'));

        $rows[] = $row;
    }
    echo implode('', $rows);

    echo Pheme::parseTranslate(
<<<end
    </table>
    $actions
end
    );

