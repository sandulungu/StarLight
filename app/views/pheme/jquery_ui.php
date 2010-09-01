<?php

/**
 * Jquery UI
 */

SlConfigure::write('Asset.js.footer.jqueryUi', array(
    'weight' => -50,
    'url' => 'jquery-ui/js/jquery-ui-1.8.4.custom.min',
));
SlConfigure::write('Asset.css.jqueryUi', 'jquery-ui/css/smoothness/jquery-ui-1.8.4.custom');

?>
<div id="{$domId}"></div>
<?php

class JqueryUiSliderParser extends PhemeParser{
     function parse($html = null, $blockName = 'document', $blockParams = null) {
         $blockParams = (array)$blockParams;
         $blockParams += array(
            'range' => true,
            'min' => 0,
            'max' => 100,
			'values' => array(0,100),
			'domId' => SL::uniqid(),
        );

		$options = $blockParams;
		unset($options['domId']);
		$key = "jqueryUiSlider-{$blockParams['domId']}";
		SlConfigure::write("Asset.js.ready.$key", "$('#{$blockParams['domId']}').slider(".json_encode($options).')');

        return parent::parse($html, $blockName);
     }
}

Pheme::registerOutputBuffer('JqueryUiSlider', new JqueryUiSliderParser, null, true);
