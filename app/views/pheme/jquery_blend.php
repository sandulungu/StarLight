<?php
/** 
 * Blend jQuery plugin: smooth animation of :hover actions
 *
 * Note: Requires user to define an extra <element>.href css class
 */

class JqueryBlendParser extends PhemeNullParser {
    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        if (empty($html)) {
            $html = '.blend';
        }
        $blockParams += array(
            // 'pulse' => false,
            // 'reverse' => false,
        );
        $options = $blockParams ? json_encode($blockParams) : '';

        $key = "blend_".Inflector::slug($html);
        SlConfigure::write("Asset.js.footer.blend", 'blend/jquery.blend-min');
        SlConfigure::write("Asset.js.ready.$key", "$('$html').blend($options);");
    }
}

Pheme::register('JqueryBlend', new JqueryBlendParser(), null, true);
