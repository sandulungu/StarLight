<?php

class PreloadImageParser extends PhemeParser {
    
    function parse($html = null, $blockName = 'document', $blockParams = null) {
        if (empty($html)) {
            return;
        }
        
        $images = Set::normalize($html, false);
        foreach ($images as &$image) {
            if (!strpos($image, '://') && $image{0} != '/') {
                $image = "img/$image";
            }
            $image = $this->_getHelper('SlHtml')->webroot($image);
            SlConfigure::append("Asset.js.ready", "$.preloadImages($images)");
        }
        
        SlConfigure::write('Asset.js.footer.imagePreloader.after',
<<<end
jQuery.preLoadImagesCache = [];

// Arguments are image paths relative to the current page.
jQuery.preLoadImages = function() {
    var args_len = arguments.length;
    for (var i = args_len; i--;) {
        var cacheImage = document.createElement('img');
        cacheImage.src = arguments[i];
        jQuery.preLoadImagesCache.push(cacheImage);
    }
}
end
        );
    }
}

Pheme::register('PreloadImage', new PreloadImageParser(), null, true);
