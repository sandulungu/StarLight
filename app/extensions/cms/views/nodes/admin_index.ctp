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
        <th width="100">{t}Thumbnail{/t}</th><th>{t}Nodes{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($nodes as $node) {
        $url = $node['Node']['model'] ? array(
            'plugin' => $node['Node']['plugin'],
            'controller' => Inflector::tableize($node['Node']['model']),
        ) : array();
        $view = $this->SlHtml->url($url + array('action' => 'view', $node['Node']['id']));
        
        $edit = $this->SlHtml->actionLink('edit', $node['Node']['id'], compact('url'));
        $delete = $node['Node']['id'] > 1 ? $this->SlHtml->actionLink('delete', $node['Node']['id']) : '';

        if ($node['Node']['params']) {
            $params = json_decode($node['Node']['params'], true);
            $params = Sl::krumo(is_array($params) ? $params : $node['Node']['params'], array('debug' => false));
        } else {
            $params = '';
        }

        $draft = $node['Node']['visible'] ? '' : $this->SlHtml->em(__t('draft'));

        $row = Pheme::parseSimple('
<tr><td>
    {if("var":"Image.id")}
        {init}JqueryColorbox{/init}
        {JqueryColorbox/}
        <a rel="colorbox" href="{webroot}files/cms_images/{$Image.filename}{/webroot}">
            <img src="{webroot}files/cms_images/thumb/icon/{$Image.filename}{/webroot}"
                title="{$Image.title}" alt="{t}Thumbnail{/t}" />
        </a>
    {/if}
</td><td>
    <div class="sl-level-{$Node.level}">
        <a name="Node{$Node.id}"></a>
        <h3><a href="{$view}">{e}{$Node.title}{/e}</a> {$draft}</h3>
        {e}{var("max":500,"parse":false)} {if("var":"Node.teaser")} Node.teaser {else} Node.body {/if} {/var}{/e}
    </div>
</td><td class="actions">
    {$edit} {$delete}
</td></tr>
        ', $node + compact('view', 'edit', 'delete', 'draft'));

        $rows[] = $row;
    }
    echo implode('', $rows);

    echo Pheme::parseTranslate(
<<<end
    </table>
    $actions
end
    );

