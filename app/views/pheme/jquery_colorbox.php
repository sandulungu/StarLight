<?php

/**
 * Colorbox jQuery plugin
 */

SlConfigure::write("Asset.js.footer.colorbox", 'colorbox/colorbox/jquery.colorbox-min');
SlConfigure::write("Asset.css.colorbox", "colorbox/example2/colorbox");

class JqueryColorboxParser extends PhemeNullParser {
    function parse($html = null, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        if (empty($html)) {
            $html = 'a[rel^="colorbox"]';
        }
        $blockParams += array(
            'skin' => null, // 'example2',
        );
        
        if ($blockParams['skin']) {
           SlConfigure::write("Asset.css.colorbox", "colorbox/{$blockParams['skin']}/colorbox");
        }
        unset($blockParams['skin']);
        
        $options = $blockParams ? json_encode($blockParams) : '';
        $key = "colorbox_".Inflector::slug($html);
        
        SlConfigure::write("Asset.js.ready.$key", "$('$html').colorbox($options);");
    }
}

Pheme::register('JqueryColorbox', new JqueryColorboxParser(), null, true);
