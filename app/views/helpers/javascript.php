<?php

/**
 * Javascript Helper class for easy use of JavaScript.
 *
 * JavascriptHelper encloses all methods needed while working with JavaScript.
 */
class JavascriptHelper extends AppHelper {

    /**
     * HTML tags used by this helper.
     *
     * @var array
     */
	public $tags = array(
		'javascriptblock' => '<script type="text/javascript">%s</script>',
		'javascriptlink' => '<script type="text/javascript" src="%s"></script>',
	);

    /**
     * Wraps the $script in a jQuery document ready callback
     *
     * @param string $script
     * @return string
     */
	public function ready($script){
        // convert an array to a script string
        if (is_array($script)) {
            foreach ($script as &$line) {
                if (substr($line, -1) !== ';') {
                    $line .= ';';
                }
            }
            $script = implode("\n", $script);
        }
        
		return $this->codeBlock("$(function(){ $script\n});");
	}

    /**
     *
     * @return string
     */
    public function multi($scripts) {
        $result = array();
        foreach($scripts as &$script) {
            if (!is_array($script)) {
                $script = array('url' => $script);
            }
            $script += array('weight' => 0);
        }

        $weights = Set::extract('{s}.weight', $scripts);
        $scripts = array_values($scripts);
        array_multisort($weights, SORT_ASC, $scripts);
        //Sl::krumo($scripts);

        foreach($scripts as &$script) {
            if (!empty($script['before'])) {
                $result[] = $this->codeBlock($script['before']);
            }
            if (!empty($script['url'])) {
                $result[] = $this->link($script['url']);
            }
            if (!empty($script['after'])) {
                $result[] = $this->codeBlock($script['after']);
            }
        }
        return implode('', $result);
    }

    /**
     * Json encode
     *
     * @param array $array
     * @param array $keys Unescaped keys list
     * @param char $q Quote keys char
     * @param char $q2 Quote values char
     * @return string
     */
    function object($array, $keys = array(), $q = '"', $q2 = '"') {
        if (empty($keys) && $q === '"' && $q2 === '"') {
            return json_encode($array);
        }
        return $this->_value($array, true, $keys, $q, $q2);
    }

    /**
     * Generates a JavaScript object in JavaScript Object Notation (JSON)
     * from an array
     *
     * @param array $data Data to be converted
     * @param array $keys Unescaped keys list
     * @param char $q Quote keys char
     * @param char $q2 Quote values char
     * 
     * @return string A JSON code block
     */
	protected function _object($data, $qkeys = array(), $q = '"', $q2 = '"') {
		if (is_object($data)) {
			$data = get_object_vars($data);
		}
		$out = array();
		$numeric = true;

        $keys = array_keys($data);
        if (!empty($keys)) {
            $numeric = (array_values($keys) === array_keys(array_values($keys)));
        }

        foreach ($data as $key => $val) {
            $quoteStrings = empty($qkeys) || !in_array($key, $qkeys, true);
            $val = $this->_value($val, $quoteStrings, $qkeys, $q, $q2);
            if (!$numeric) {
                $val = $q . $this->_value($key, false) . $q . ':' . $val;
            }
            $out[] = $val;
        }

        if (!$numeric) {
            $rt = '{' . join(',', $out) . '}';
        } else {
            $rt = '[' . join(',', $out) . ']';
        }
		return $rt;
	}

    /**
     * Json quote a variable
     *
     * @param mixed $val
     * @param bool $quoteStrings
     * @return string
     */
	protected function _value($val, $quoteStrings = true, $qkeys = array(), $q = '"', $q2 = '"') {
		switch (true) {
			case (is_array($val) || is_object($val)):
				$val = $this->_object($val, $qkeys, $q, $quoteStrings ? $q2 : '');
			break;
			case ($val === null):
				$val = 'null';
			break;
			case (is_bool($val)):
				$val = ife($val, 'true', 'false');
			break;
			case (is_int($val)):
				$val = $val;
			break;
			case (is_float($val)):
				$val = sprintf("%.11f", $val);
			break;
			default:
				if ($quoteStrings && $q2) {
    				$val = $this->escapeString($val);
					$val = $q2 . $val . $q2;
				}
			break;
		}
		return $val;
	}

    /**
     * Escape a string to be JavaScript friendly.
     *
     * List of escaped ellements:
     *	+ "\r\n" => '\n'
     *	+ "\r" => '\n'
     *	+ "\n" => '\n'
     *	+ '"' => '\"'
     *	+ "'" => "\\'"
     *
     * @param  string $script String that needs to get escaped.
     * @return string Escaped string.
     */
	function escapeString($string) {
		$escape = array('\n' => '\\\n', "\r\n" => '\n', "\r" => '\n', "\n" => '\n', '"' => '\"', "'" => "\\'");
		return str_replace(array_keys($escape), array_values($escape), $string);
	}

    /**
     * Returns a JavaScript include tag (SCRIPT element).  If the filename is prefixed with "/",
     * the path will be relative to the base path of your application.  Otherwise, the path will
     * be relative to your JavaScript path, usually webroot/js.
     *
     * @param mixed $url String URL to JavaScript file, or an array of URLs.
     * @param boolean $inline Set to false to add to header declaration
     * @return string
     */
	function link($url, $inline = true) {
        if (is_array($url)) {
            $result = '';
            foreach ($url as $u) {
                $result .= $this->link($u, $inline);
            }
            return $result;
        }
        $url = $this->assetUrl($url, 'js');
		return $this->output(sprintf($this->tags['javascriptlink'], $url));
	}

     /**
     * Returns a JavaScript script tag.
     *
     * Options:
     *
     *  - allowCache: boolean, designates whether this block is cacheable using the
     * current cache settings.
     *  - safe: boolean, whether this block should be wrapped in CDATA tags.  Defaults
     * to helper's object configuration.
     *  - inline: whether the block should be printed inline, or written
     * to cached for later output (i.e. $scripts_for_layout).
     *
     * @param string $script The JavaScript to be wrapped in SCRIPT tags.
     * @param array $options Set of options:
     * @return string The full SCRIPT element, with the JavaScript inside it, or null,
     *   if 'inline' is set to false.
     */
	function codeBlock($script = null, $options = array()) {
		if (!is_array($options)) {
			$options = array('inline' => $options);
		} elseif (empty($options)) {
			$options = array();
		}
		$defaultOptions = array('safe' => true, 'inline' => true);
		$options = array_merge($defaultOptions, $options);

        // convert an array to a script string
        if (is_array($script)) {
            foreach ($script as &$line) {
                if (substr($line, -1) !== ';') {
                    $line .= ';';
                }
            }
            $script = implode("\n", $script);
        }

        $safe = ($options['safe'] || SlConfigure::read('View.options.safeJsCodeBlocks'));
        if ($safe) {
            $script  = "\n" . '//<![CDATA[' . "\n" . $script;
            $script .= "\n" . '//]]>' . "\n";
        }

        if ($options['inline']) {
            return sprintf($this->tags['javascriptblock'], $script);
        } else {
            $view =& ClassRegistry::getObject('view');
            $view->addScript(sprintf($this->tags['javascriptblock'], $script));
        }
	}

}
