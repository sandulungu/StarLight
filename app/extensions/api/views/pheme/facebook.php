<?php

/**
 * Facebook XFBML
 */

class FacebookParser extends PhemeParser{
	function parse($html = null, $blockName = 'document', $blockParams = null){
		$blockParams = (array)$blockParams;
        $blockParams += array(
            'XFBML' => true,
            'name' => '',
        );

		if ($blockParams['name'] && $blockParams['XFBML']){
			$name = $blockParams['name'];
			return $this->XFBML(
				$name,
				array_diff_key(
					$blockParams,
					array(
						'name' => 1,
						'XFBML' => 1,
					)
				)
			);
		}

        $this->vars = $blockParams;
        return parent::parse($html, $blockName);
	}

	function XFBML($name, $params){
		SL::config()->JS->Link->FacebookJavaScriptSDK = 'http://connect.facebook.net/en_US/all.js';

		$paramsStr = "";
		foreach($params as $key => $value) {
            if (empty($value)) {
                continue;
            }
            
            $value = h($value);
			$paramsStr .= "$key='$value' ";
		}

		$result = "<fb:$name $paramsStr></fb:$name.>";
		return $result;
	}
}
Pheme::register('Facebook', new FacebookParser(), null, true);

// -----------------------------------------------------------------------------

/**
 * Facebook Activity Feed
 *  @param int $width activity feed width
 *  @param int $height activity feed height
 *  @param bool $header show/hide header
 *  @param string $colorScheme light or dark
 *  @param string $font arial, lucida grande, segoie ui, tahoma, verdana, trebuchet ms
 *  @param string $border_color
 */

?>
<iframe
	src="http://www.facebook.com/plugins/activity.php?site={$site}&amp;width={$width}&amp;height={$height}&amp;header={$header}&amp;colorscheme={$colorscheme}&amp;font={$font}&amp;border_color={$border_color}"
	scrolling="no"
	frameborder="0"
	style="border:none; overflow:hidden; width:{$width}px; height:{$height}px;"
	allowTransparency="true">
</iframe>
<?php

class FacebookActivityParser extends FacebookParser {
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'XFBML' => false,
            'name' => 'activity',
            'site' => SL::url(SL::nodeUrl($this->_getVar('Node'))),
            'width' => 300,
            'height' => 300,
            'header' => false,
            'colorscheme' => 'light', // dark
            'font' => 'tahoma',
            'border_color' => '#fff',
        );
		
		$blockParams['header'] = $blockParams['header'] ? 'true' : 'false';
        if (empty($blockParams['XFBML'])) {
            $blockParams['border_color'] = urlencode($blockParams['border_color']);
            $blockParams['site'] = urlencode($blockParams['site']);
			$blockParams['font'] = urlencode($blockParams['font']);
        }
		return parent::parse($html, $blockName, $blockParams);

    }
}

Pheme::registerOutputBuffer('FacebookActivityFeed', new FacebookActivityParser(), true);



// -----------------------------------------------------------------------------

/**
 * Facebook Comments
 * @param int $numposts - number of comments per page
 * @param int $width - witdh of comment box
 */

 class FacebookCommentsParser extends FacebookParser {
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'name' => 'comments',
            'site' => SL::url(SL::nodeUrl($this->_getVar('Node'))),
            'width' => 550,
            'numposts' => 10,
        );

    	return parent::parse($html, $blockName, $blockParams);
    }
}

Pheme::registerOutputBuffer('FacebookComments', new FacebookCommentsParser(), true);



// -----------------------------------------------------------------------------

/**
 * Facebook Facepile
 * @param int $max-rows - number of rows
 * @param int $witdh - witdh of comment box
 */

 class FacebookFacepileParser extends FacebookParser {
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'name' => 'facepile',
            'width' => 200,
            'max-rows' => 1,
        );
    	return parent::parse($html, $blockName, $blockParams);
    }
}

Pheme::registerOutputBuffer('FacebookFacepile', new FacebookFacepileParser(), true);



//------------------------------------------------------------------------------

/**
 * Facebook Like Button
 * @param string $href
 * @param string $layout standart/button_count
 * @param bool $show_faces
 * @param int $width
 * @param string $action like/recomended
 * @param string $font
 * @param string $colorscheme light/dark
 */

 ?>
<iframe
	src="http://www.facebook.com/plugins/like.php?href={$href}&amp;layout={$layout}&amp;show_faces={$show_faces}&amp;width={$width}&amp;action={$action}&amp;font={$font}&amp;colorscheme={$colorscheme}"
	scrolling="no"
	frameborder="0"
	style="border:none; overflow:hidden; width:{$width}px;"
	allowTransparency="true">
</iframe>
<?php

class FacebookLikeButtonParser extends FacebookParser {
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'XFBML' => false,
            'name' => 'like',
            'href' => SL::url(SL::nodeUrl($this->_getVar('Node'))),
            'width' => 450,
            'layout' => 'standart',//button_count
            'show_faces' => true,
            'colorscheme' => 'light', // dark
            'font' => 'tahoma',
            'action' => 'like', //recommended
        );
        
		$blockParams['show_faces'] = $blockParams['show_faces'] ? 'true' : 'false';

		if (empty($blockParams['XFBML'])) {
            $blockParams['href'] = urlencode($blockParams['href']);
            $blockParams['layout'] = urlencode($blockParams['layout']);
			$blockParams['font'] = urlencode($blockParams['font']);
        }
		return parent::parse($html, $blockName, $blockParams);

    }
}

Pheme::registerOutputBuffer('FacebookLikeButton', new FacebookLikeButtonParser(), true);



//------------------------------------------------------------------------------

/**
 * Facebook Like Box
 * @param int $profile_id profile id
 * @param int $connections
 * @param int $width
 * @param int $height
 * @param bool $stream
 * @param bool $header
 */

?>
<iframe
	src="http://www.facebook.com/plugins/likebox.php?profile_id={$profile_id}&amp;width={$width}&amp;height={$height}&amp;connections={$connections}&amp;stream={$stream}&amp;header={$header}"
	scrolling="no"
	frameborder="0"
	style="border:none; overflow:hidden; width:{$width}px; height:{$height}px;"
	allowTransparency="true">
</iframe>
<?php

class FacebookLikeBoxParser extends FacebookParser {
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'XFBML' => false,
            'name' => 'like-box',
            'connections' => 10,
            'width' => 292,
            'height' => 300,
            'stream' => true,
            'header' => true,
        );
        if (isset($blockParams['id'])) {
            $blockParams['profile_id'] = $blockParams['id'];
        }

		$blockParams['stream'] = $blockParams['stream'] ? 'true' : 'false';
		$blockParams['header'] = $blockParams['header'] ? 'true' : 'false';
		return parent::parse($html, $blockName, $blockParams);

    }
}

Pheme::registerOutputBuffer('FacebookLikeBox', new FacebookLikeBoxParser(), true);



//------------------------------------------------------------------------------

/**
 * Facebook Live Stream
 * @param int $app_id app id
 * @param int $xid
 * @param int $width
 * @param int $height
 */

?>
<iframe
	src="http://www.facebook.com/plugins/livefeed.php?app_id={$app_id}&amp;width={$width}&amp;height={$height}&amp;xid={$xid}"
	scrolling="no"
	frameborder="0"
	style="border:none; overflow:hidden; width:{$width}px; height:{$height}px;"
	allowTransparency="true">
</iframe>
<?php

class FacebookLiveStreamParser extends FacebookParser {
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'XFBML' => true,
            'name' => 'live-stream',
            'width' => 400,
            'height' => 500,
        );
        if (isset($blockParams['id'])) {
            $blockParams['app_id'] = $blockParams['id'];
        }

		return parent::parse($html, $blockName, $blockParams);
    }
}

Pheme::registerOutputBuffer('FacebookLiveStream', new FacebookLiveStreamParser(), true);



//------------------------------------------------------------------------------

/**
 * Facebook Login Button
 * @param int $width
 * @param int $max-rows of profile picture
 * @param bool $show-faces defaul true
 */

class FacebookLoginButtonParser extends FacebookParser {
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'name' => 'login-button',
            'width' => 200,
			'show-faces' => true,
			'max-rows' => 1,
        );

		$blockParams['show_faces'] = $blockParams['show_faces'] ? 'true' : 'false';
    	return parent::parse($html, $blockName, $blockParams);
    }
}

Pheme::registerOutputBuffer('FacebookLoginButton', new FacebookLoginButtonParser(), true);



//------------------------------------------------------------------------------

/**
 * Facebook Recommendations
 *  @param int $width activity feed width
 *  @param int $height activity feed height
 *  @param string $font arial, lucida grande, segoie ui, tahoma, verdana, trebuchet ms
 *  @param string $border_color
 */

?>
<iframe
	src="http://www.facebook.com/plugins/recommendations.php?site={$site}&amp;width={$width}&amp;height={$height}&amp;header={$header}&amp;colorscheme={$colorscheme}&amp;font={$font}&amp;border_color={$border_color}"
	scrolling="no"
	frameborder="0"
	style="border:none; overflow:hidden; width:{$width}px; height:{$height}px;"
	allowTransparency="true">
</iframe>
<?php

class FacebookRecommendationsParser extends FacebookParser {
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'XFBML' => false,
            'name' => 'recommendations',
            'site' => SL::url(SL::nodeUrl($this->_getVar('Node'))),
            'width' => 300,
            'height' => 300,
            'header' => false,
            'colorscheme' => 'light', // dark
            'font' => 'tahoma',
            'border_color' => '#fff',
        );

        if (empty($blockParams['XFBML'])) {
            $blockParams['border_color'] = urlencode($blockParams['border_color']);
            $blockParams['site'] = urlencode($blockParams['site']);
			$blockParams['font'] = urlencode($blockParams['font']);
        }
		return parent::parse($html, $blockName, $blockParams);

    }
}

Pheme::registerOutputBuffer('FacebookRecommendations', new FacebookRecommendationsParser(), true);
