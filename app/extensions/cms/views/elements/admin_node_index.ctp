<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    $allNodeTypes = $this->params['controller'] == 'cms_nodes' && empty($this->params['named']['simple_add']);

    if ($allNodeTypes) {
        $newActions = array();
        foreach (SlConfigure::read('Cms.nodeTypes') as $model => $data) {
            $url = $model != 'default' ?
                array('controller' => Inflector::tableize($model), 'plugin' => $data['plugin']) :
                array();
            $newActions[] = $this->SlHtml->actionLink('add',
                array('parent_id' => isset($this->params['named']['parent_id']) ? $this->params['named']['parent_id'] : null) + $url,
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
    <div class="sl-pagination-wrap">
    $actions
    <table>
        <th width="100">{t}Thumbnail{/t}</th><th>{t}Nodes list{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $homeNodeId = SlConfigure::read('Cms.homeNodeId');

    $rows = array();
    foreach ($cmsNodes as $i) {
        $url = $i['CmsNode']['model'] ? array(
            'plugin' => $i['CmsNode']['plugin'],
            'controller' => Inflector::tableize($i['CmsNode']['model']),
        ) : array();
        $view = $this->SlHtml->url($url + array('action' => 'view', $i['CmsNode']['id']));

        $setAsHome = $homeNodeId != $i['CmsNode']['id'] ?
            $this->SlHtml->actionLink('set_as_homepage', array('controller' => 'cms_nodes', 'plugin' => 'cms', $i['CmsNode']['id'])) :
            '';

        $clone = $this->SlHtml->actionLink('clone', $i['CmsNode']['id'], compact('url'));
        $preview = $this->SlHtml->actionLink('preview', $i['CmsNode']['id'], compact('url'));
        $edit = $this->SlHtml->actionLink('edit', $i['CmsNode']['id'], compact('url'));
        $delete = $this->SlHtml->actionLink('delete', array('controller' => 'cms_nodes', 'plugin' => 'cms', $i['CmsNode']['id']));

        $type = $allNodeTypes && $i["CmsNode"]["model"] ?
            SlConfigure::read("Cms.nodeTypes.{$i["CmsNode"]["model"]}.name") :
            '';

        if ($i['CmsNode']['params']) {
            $params = json_decode($i['CmsNode']['params'], true);
            $params = Sl::krumo(is_array($params) ? $params : $i['CmsNode']['params'], array('debug' => false));
        } else {
            $params = '';
        }

        $draft = $i['CmsNode']['visible'] ? '' : $this->SlHtml->em(__t('draft'));

        $tags = array();
        if (!empty($i['CmsTag'])) {
            foreach ($i['CmsTag'] as $tag) {
                $tags[] = $this->SlHtml->link($tag['name'], array('plugin' => 'cms', 'controller' => 'cms_nodes', 'tag' => $tag['id']));
            }
        }
        $tags = implode(', ', $tags);

        $row = Pheme::parseSimple('
<tr {if("var":"setAsHome")}{else}class="active"{/if}><td>
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
        <h3><a href="{$view}">{e}{$CmsNode.title}{/e}</a> {$type} {$draft} [ {$preview} ]
        </h3>
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
    {$setAsHome} {$clone} {$edit} {$delete}
</td></tr>
        ', $i + compact('preview', 'setAsHome', 'tags', 'type', 'clone', 'view', 'edit', 'delete', 'draft'));

        $rows[] = $row;
    }
    echo implode('', $rows);

    $pagination = $this->element('pagination');
    
    echo Pheme::parseTranslate(
<<<end
    </table>
    $pagination
    $actions
    </div>
end
    );
