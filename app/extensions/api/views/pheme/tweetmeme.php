<?php
/**
 * Tweetmeme (Retweet) Button
 * Follow Button
 *
 * @link http://help.tweetmeme.com/2009/04/06/tweetmeme-button/ Tweetmeme Button
 * @link http://help.tweetmeme.com/2010/02/23/follow-button/ Follow Button
 */

?>
<script type="text/javascript">
	tweetmeme_url = '{$url}';
	{if("var":"style")}tweetmeme_style = '{$style}';{/if}
	tweetmeme_source = '{$source}';
</script>
<script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
<?php

class TweetmemeRetweetButtonParser extends PhemeParser {

    function parse($html = null, $blockName = 'document', $blockParams = null) {

	    $blockParams = (array)$blockParams;
	    $blockParams += array(
		    'url' => SL::url(SL::nodeUrl($this->_getVar('Node'))),
		    'style' => null, // 'compact',
		    'source' => 'tweetmeme',
	    );
		
		$this->vars = $blockParams;
	    return parent::parse($html, $blockName);
    }
}

Pheme::registerOutputBuffer('TweetmemeRetweetButton', new TweetmemeRetweetButtonParser(), true);



// -----------------------------------------------------------------------------

?>
<script type="text/javascript">
	tweetmeme_size = '{$size}';
	{if("var":"style")}tweetmeme_style = '{$style}';{/if}
	tweetmeme_screen_name = '{$source}';
</script>
<script type="text/javascript" src="http://tweetmeme.com/i/scripts/follow.js"></script>
<?php

class TweetmemeFollowButtonParser extends PhemeParser {

    function parse($html = null, $blockName = 'document', $blockParams = null) {

	    $blockParams = (array)$blockParams;
	    $blockParams += array(
		    'size' => 32,
		    'style' => null, // 'compact', 'square'
		    'source' => 'tweetmeme',
	    );

		$this->vars = $blockParams;
	    return parent::parse($html, $blockName);
    }
}

Pheme::registerOutputBuffer('TweetmemeFollowButton', new TweetmemeFollowButtonParser(), true);
