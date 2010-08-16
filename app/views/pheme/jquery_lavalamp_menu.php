<?php

/**
 * jQuery lavalamp plugin
 */

?>
{loop("itemTag":"li")}
    <ul id="{$domId}" class="{$class}">
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
<div class="sl-clear">&nbsp;</div>
<?php

class JqueryLavalampParser extends PhemeParser {
    function parse($html = null, $blockName = 'document', $blockParams = null) {

        // set defaults
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'options' => array(
                'fx' => 'backout',
            ),
            'menuClass' => 'lavaLampNoImage',
            'domId' => Sl::uniqid(),
            'recursive' => 0,
        );
        $this->vars['domId'] = $id = $blockParams['domId'];

        // load & configure lavalamp
        $options = $blockParams['options'] ? json_encode($blockParams['options']) : '';
        SlConfigure::write('Asset.css.lavalamp', 'lavalamp/lavalamp_test');
        SlConfigure::write('Asset.js.footer.lavalamp', 'lavalamp/jquery.lavalamp.min');
        SlConfigure::write("Asset.js.ready.$id", "$('#$id').lavaLamp($options);");

        // custom fx easing effects
        if (!in_array($blockParams['options']['fx'], array('swing', 'linear'))) {
            SlConfigure::write('Asset.js.footer.easing', 'lavalamp/jquery.easing.min');
        }
        
        unset($blockParams['options']);

        // construct DOM
        self::$parseCallStack[] = $this;
        $html = Pheme::init('Menu')->parse($html, $blockName, $blockParams);
        array_pop(self::$parseCallStack);
        return $html;
    }
}

Pheme::registerOutputBuffer('JqueryLavalamp', new JqueryLavalampParser(), true);
