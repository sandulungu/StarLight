<?php

/**
 * Image ('link' => true, 'id' => null, 'index' => null, 'filter' => 'thumb', 'var' => 'Image',
 *  'width' => null, 'height' => null, 'method' => 'fitCrop', 'gravity' => 'center', 'rel' => 'prettyPhoto')
 *      $href, $align, $title, $src
 *
 * ImageGallery ('link' => true, 'filter' => 'thumb',
 *  'width' => null, 'height' => null, 'method' => null)
 *      loop -> GalleryImage
 *
 * The method param could be 'fitCrop', 'fitInside', 'fitOutside' or any other method of ImageMedium
 *
 * -----------------------------------------------------------------------------
 * The image is a prettyPhoto-friendly div with an IMaGe that may be aligned as desired
 */

Pheme::init('JqueryColorbox');

?>
{JqueryColorbox/}
<div class='sl-image {if("var":"align")}sl-align-{$align}{/if}'>
    {if("var":"href")}
        <a rel="{$rel}" href="{$href}" title="{$title}">
            <img src="{webroot}{$src}{/webroot}" alt="{$title}" />
        </a>
    {else}
        <img src="{$src}" alt="{$title}" />
    {/if}
</div>
<?php

/**
 * Show an image (from current node or any from DB)
 */
class ImageParser extends PhemeParser {

    protected function _thumb($sourceFilename, $targetFilename, $params) {
        // recursively create folders
        new Folder(dirname($targetFilename), true);

        // Import phpThumb class
		App::import('Vendor', 'phpthumb', array('file' => 'phpThumb'.DS.'phpthumb.class.php'));

		// Configuring thumbnail settings
		$phpThumb = new phpthumb;
		$phpThumb->setSourceFilename($sourceFilename);

    	$phpThumb->w = $params['width'];
		$phpThumb->h = $params['height'];

		$phpThumb->setParameter('zc', $params['zoomCrop']);
		$phpThumb->q = $params['quality'];

		$imageArray = explode(".", $source);
		$phpThumb->config_output_format = $imageArray[1];
		unset($imageArray);

		// Setting whether to die upon error
		$phpThumb->config_error_die_on_error = true;
        
		// Creating thumbnail
		if ($phpThumb->GenerateThumbnail()) {
			if (!$phpThumb->RenderToFile($targetFilename)) {
				trigger_error('Could not render image to: ' . $targetFilename);
			}
		}
    }

    function parse($html = null, $blockName = 'document', $blockParams = null, $noCycle = false) {
        $this->vars['href'] = null;

        $blockParams = (array)$blockParams;
        $blockParams += array(
            'align' => false,
            'var' => 'CmsImage',
            'thumb' => 'icon',
            'zoomCrop' => false,
            'quality' => 75,
            'link' => empty($blockParams['filter']) || $blockParams['filter'] !== 'full' ? 'auto' : false,
            'rel' => empty($blockParams['href']) ? 'colorbox' : null,
			'href' => null,
        );
        $autoLink = $blockParams['link'] == 'auto';

        if (!empty($blockParams['id'])) {
            $image = ClassRegistry::init('Image');
            list($data) = $image->find('first', array(
                'conditions' => array('id' => $blockParams['id']),
                'recursive' => -1,
            ));
        }
        elseif (isset($blockParams['index'])) {
            $data = $this->_getVar("ImageGallery.{$blockParams['index']}");
        }
        else {
            $data = $this->_getVar($blockParams["var"]);
        }

        if (!empty($data['filename'])) {

            // support for skin-specific filter versions
            if (!empty($blockParams['width']) && !empty($blockParams['height'])) {
                $blockParams['thumb'] = $filter = "{$blockParams['width']}x{$blockParams['height']}{$blockParams['crop']}";
                $targetFilename = r('/', DS, WWW_ROOT . "files/cms_images/thumb/{$blockParams['thumb']}/{$data['filename']}");

                // check for file
                if (!file_exists($targetFilename)) {
                    $sourceFilename = WWW_ROOT . "files/cms_images/{$data['filename']}";
                    $this->_thumb($sourceFilename, $targetFilename, $blockParams);
                }
            }

            $src = "files/cms_images/thumb/{$blockParams['thumb']}/{$data['filename']}";
			if ($blockParams['href']) {
				$href = h(r(' ', '+', Sl::url($blockParams['href'])));
			}
            elseif ($blockParams['link']) {
                if (!$autoLink || !$this->_getVar('href')) {
                    $href = $this->_getHelper('SlHtml')->webroot("files/cms_images/{$data['filename']}");
                }
            }
        }

        if (!empty($src)) {
            $this->vars['align'] = $blockParams['align'];
            $this->vars['rel'] = empty($href) ? ($autoLink ? null : false) : $blockParams['rel'];
            $this->vars['title'] = empty($data['title']) ? false : $data['title'];
            $this->vars['src'] = $src;
            $this->vars['href'] = empty($href) ? ($autoLink ? null : false) : $href;

            return parent::parse($html, $blockName);
        }
    }
}

Pheme::registerOutputBuffer('Image', new ImageParser(), true);



// -----------------------------------------------------------------------------
// Image gallery has a table with 3 cols with image thumbs and thir titles

?>
{loop("groupTag":"tr", "itemTag":"td", "showEmpty":true)}
    <table class="sl-image-gallery">
        <tr>
            <td width="33%">{GalleryImage/} {if("var":"title")}<div class="sl-image-title">{$title}</div>{/if}</td>
            <td width="33%">{GalleryImage/} {if("var":"title")}<div class="sl-image-title">{$title}</div>{/if}</td>
            <td width="33%">{GalleryImage/} {if("var":"title")}<div class="sl-image-title">{$title}</div>{/if}</td>
        </tr>
        <tr><td class="sl-empty">&nbsp;</td><td class="sl-empty">&nbsp;</td><td class="sl-empty">&nbsp;</td></tr>
    </table>
{/loop}
<?php

/**
 * Loop through the images of a node
 */
class ImageGalleryLoopParser extends PhemeLoopParser {
    protected function _parseItem($html, $vars, $blockName = 'document', $blockParams = null) {

        // we need this ajustment to call {Image/} block w/o any params
        $vars = Set::merge($vars, array('Image' => $vars));

        return parent::_parseItem($html, $vars, $blockName, $blockParams);
    }
}

/**
 * Gallery-compatible sibling of the SLImageParser
 */
class ImageGalleryItemParser extends PhemeSubParser {
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        return Pheme::get('Image')->parse($html, 'Image', am($this->params[0], $blockParams));
    }
}

/**
 * Show a gellery of images
 */
class ImageGalleryParser extends PhemeParser {

    function __construct($rules = array(), $options = array()) {
        $this->blocks["loop"] = new ImageGalleryLoopParser();
		$this->blocks["loop"]->blocks['GalleryImage'] = new ImageGalleryItemParser();

        parent::__construct($rules, $options);
    }

    function parse($html = null, $blockName = 'document', $blockParams = null, $noCycle = false) {
        if (!empty($blockParams['nodeId'])) {
            $image = ClassRegistry::init('Image');
            $data = $image->find('all', array(
                'conditions' => array(
                    'node_id' => $blockParams['nodeId'],
                    'publish' => true,
                ),
                'recursive' => -1,
            ));
            $this->blocks["loop"]->params[0] =& $data;

        } else {
            $this->blocks["loop"]->params[0] = $this->_getVar('ExtraImages');
        }

        // {GalleryImage/} blocks will use {ImageGallery/}'s parameters as defaults
        $this->blocks["loop"]->blocks['GalleryImage']->params[0] = $blockParams;

        return parent::parse($html, $blockName);
    }
}

Pheme::registerOutputBuffer('ImageGallery', new ImageGalleryParser(), true);
