<?php

/**
 * Main (X)HTML helper, extends Cake's HtmlHelper
 *
 * @property-read FormHelper $Form
 */
class SlHtmlHelper extends AppHelper {

    /**
     * @var array
     */
    protected $_emptyTags = array('img', 'br', 'hr', 'input', 'button', 'embed', 'param');

    /**
     * Automagic method...
     *
     * Renders any HTML tag, specified as the $method name
     *
     * @param string $tag
     * @param array $args
     * @return string Html
     */
    public function __call($tag, $args) {
        
        // class or id
        if (!empty($args[0]) && is_string($args[0]) && preg_match('!^(#[A-Za-z0-9_-]+|\.[A-Za-z0-9_ -]+)$!', $args[0])) {
            $classOrId = array_shift($args);
        }

        // inner HTML
        if (!in_array($tag, $this->_emptyTags)) {
            $text = array_shift($args);
			if (is_array($text)) {
				$text = implode('', $text);
			}
            if ($text === null) {
                $text = '&nbsp;';
            }
        }
        $attributes = array_shift($args);

        // html attributes
        if (isset($classOrId)) {
            if ($classOrId{0} === '#') {
                $attributes['id'] = substr($classOrId, 1);
            }
            elseif ($classOrId{0} === '.') {
                $attributes = $this->addClass($attributes, substr($classOrId, 1));
            }
        }
        if ($attributes) {
            $attributes = $this->_parseAttributes($attributes);
        }
        
        if (isset($text)) {
            $result = "<$tag{$attributes}>$text</$tag>";
        } else {
            $result = "<$tag{$attributes} />";
        }
        return $result;
    }

    /**
     * Creates a link element for CSS stylesheets.
     *
     * @param mixed $url The name of a CSS style sheet or an array containing names of
     *   CSS stylesheets. If `$path` is prefixed with '/', the path will be relative to the webroot
     *   of your application. Otherwise, the path will be relative to your CSS path, usually webroot/css.
     * @param string $rel Rel attribute. Defaults to "stylesheet". If equal to 'import' the stylesheet will be imported.
     * @param array $options Array of HTML attributes.
     * @param boolean $inline If set to false, the generated tag appears in the head tag of the layout.
     * @return string CSS <link /> or <style /> tag, depending on the type of link.
     */
	function css($url, $rel = null, $options = array()) {
        $url = $this->assetUrl($url, 'css');

		if ($rel === 'import') {
			$out = sprintf('<style type="text/css"%s>%s</style>', $this->_parseAttributes($options, null, '', ' '), '@import url(' . $url . ');');
		} else {
			if (empty($rel)) {
				$rel = 'stylesheet';
			}
			$out = sprintf('<link rel="%s" type="text/css" href="%s" %s/>', $rel, $url, $this->_parseAttributes($options, null, '', ' '));
		}
		return $out;
	}

    public function actionLink($action, $id = null, $options = array()) {
        $options += array(
            'title' => __t(Inflector::humanize($action)),
            'url' => array(),
        );

        switch ($action) {
            case 'clone':
                $url = array('action' => 'add');
                break;

            case 'preview':
                $url = array('admin' => false, 'action' => 'view');
                break;

            default:
                $url = array('action' => $action);
        }

        if ($id) {
            if (is_array($id)) {
                $url = $id + $url;
            } else {
                $url[] = $id;
            }
        }
        $options['url'] += $url;

        switch ($action) {
            case 'add':
            case 'clone':
                $options += array(
                    'class' => 'add',
                );
                break;

            case 'edit':
                $options += array(
                    'class' => 'edit',
                );
                break;

            case 'delete':
                $options += array(
                    'confirm' => __t('Delete?'),
                    'class' => 'remove',
                );
                break;
        }
        
        $title = $options['title'];
        $url = $options['url'];
        unset($options['title']);
        unset($options['url']);
        return $this->link($title, $url, $options);
    }

    /**
     * Creates an HTML link.
     *
     * If $url starts with "http://" this is treated as an external link. Else,
     * it is treated as a path to controller/action and parsed with the
     * HtmlHelper::url() method.
     *
     * If the $url is empty, $title is used instead.
     *
     * @param  mixed  $title The content to be wrapped by <a> tags or a node $item.
     * @param  mixed   $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
     * @param  array   $options Array of HTML attributes.
     * @param  string  $confirmMessage JavaScript confirmation message.
     * @param  boolean $escapeTitle	Whether or not $title should be HTML escaped.
     * @return string	An <a /> element.
     */
    function link($title, $url = null, $options = array()) {

        if (Sl::isHere($url)) {
            $options = $this->addClass($options, 'sl-active');
        }

        if ($url !== null) {
			$url = ($url !== false) ? $this->url($url) : '';
		} else {
			$url = $this->url($title);
			$title = $url;
		}

		if (!empty($options['escape'])) {
			$title = h($title);
		}

		if (!empty($options['confirm'])) {
			$confirmMessage = r("'", "\'", r('"', '\"', $options['confirm']));
            $url2 = r("'", "\'", r('"', '\"', $url));
			$options['onclick'] = "if (window.Ext) { Ext.Msg.confirm('', '$confirmMessage', function(btn){ if (btn == 'yes') Sl.go('$url2'); }); return false; } return confirm('{$confirmMessage}');";
            unset($options['confirm']);
		}
        
		return $url ? 
            sprintf('<a href="%s"%s>%s</a>', $url, $this->_parseAttributes($options), $title) :
            sprintf('<a%s>%s</a>', $this->_parseAttributes($options), $title);
    }

}
