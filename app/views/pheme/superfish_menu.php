<?php

/**
 * jQuery superfish (+ supersubs) plugin
 *
 * SuperfishMenu ('type' => 'default', 'menuClass' => 'sf-menu',
 *  'supersubs' => array(...), 'options' => array() )
 *
 * SuperfishMenu will pass all other params to the (parent) Menu block.
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
{if("var":"level","value":1)}
    <div class="sl-clear">&nbsp;</div>
{/if}
<?php

class SuperfishMenuParser extends PhemeParser {
    function parse($html = null, $blockName = 'document', $blockParams = null) {

        // set defaults
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'type' => 'default',
            'supersubs' => array(
                'extraWidth' => 1.5
            ),
            'options' => array(),
            'menuClass' => 'sf-menu',
            'domId' => Sl::uniqid(),
        );
        $id = $blockParams['domId'];
        $blockParams['menuClass'] .= " $id";

        // load & configure supersubs
        if ($blockParams['supersubs']) {
            $ssOptions = json_encode($blockParams['supersubs']);
            $ssKey = "supersubs_$id";
            SlConfigure::write("Asset.js.footer.supersubs", 'superfish/js/supersubs');
            SlConfigure::write("Asset.js.ready.$ssKey", "$('ul.$id').supersubs($ssOptions);");
        }

        // load & configure superfish
        $sfOptions = $blockParams['options'] ? json_encode($blockParams['options']) : '';
        $sfKey = "superfish_$id";
        SlConfigure::write("Asset.css.superfish", 'superfish/css/superfish');
        SlConfigure::write("Asset.js.footer.superfish",'superfish/js/superfish');
        SlConfigure::write("Asset.js.ready.$sfKey", "$('ul.$id').superfish($sfOptions);");

        // alternative styles
        switch ($blockParams['type']) {
            case 'vertical':
                $blockParams['menuClass'] .= " sf-vertical";
                SlConfigure::write("Asset.css.superfishVertical", 'superfish/js/superfish-vertical');
                break;
            case 'navbar':
                $blockParams['menuClass'] .= " sf-navbar";
                SlConfigure::write("Asset.css.superfishNavbar", 'superfish/js/superfish-navbar');
                break;
        }

        unset($blockParams['type']);
        unset($blockParams['supersubs']);
        unset($blockParams['options']);

        // construct DOM
        return Pheme::init('Menu')->parse($html, $blockName, $blockParams);
    }
}

Pheme::registerOutputBuffer('SuperfishMenu', new SuperfishMenuParser(), true);
