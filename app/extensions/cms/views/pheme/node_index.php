<?php

/**
 * NodeIndex, NodeIndexDefault, NodeIndexGallery, NodeIndexTable, NodeIndexBlog, NodeIndexList
 *      $nodes, Pagination, NodeView*
 */

Pheme::init('Cms.NodeView');

/**
 * View a list of nodes
 */
class NodeIndexParser extends PhemeParser {

    function __construct($rules = array(), $options = array()) {
        $this->blocks["loop"] = new PhemeLoopParser();
        parent::__construct($rules, $options);
    }

    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'id' => false,
            'skin' => null, // auto
        );

        if ($blockName === 'NodeIndex') {
            $skin = empty(SL::getInstance()->view->params['named']['skin']) ?
                Inflector::camelize($blockParams['skin']) :
                Inflector::camelize(SL::getInstance()->view->params['named']['skin']);
            $skin = $skin && Pheme::init("NodeIndex$skin") ?
                "NodeIndex$skin" : "NodeIndexDefault";
        } else {
            $skin = $blockName;
        }

        $this->vars['ajaxId'] = SL::uniqid();

        if ($blockParams['id'] === false) {
            $nodes = $this->_getVar('nodes');
        } else {
            if ($blockParams['id'] === 0) {
                $blockParams['id'] = null;
            }
            $nodes = SlNode::find('all', array(
                    'conditions' => array(
                        'CmsNode.parent_id' => $blockParams['id'],
                        'CmsNode.visible' => true,
                    ),
                    'order' => array('CmsNode.lft' => 'asc'),
                    'auth' => 'index',
                ));
        }

        if ($nodes) {
            foreach ($nodes as &$node) {
                $node['href'] = h(SlNode::url($node));
            }
            $this->blocks["loop"]->params[0] =& $nodes;


            if ($skin != $blockName) {
                PhemeParser::$parseCallStack[] = Pheme::get($skin);
            }
            PhemeParser::$parseCallStack[] = Pheme::get('NodeView');
            $html = parent::parse($html, $skin);
            array_pop(PhemeParser::$parseCallStack);
            if ($skin != $blockName) {
                array_pop(PhemeParser::$parseCallStack);
            }

            return $html;
        }
    }
}

Pheme::register('NodeIndex', new NodeIndexParser(), null, true);

// -----------------------------------------------------------------------------
// Default: shows node teasers with thumbs and admin. actions separated by <hr>s (with ajax pagination)

?>
<div id="{$ajaxId}">
{loop}
    {NodeActions/}
    {if("var":"CmsNode.title")}
        <h3><a href="{$href}">{e}{$CmsNode.title}{/e}</a></h3>
    {/if}
    {Image("align":"auto")/}
    {NodeTeaser/}
    {!sep:loop} <hr class="sl-node-separator" />
{/loop}
</div>

{require}Pagination{/require}
{Pagination("ajax":true)/}
<?php

Pheme::registerOutputBuffer('NodeIndexDefault', 'NodeIndex', true);

// -----------------------------------------------------------------------------
// List: Unsortered List of node titles as links and a link to the default skin

?>
{loop("itemTag":"li")}
    <ul class="sl-node-index">
        <li><a href="{$href}">{if("var":"CmsNode.title")} {e}{$CmsNode.title}{/e} {else} {t}Unnamed page{/t} {/if}</a></li>
    </ul>
{/loop}

{require}Pagination{/require}
{PaginationLink("text":"{t}Show detailed...{/t}", "params":{"skin":"default", "limit":null})/}
<?php

Pheme::registerOutputBuffer('NodeIndexList', 'NodeIndex', true);

// -----------------------------------------------------------------------------
// Table: A table with small icons as thumb, title and teaser fields

?>
<div id="{$ajaxId}">
{loop("itemTag":"tr")}
    <table class="sl-node-index">
        <thead>
            <tr class="sl-header"><th>&nbsp;</th><th>{!skip:loop} {t}Title{/t}</th><th>Teaser</th></tr>
        </thead>
        <tbody>
            <tr class="sl-even"><td>{Image("width":16, "height":16, "method":"fitCrop")/}</td><td><a href="{$href}">{e}{$CmsNode.title}{/e}</a></td><td>{var}CmsNode.teaser{/var}</td></tr>
            <tr class="sl-odd"><td>{Image("width":16, "height":16, "method":"fitCrop")/}</td><td><a href="{$href}">{e}{$CmsNode.title}{/e}</a></td><td>{var}CmsNode.teaser{/var}</td></tr>
        </tbody>
    </table>
{/loop}
</div>

{require}Pagination{/require}
{Pagination("ajax":true)/}
<?php

Pheme::registerOutputBuffer('NodeIndexTable', 'NodeIndex', true);

// -----------------------------------------------------------------------------
// Gallery: a simple gallery with node title and teaser fields show below the image

?>
<div id="{$ajaxId}">
{loop("groupTag":"tr", "itemTag":"td", "showEmpty":true)}
    <table class="sl-node-index-gallery">
        <tr>
            <td width="33%">{Image/}<h3><a href="{$href}">{e}{$CmsNode.title}{/e}</a></h3><div class="sl-node-teaser">{var}CmsNode.teaser{/var}</div></td>
            <td width="33%">{Image/}<h3><a href="{$href}">{e}{$CmsNode.title}{/e}</a></h3><div class="sl-node-teaser">{var}CmsNode.teaser{/var}</div></td>
            <td width="33%">{Image/}<h3><a href="{$href}">{e}{$CmsNode.title}{/e}</a></h3><div class="sl-node-teaser">{var}CmsNode.teaser{/var}</div></td>
        </tr>
        <tr><td class="sl-empty">&nbsp;</td><td class="sl-empty">&nbsp;</td><td class="sl-empty">&nbsp;</td></tr>
    </table>
{/loop}
</div>

{require}Pagination{/require}
{Pagination("ajax":true)/}
<?php

Pheme::registerOutputBuffer('NodeIndexGallery', 'NodeIndex', true);

// -----------------------------------------------------------------------------
// Blog: same as default index, but with author/publishing details and continuous pagination

?>
{loop}
    {NodeActions/}
    {if("var":"CmsNode.title")}
        <h3><a href="{$href}">{e}{$CmsNode.title}{/e}</a></h3>
    {/if}
    {if("var":"CmsNode.created","value":"{$CmsNode.modified}")}
        <div class="sl-node-modified"><span>{t}Writted on{/t}:</span> {$CmsNode.modified}</div>
    {else}
        <div class="sl-node-modified"><span>{t}Updated on{/t}:</span> {$CmsNode.modified}</div>
    {/if}
    {Image("align":"auto")/}
    {NodeTeaser/}
    {!sep:loop} <hr class="sl-node-separator" />
{/loop}
<div id="{$ajaxId}"></div>

{require}Pagination{/require}
{PaginationLink("text":"{t}Show more...{/t}", "ajax":true, "page":"next")/}
<?php

Pheme::registerOutputBuffer('NodeIndexBlog', 'NodeIndex', true);
