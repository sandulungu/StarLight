<?php

/**
 * NodeView, NodeViewDefault, NodeViewGallery, NodeViewBlog,
 * NodeBody, NodeTeaser, NodeBodyDefault, NodeTeaserDefault:
 *      $Node, $Image, $Attachment, $Tag, $ExtraImages,
 *      NodeActions, Tags, Image, ImageGallery, Attachments, Attachment
 */

Pheme::init('Cms.NodeIndex', 'Cms.Image', 'Swfobject', 'Email');

/**
 * Show (direct) child nodes
 */
class NodeChildrenParser extends PhemeParser {
    function parse($html = null, $blockName = 'document', $blockParams = null) {

        // check whether we have any child nodes
        $lft = $this->_getVar('CmsNode.lft');
        $rght = $this->_getVar('CmsNode.rght');
        if ($lft && $rght && $lft == $rght - 1) {
            return;
        }

        $blockParams = (array)$blockParams;
        $blockParams += array(
            'id' => $this->_getVar('CmsNode.id')
        );
        return Pheme::get('NodeIndex')->parse($html, 'NodeIndex', $blockParams);
    }
}

/**
 * Unescape HTML - so you can write HTML code directly in the WYSIWYG editor
 */
class NodeUnescapeParser extends PhemeParser {
    function parse($html = null, $blockName = 'document', $blockParams = null) {
        return html_entity_decode(
            parent::parse($html),
            ENT_QUOTES,
            empty($blockParams['charset']) ? 'UTF-8' : $blockParams['charset']
        );
    }
}

/**
 * View one node's contents
 */
class NodeParser extends PhemeParser {
    function __construct($rules = array(), $options = array()) {
        $this->blocks['NodeChildren'] = new NodeChildrenParser();
        $this->blocks['Html'] = new NodeUnescapeParser();

        parent::__construct($rules, $options);
    }

    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'id' => null,
        );

        if ($blockParams['id']) {
            $this->vars = SL::getNode($blockParams['id'], array('auth' => true));
        }

        if ($blockName === 'NodeView') {
            $skin = empty(SL::getInstance()->view->params['named']['skin']) ?
                Inflector::camelize($this->_getVar('CmsNode.skin')) :
                Inflector::camelize(SL::getInstance()->view->params['named']['skin']);
            $skin = $skin && Pheme::init("NodeView$skin") ? "NodeView$skin" : "NodeViewDefault";
        } else {
            $skin = $blockName;
        }


        if ($skin != $blockName) {
            PhemeParser::$parseCallStack[] = Pheme::get($skin);
            $result = parent::parse($html, $skin);
            array_pop(PhemeParser::$parseCallStack);
            return $result;
        } else {
            return parent::parse($html, $skin);
        }
    }
}

Pheme::register('NodeView', new NodeParser(), null, true);

// -----------------------------------------------------------------------------
// Default view: admin. actions, thumb image and node body

?>
{NodeActions/}
{Image("align":"auto")/}
{NodeBody/}
<?php

Pheme::registerOutputBuffer('NodeViewDefault', 'NodeView', true);

// -----------------------------------------------------------------------------
// Simplest view: just the body (optinally the actions)

?>
{if("param":"hideActions","op":"==","value":null)}
    {NodeActions/}
{/if}
{NodeBody/}
<?php

Pheme::registerOutputBuffer('NodeViewSimple', 'NodeView', true);

// -----------------------------------------------------------------------------
// Gallery view: admin. actions, node body and a full gallery

?>
{NodeActions/}
{NodeBody/}
{ImageGallery/}
<?php

Pheme::registerOutputBuffer('NodeViewGallery', 'NodeView', true);

// -----------------------------------------------------------------------------
// Blog view: admin. actions, author and publishing info, thumb and node body

?>
{NodeActions/}
{if("var":"CmsNode.created","value":"{$CmsNode.modified}")}
    <div class="sl-node-create">
        <span>{t}Writted on{/t}:</span> {$CmsNode.modified} {t}by{/t} {$AuthUser.fullname}
    </div>
{else}
    <div class="sl-node-modified">
        <span>{t}Updated on{/t}:</span> {$CmsNode.modified} {t}by{/t} {$AuthUser.fullname}
    </div>
{/if}
{Image("align":"auto")/}
{NodeBody/}
{Attachments/}
<?php

Pheme::registerOutputBuffer('NodeViewBlog', 'NodeView', true);



// ============================= NodeTeaser ====================================



class NodeTeaserParser extends PhemeParser {
    function parse($html = null, $blockName = 'document', $blockParams = null) {

        if ($blockName == 'NodeTeaser' || $blockName == 'NodeBody') {
            $skin = $this->_getVar('CmsNode.model');
            if ($skin) {
                $plugin = $this->_getVar('CmsNode.plugin');
            }
            if (!empty($plugin)) {
                $skin = Pheme::init("$plugin.{$blockName}$skin") ?
                    "{$blockName}$skin" : "{$blockName}Default";
            } else {
                $skin = $skin && Pheme::init("{$blockName}$skin") ?
                    "{$blockName}$skin" : "{$blockName}Default";
            }
        } else {
            $skin = $blockName;
        }

        if ($skin != $blockName) {
            PhemeParser::$parseCallStack[] = Pheme::get($skin);
            $result = parent::parse($html, $skin);
            array_pop(PhemeParser::$parseCallStack);
            return $result;
        } else {
            return parent::parse($html, $skin);
        }
    }
}


Pheme::register('NodeTeaser', new NodeTeaserParser(), null, 'NodeView');

// -----------------------------------------------------------------------------
// Default teaser: the teaser, body field or child nodes whichever is not empty

?>
{if("var":"Node.teaser")}
    <div class="sl-node-teaser">
        {var}CmsNode.markdown_teaser{/var}
    </div>
    <p class="sl-node-more"><a href="{$href}">{t}Read more...{/t}</a></p>
{elseIf("var":"CmsNode.body")}
    <div class="sl-node-teaser">
        {var}CmsNode.markdown_body{/var}
    </div>
{else}
	{NodeChildren("skin":"list")/}
{/if}
<?php

Pheme::registerOutputBuffer('NodeTeaserDefault', 'NodeTeaser', 'NodeView');



// ================================ NodeBody ===================================



class NodeBodyParser extends NodeTeaserParser { }

Pheme::register('NodeBody', new NodeBodyParser(), null, 'NodeView');

// -----------------------------------------------------------------------------
// Default node body: Body field or child nodes (if body is empty)

?>
<div class="sl-node-body">
	{if("var":"CmsNode.body")}
		{var}CmsNode.markdown_body{/var}
	{else}
		{NodeChildren/}
	{/if}
</div>
<?php

Pheme::registerOutputBuffer('NodeBodyDefault', 'NodeBody', 'NodeView');
