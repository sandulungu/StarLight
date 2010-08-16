<?php

/**
 * Menu, SimpleMenu ('items' => array(), 'id' => 'links', 'menuClass' => null,
 *  'class' => 'sl-menu', 'itemClass' => 'sl-menu-item'):
 *      $class, $id
 *      loop -> $id, $itemClass, $text, $href, link ($link)
 */

?>
{loop("itemTag":"li")}
    <ul class="{$class}">
        <li>
            {if("var":"link")}
                {MenuLink}{$text}{/MenuLink}
            {elseIf("var":"text", "value":"-")}
                <hr />
            {else}
                <span class="{$itemClass}">{$text}</span>
            {/if}
            {$subItems}
        </li>
    </ul>
{/loop}
<?php

/**
 * Serves {link} block
 */
class MenuLinkParser extends PhemeParser {
    function parse($html = null, $blockName = 'document', $blockParams = null) {
        return sprintf($this->_getVar('link'), parent::parse($html, $blockName, $blockParams));
    }
}

/**
 * Serves {Menu} and {SimpleMenu} blocks
 */
class MenuParser extends PhemeParser {

    function __construct($rules = array(), $options = array()) {
        $this->blocks["loop"] = new PhemeLoopParser();
        $this->blocks["loop"]->blocks['MenuLink'] = new MenuLinkParser();

        parent::__construct($rules, $options);
    }

    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'recursive' => $blockName !== 'SimpleMenu' ? -1 : 0,
            'level' => 1,
            'itemClass' => 'sl-menu-item',
            'menuClass' => null,
            'class' => 'sl-menu',
        );

        // get menu items
        if (!isset($blockParams['items'])) {
            if (!isset($blockParams['id'])) {
                if (preg_match('/^[a-zA-Z0-9_.{}\/-]+$/', $html)) {
                    $name = parent::parse($html);
                    $html = null;
                }
                else {
                    $name = 'cms';
                }
            }
            elseif(preg_match('/^[0-9]+(-[0-9]+)*$/', $blockParams['id'])) {
                $name = 'cms.' . r('-', '.children.', $blockParams['id']) . '.children';
            } else {
                $name = $blockParams['id'];
            }
            $blockParams['items'] = SlConfigure::read2("Navigation.$name");
        }

        $parentId = empty($blockParams['id']) ? '' : $blockParams['id'].'-';

        if (!is_array($blockParams['items']) || !is_array(reset($blockParams['items']))) {
            return;
        }

        $keys = array_keys($blockParams['items']);
        $first = reset($keys);
        $last = end($keys);

        $results = array();
        foreach ($blockParams['items'] as $key => $item) {
            $id = $key;
            $args = array();

            // default template vars values
            $result = array(
                'itemClass' => "{$blockParams['itemClass']}-child {$blockParams['itemClass']}-$id",
                'link' => false,
                'subItems' => false,
                'href' => false,
                'id' => $parentId.$id,
            );
            if ($key == $first) {
                $result['itemClass'] .= ' '.$blockParams['itemClass'].'-first';
            }
            if ($key == $last) {
                $result['itemClass'] .= ' '.$blockParams['itemClass'].'-last';
            }

            // text items
            if (!is_array($item) && $item) {
                $item = array('title' => $item);
            }

            // NavigationLink data arrays
            if (isset($item['NavigationLink'])) {
                $item = am($item, $item['NavigationLink']);
                unset($item['NavigationLink']);
            }

            // get link text
            if (!empty($item['title'])) {
                $result['text'] = h(__t($item['title']));
            }
            if (empty($result['text'])) {
                continue;
            }

            // check permissions
            /*if (!SlAuth::isAuthorized($item, null, null, true)) {
                continue;
            }
            unset($item['allow']);
            unset($item['deny']);*/

            // get link url
            $url = empty($item['url']) ? false : $item['url'];

            // set class attributte
            if (!empty($item['class'])) {
                $result['itemClass'] .= ' '.$item['class'];
            }

            // set hint attribute
            if (!empty($item['hint'])) {
                $args['title'] = __t($item['hint']);
            }

            // set onclick attribute
            if (!empty($item['onclick'])) {
                $args['onclick'] = $item['onclick'];
            }

            // set rel attribute
            if (!empty($item['rel'])) {
                $args['rel'] = $item['rel'];
            }

            // set target attribute
            if (!empty($item['target'])) {
                $args['target'] = $item['target'];
            }

            // children?
            if (!empty($item['children']) && $blockParams['recursive'] != 0) {
                $result['subItems'] = $this->parse(
                    $html,
                    $blockName,
                    array(
                        'recursive' => $blockParams['recursive'] - 1,
                        'class' => $blockParams['class'].'-'.$id,
                        'itemClass' => $blockParams['itemClass'].'-'.$id,
                        'items' => $item['children'],
                        'id' => $parentId.$id,
                        'level' => $blockParams['level'] + 1,
                    )
                );
                if (strpos($result['subItems'], 'sl-active')) {
                    $result['itemClass'] .= ' sl-child-active';
                }
            }

            // is this a link?
            if ($url) {
                $args['escape'] = false;
                $args['class'] = $result['itemClass'];
                $result['link'] = $this->_getHelper('SlHtml')->link('%s', $url, $args);
                $result['href'] = $this->_getHelper('SlHtml')->url($url);
                if (strpos($result['subItems'], 'sl-active')) {
                    $result['itemClass'] .= ' sl-active';
                }
            }

            $results[] = $result;
        }

        if (empty($results)) {
            return;
        }
        $this->blocks["loop"]->params[0] = $results;
        $this->vars['class'] = $blockParams['class'];
        $this->vars['level'] = $blockParams['level'];
        if ($blockParams['menuClass']) {
            $this->vars['class'] .= ' '.$blockParams['menuClass'];
        }
        $this->vars['id'] = empty($blockParams['id']) ? false : $blockParams['id'];
        return parent::parse($html, $blockName);
    }
}

Pheme::registerOutputBuffer('Menu', new MenuParser(), true);
