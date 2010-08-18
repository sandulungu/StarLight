<?php

/**
 * Blocks ('id' => ...)
 *      $id
 *      loop -> $id, $title, $body
 */

?>
<div class="sl-block-container sl-block-container-{$id}">
    {loop}
        <div class="sl-block sl-block-{$id}">
            {if("var":"title")}
                <h2>{e}{$title}{/e}</h2>
            {/if}
            <div class="sl-block-body">{$body}</div>
        </div>
    {!sep:loop} <hr class="sl-block-separator" />
    {/loop}
</div>
<?php

class BlocksParser extends PhemeParser {

    function __construct($rules = array(), $options = array()) {
        $this->blocks["loop"] = new PhemeLoopParser();
    }

    function parse($html = null, $blockName = 'document', $blockParams = null) {
        if (empty($blockParams['id'])) {
            if (preg_match('/^[a-zA-Z0-9_.-]+$/', $html)) {
                $blockParams['id'] = parent::parse($html);
                $html = null;
            }
            else {
                return;
            }
        }
        $id = $blockParams['id'];
        
        $cacheKey = empty($blockParams['cacheKey']) ? 'default' : $blockParams['cacheKey'];

        $data = SlConfigure::read2("Block.$id");

        if ($data) {
            $id = r('.', '-', $id);
            $this->vars = array(
                'id' => $id,
                'title' => false,
            );

            $defaults = SlConfigure::read2('Block.defaults');

            $blocks = array();
            foreach($data as $key => $block) {
                if ($block) {
                    if (!is_array($block)) {
                        $block = array('body' => $block);
                    }
                    $block = Set::merge($defaults, $block);

                    if (empty($block['id'])) {
                        $block['id'] = "$id-$key";
                    }

                    if (!empty($block['cache']) && !is_array($block['cache'])) {
                        $block['cache'] = array('time' => $block['cache']);
                    }
                    if (!empty($block['cache_time'])) {
                        $block['cache']['time'] = $block['cache_time'];
                    }
                    if (isset($block['cache']['time']) && is_numeric($block['cache']['time'])) {
                        $block['cache']['time'] += time();
                    }
                    
                    if (!empty($block['cache']['spread'])) {
                        if (!is_numeric($block['cache']['time'])) {
                            $block['cache']['time'] = strtotime($block['cache']['time'], time());
                        }
                        $block['cache']['time'] += mt_rand(-$block['cache']['spread'], $block['cache']['spread']);
                    }

                    if (!empty($block['cache']) && empty($block['cache']['key'])) {
                        $block['cache']['key'] = $block['id'].'-'.md5(serialize($block));
                    }

                    if (!empty($block['cache']['time'])) {
                        $cacheFile = 'views/block_' . $cacheKey .'_'. $block['cache']['key'];
                        $cache = cache($cacheFile, null, $block['cache']['time']);

                        if (is_string($cache)) {
                            $blocks[] = unserialize($cache);
                            continue;
                        }
                    }

                    // dynamic block (from custom controller)
                    if (!empty($block['url'])) {
                        $block['body'] = Sl::requestAction($block['url']);
                        if (!isset($block['title'])) {
                            $block['title'] = Sl::r('View._pageTitle');
                        }
                    }

                    elseif (!empty($block['body'])) {
                        $block['body'] = parent::parse($block['body']);
                    } else {
                        continue;
                    }

                    $blocks[] = $block;

                    // update cache
                    if (!empty($block['cache']['time'])) {

                        // we don't wanna cache administrative stuff
                        if (!strpos($block['body'], 'sl-node-actions')) {

                            cache($cacheFile, serialize($block), $block['cache']['time']);
                        }
                    }
                }
            }

            if (empty($blocks)) {
                return;
            }
            $this->blocks["loop"]->params[0] = $blocks;
            return parent::parse($html, $blockName);
        }
    } // parse(...)
}

Pheme::registerOutputBuffer('Blocks', new BlocksParser(), true);
