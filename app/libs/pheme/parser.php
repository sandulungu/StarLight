<?php

/**
 * Pheme
 *
 * A tiny, yet powerful and flexible PHP5 template engine.
 *
 * Was inspired by Tumblr's custom templating engine, so the syntax is pretty
 * close to that, but more compact and flexible. Even more, TumblParser and
 * TumblSubParser mimic Tumblr's theme syntax making it 100% compatible (you'll
 * just need to create the (sub)parsers for your custom logics).
 *
 * Tumblr's ingenious <meta> variable declarations are not yet supported, byt
 * may be some time soon; as well, as better documentation and reviews.
 *
 * This file represents the library itself, having a few classes (Parsers).
 *
 * The core class (PhemeParser) should be used as a parent class for all subparsers.
 *
 * You will usualy override the PhemeParser::parser(...) method to describe
 * your template logic. From the architectural point of view, all parser
 * class(es) initialization should be done at runtime and as late as possible.
 *
 * For now it does not natively support any caching (it's enough speedy for most
 * tasks w/o any need for caching), but it could be easily added, if required.
 *
 * This library was developed as the template parser for the StarLight CMS
 * (which will be publicly released as GPL v3 very soon).
 *
 * @author Sandu Lungu <sandu@lungu.info>
 * @copyright 2010 Sandu Lungu
 * @filesource
 * @license http://www.gnu.org/licenses/gpl.html GPL3
 * @link https://www.ohloh.net/p/pheme
 * @package Pheme
 * @example index.php Main features demonstration
 *
 * @version 1.0.alpha
 */

if (!class_exists('PhemeBaseParser')) {
    /**
     * Base class for all parsers (except PhemeNullParser)
     */
    class PhemeBaseParser {
        /**
         * Custom-scenarios initialization routines
         */
        function __construct() {
        }
    }
}

/**
 * Main parser class
 */
class PhemeParser extends PhemeBaseParser {
    /**
     * No. recursive PhemeParser::parse(...) method calls (succesively
     * openned sub-blocks) before will be forced to return
     */
    const MAX_RECURSIVITY = 1000;

    /**
     * Current version number
     */
    const VERSION = '0.3';

    /**
     * Recursion stack containing with active parent parsers
     *
     * @var array
     */
    static public $parseCallStack = array();

    /**
     * Total count of preg callbacks (only main parser)
     *
     * @var int
     */
    static public $iterations = 0;

    /**
     * Globally available (special) blocks
     *
     * @var PhemeConditionalParser
     */
    public static $coreBlocks = array();

    /**
     * Preg used by the parse(...) method
     *
     * @var string
     */
    protected $_preg = '!{([@$])([A-Za-z0-9][A-Za-z0-9._]*)}|{([A-Za-z][A-Za-z0-9]*:?[A-Za-z0-9]*)(\((.+?)\))?(/}|}(.*?){/\\3})!s';

    /**
     * Last openned block name
     *
     * @var string
     */
    protected $_blockName = null;

    /**
     * Last openned block params
     *
     * @var array
     */
    protected $_blockParams = null;

    /**
     * Customizable variable that will be directly injected in templats
     *
     * @var array
     */
    public $vars = array();

    /**
     * Dynamic vars, some core parsers (PhemeSkinParser instances) will update this value
     *
     * @var array
     */
    public $skins = array();

    /**
     * Independent (subtemplate) logical units associated with Parser
     * (or its descendent classes) instances
     *
     * @var array
     */
    public $blocks = array();

    /**
     * Any custom configuration options
     *
     * @var array
     */
    public $options = array();

    /**
     * Preg callback used in parse(...) method
     *
     * @param array $matches
     * @return string
     */
    protected function _pregCallback($matches) {
        self::$iterations++;

        if (!empty($matches[2])) {
            // variable
            return $matches[1] == '@' ?
            $this->_getSkin($matches[2]) :
            $this->_getVar($matches[2], true);

        } else {
            // block
            list($blockName) = explode(':', $matches[3]);

            $params = !empty($matches[5]) ?
                json_decode('{'.$matches[5].'}', true) :
                null;
            if (is_array($params)) {
                foreach ($params as &$param) {
                    if (is_string($param) && strpos($param, '{') !== false) {
                        $param = PhemeParser::parse($param, '_param');
                    }
                }
            }
                
            return $this->_getBlock($blockName)->parse(
                empty($matches[7]) ? null : $matches[7],
                $matches[3],
                $params
            );
        }
    }

    /**
     * Controls all the hardcore parsing
     *
     * @param string $html Input/block text
     * @param string $blockName
     * @param array $blockParams
     * @return string Html
     */
    public function parse($html = null, $blockName = 'document', $blockParams = null) {
        if (count(self::$parseCallStack) > self::MAX_RECURSIVITY) {
            trigger_error('Stack overflow check error in Parser::parse()');
            return;
        }
        
        $register2stack = $blockName{0} !== '_';
        if ($register2stack) {
            self::$parseCallStack[] = $this;
        }

        if (empty($html) && !empty($this->options['skinnable'])) {
            $html = $this->_getSkin($blockName, false);
            if (empty($html) && strpos($blockName, ':')) {
                list($skin) = explode(':', $blockName);
                $html = $this->_getSkin($skin, false);
            }
        }

        $this->_blockName = $blockName;
        $this->_blockParams = $blockParams;
        $html = preg_replace_callback(
            $this->_preg, array($this, '_pregCallback'),
            $html
        );

        if ($register2stack) {
            array_pop(self::$parseCallStack);
        }
        return $html;
    }

    /**
     * Get the Parser instance that triggered current block
     *
     * @return PhemeParser
     */
    function referrer() {
        return self::$parseCallStack ? self::$parseCallStack[count(self::$parseCallStack) - 1] : null;
    }

    /**
     * Recursively gets a (static) variable
     *
     * @param string $name
     * @param bool $toString Set to true to force the returned value to be of string type
     * @return mixed (string if $toString is true)
     */
    protected function _getVar($name, $toString = false) {
        $name = explode('.', $name);
        if (isset($this->vars[$name[0]])) {
            $addr =& $this->vars[array_shift($name)];
            while ($name && isset($addr[$name[0]])) {
                $addr =& $addr[array_shift($name)];
            }
            return $name ? null : ($toString && is_array($addr) ? null : $addr);
        }

        for ($i = count(self::$parseCallStack) - 1; $i >= 0; $i--) {
            if (isset(self::$parseCallStack[$i]->vars[$name[0]])) {
                $addr =& self::$parseCallStack[$i]->vars[array_shift($name)];
                while ($name && isset($addr[$name[0]])) {
                    $addr =& $addr[array_shift($name)];
                }
                return $name ? null : ($toString && is_array($addr) ? null : $addr);
            }
        }
        return null;
    }

    /**
     * Recursively gets a (dynamic) variable (a.k.a. skins)
     *
     * @param string $name
     * @param bool $parse Set to false if you plan to parse the returned value later
     * @return string
     */
    protected function _getSkin($name, $parse = true) {
        if (isset($this->skins[$name])) {
            $skin = $this->skins[$name];
            if ($parse) {
                $skin = PhemeParser::parse($skin, '_skin');
            }
            return $skin;
        }

        for ($i = count(self::$parseCallStack) - 1; $i >= 0; $i--) {
            if (isset(self::$parseCallStack[$i]->skins[$name])) {
                $skin = self::$parseCallStack[$i]->skins[$name];
                if ($parse) {
                    $skin = PhemeParser::parse($skin, '_skin');
                }
                return $skin;
            }
        }
        return null;
    }

    /**
     * Recursively gets the parser that should process a specific block
     *
     * @param string $name Block name
     * @return PhemeParser
     */
    protected function _getBlock($name) {
        if (isset(self::$coreBlocks[$name])) {
            return self::$coreBlocks[$name];
        }
        if (isset($this->blocks[$name])) {
            return $this->blocks[$name];
        }
        for ($i = count(self::$parseCallStack) - 1; $i >= 0; $i--) {
            if (isset(self::$parseCallStack[$i]->blocks[$name])) {
                return self::$parseCallStack[$i]->blocks[$name];
            }
        }
        return new PhemeSkinParser();
    }
}

/**
 * Useful for subtemplates, like blocks or interface elements.
 *
 * The constructor takes any number of parameters, then assigs them to the $_params property
 */
class PhemeSubParser extends PhemeParser {
    public $params = null;

    /**
     * Class constructor.
     * Takes any number of parameters, then assigs them to the $_params property
     */
    function __construct() {
        $this->params = func_get_args();
        parent::__construct();
    }
}

/**
 * Copies block text to parent parser's vars array and silently returns
 */
class PhemeLoopParser extends PhemeSubParser {
    /**
     * Any custom configuration options.
     *
     * Use 'separator' key to set the text between 2 adiacent items, defaults to "\n"
     *
     * @var array
     */
    public $options = array('separator' => "\n");

    /**
     * The $tag parameter in last _extractTags(...) call. Used by preg callbacks
     *
     * @var string
     */
    protected $_extractedTag = null;

    /**
     * Expression used for temporary escaping tags in sub-blocks
     *
     * @var string
     */
    protected $_innerSkipTag = '{!innerSkip}';

    /**
     * sprintf(...) expression used in skins for marking a tag as loop unrelated
     *
     * @var string
     */
    protected $_skipTag = '{!skip:%s}';

    /**
     * sprintf(...) expression used to mark the separator of items in skins
     *
     * @var string
     */
    protected $_lastTag = '{!sep:%s}';

    /**
     * Preg callback. Escape tags in sub-blocks
     *
     * @param array $matches
     * @return string
     */
    protected function _extractTagsPregCallback($matches) {
        return preg_replace(
            "!<({$this->_extractedTag}.+?<\/{$this->_extractedTag})>!s",
            "<{$this->_innerSkipTag}\\1{$this->_innerSkipTag}>",
            $matches[0]
        );
    }

    /**
     * Preg callback. Escape marked tags
     *
     * @param array $matches
     * @return string
     */
    protected function _extractTagsPregCallback2($matches) {
        $skip = sprintf($this->_skipTag, $this->_blockName);
        return strpos($matches[1], "<{$this->_extractedTag}") ?
            "<{$matches[1]}{$matches[2]}{$skip}>" :
            "<{$skip}{$matches[1]}{$matches[2]}>";
    }

    /**
     * Split block content in non-repeating, separator and item skins, based
     * on html tags that surround items
     *
     * @param string $html
     * @param string $tag Tag name
     * @param bool $ignoreSeparators Set to false to get all the separators between items
     * @return array Openning, (separators), closing and items html/xml
     */
    protected function _extractTags($html, $tag, $ignoreSeparators = true) {
        $matches = array();
        $this->_extractedTag = $tag;
        $skip = sprintf($this->_skipTag, $this->_blockName);

        // prevent usage of tags inside inner box in loops
        $html = preg_replace_callback(
            '!{([A-Za-z][A-Za-z0-9]*)(\s*,(.+?))?}(.*?){/\\1}!s',
            array($this, '_extractTagsPregCallback'),
            $html
        );

        // transform '<td...{!skiptag}...' => '<{!skiptag}td...'
        $html = preg_replace_callback(
            "/<($tag.+?){$skip}(.*?<\/$tag)>/s",
            array($this, '_extractTagsPregCallback2'),
            $html
        );

        // split into $start, $end and $items[]
        preg_match_all("!<$tag.+?</$tag>!s", $html, $matches, PREG_PATTERN_ORDER);
        $startEnd = preg_split("!<$tag.+?</$tag>!s", $html);
        if ($matches[0]) {
            $items = array();
            foreach ($matches[0] as $item)
                $items[] = str_replace($this->_innerSkipTag, '', $item);
        } else {
            $items = null;
        }

        if ($ignoreSeparators) {
            return array(
            str_replace(array($skip, $this->_innerSkipTag), '', array_shift($startEnd)),
            str_replace(array($skip, $this->_innerSkipTag), '', array_pop($startEnd)),
            $items
            );
        }

        foreach ($startEnd as &$item) {
            $item = str_replace(array($skip, $this->_innerSkipTag), '', $item);
        }
        $lastItem = explode(sprintf($this->_lastTag, $this->_blockName), $item);
        if (count($lastItem) > 1) {
            $item = $lastItem[0];
            $startEnd[] = $lastItem[1];
        } else {
            $item = '';
            $startEnd[] = $lastItem[0];
        }

        return array($startEnd, $items);
    }

    /**
     * Controls all the hardcore parsing, also does the cycling
     *
     * @param string $html Input
     * @param string $blockName
     * @param bool $noCycle Set to true to just parse the $html (like PhemeParser)
     * @return string Html
     */
    public function parse($html = null, $blockName = 'document', $blockParams = null, $noCycle = false) {
        if ($noCycle) {
            return parent::parse($html, $blockName, $blockParams);
        }

        if (empty($html)) {
            $html = $this->_getSkin($blockName);
        }
        if (empty($this->params[0]) || empty($html)) {
            return;
        }
        $param = is_string($this->params[0]) ?
            $this->_getVar($this->params[0]) :
            $this->params[0];

        if (empty ($param) || !is_array($param)) {
            return;
        }

        $this->_blockName = $blockName;
        $items = array();

        if (!empty($blockParams['itemTag'])) {
            if (!empty($blockParams['groupTag'])) {

                list($groupStart, $groupEnd, $groupTags) =
                    $this->_extractTags($html, $blockParams['groupTag']);
                if (!$groupTags) {
                    return;
                }
                $itemStarts = $itemEnds = $itemTags = array();
                foreach ($groupTags as $groupTag) {
                    list($itemStart, $itemEnd, $itemTag) =
                        $this->_extractTags($groupTag, $blockParams['itemTag']);
                    if (!empty($itemTag)) {
                        $itemStarts[] = $itemStart;
                        $itemEnds[] = $itemEnd;
                        $itemTags[] = $itemTag;
                    }
                }

                // 2-dim projection cycle
                $i = 0;
                $ci = count($itemTags);
                if (!empty($blockParams['showEmpty'])) {
                    $ci--;
                }
                $j = 0;
                $items[] = parent::parse($groupStart);
                foreach ($param as $key => $vars) {
                    if ($vars === null) {
                        continue;
                    }

                    if ($j == 0) {
                        $items[] = parent::parse($itemStarts[$i]);
                    }
                    $items[] = $this->_parseItem($itemTags[$i][$j], $vars, $blockName, $blockParams, $key);
                    $j++;
                    if ($j >= count($itemTags[$i])) {
                        $items[] = parent::parse($itemEnds[$i]);
                        $j = 0;
                        $i = ($i+1)%$ci;
                    }
                }
                if ($j > 0) {
                    $cj = count($itemTags[$i]);
                    if (!empty($blockParams['showEmpty']) && $j < $cj) {
                        $cij = count($itemTags[$ci]);
                        while ($j < $cj) {
                            $items[] = parent::parse($itemTags[$ci][$j%$cij]);
                            $j++;
                        }
                    }
                    $items[] = parent::parse($itemEnds[$i]);
                }
                $items[] = parent::parse($groupEnd);

            } else {
                list($itemStartEnd, $itemTags) =
                    $this->_extractTags($html, $blockParams['itemTag'], false);
                if (!$itemTags) {
                    return;
                }

                if (!empty($blockParams['first'])) {
                    $firstTag = array_shift($itemTags);
                }
                if (!empty($blockParams['last'])) {
                    $lastTag = array_pop($itemTags);
                    $aKeys = array_keys($vars);
                    $last = end($aKeys);
                } else {
                    $last = null;
                }

                // liniar projection cycle
                $i = 0;
                $ci = count($itemTags);
                $first = true;
                foreach ($param as $key => $vars) {
                    if ($vars === null) {
                        continue;
                    }

                    $items[] = parent::parse($itemStartEnd[$i || $first ? $i : $ci]);
                    $items[] = $this->_parseItem(
                        $first && !empty($firstTag) ? $firstTag :
                            ($last === $key && !empty($lastTag) ? $lastTag : $itemTags[$i]),
                        $vars, $blockName, $blockParams, $key
                    );
                    $i = ($i+1)%$ci;
                    $first = false;
                }
                $items[] = parent::parse($itemStartEnd[$ci+1]);
            }

        } else {
        // the simplest cycle. We use $this->_params[0], stored by the class constructor
            $first = true;
            $tag = sprintf($this->_lastTag, $blockName);
            list($html, $sep) = explode($tag, $html.$tag);
            foreach ($param as $i => $vars) {
                if ($vars === null) {
                    continue;
                }

                $items[] = ($first ? '' : $sep) .
                    $this->_parseItem($html, $vars, $blockName, $blockParams, $i);
                $first = false;
            }
        }

        return implode($this->options['separator'], $items);
    }

    /**
     * Item parsing with custom vars set
     *
     * @param string $html
     * @param array $vars Associativea array of vars (strings)
     * @param string $blockName
     * @param array $blockParams
     * @return string
     */
    protected function _parseItem($html, $vars, $blockName = 'document', $blockParams = null, $i = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'var' => 'item',
            'indexVar' => 'i'
        );

        $oldvars = $this->vars;
        $this->vars = is_array($vars) && !Set::numeric($vars) ? $vars : array($blockParams['var'] => $vars);
        if (!empty($blockParams['indexVar']) && isset($i)) {
            $this->vars[$blockParams['indexVar']] = $i;
        }
        $html = parent::parse($html);
        $this->vars = $oldvars;
        return $html;
    }
}

/**
 * Most common cycle
 */
class PhemeForeachParser extends PhemeLoopParser {

    public function parse($html, $blockName = 'document', $blockParams = null, $noCycle = false) {
        if ($noCycle) {
            return parent::parse($html, $blockName, $blockParams);
        }
        if (empty($blockParams['var'])) {
            return;
        }
        $this->params[0] = $this->_getVar($blockParams['var']);
        return parent::parse($html, $blockName, $blockParams, $noCycle);
    }
}

/**
 * Block skins updater
 */
class PhemeSkinParser extends PhemeParser {
    /**
     * Set a dynamic var in parents instance (updates the skin)
     *
     * @param string $html Input
     * @param string $blockName
     * @param string $blockParams
     * @return string
     */
    public function parse($html, $blockName = 'document', $blockParams = null) {
        if ($blockName != 'skin') {
            $this->referrer()->skins[$blockName] = $html;
        }
        elseif (!empty($blockParams['block'])) {
            $this->referrer()->skins[$blockParams['block']] = $html;
        }
    }
}

/**
 * Simulates if/else behavior
 */
class PhemeConditionalParser extends PhemeParser {
    protected $_elseTag = '{else%s}';
    protected $_elseIfTag = array('{elseIf%s(', ')}');

    protected function _eval($blockParams) {
        $value = $value2 = null;
        if (!empty ($blockParams['var'])) {
            $value = $this->_getVar($blockParams['var']);
        }
        elseif (!empty ($blockParams['template'])) {
            $value = $this->_getSkin($blockParams['template']);
        }
        elseif (!empty ($blockParams['param'])) {
            $params =& $this->referrer()->_blockParams;
            $value = isset($params[$blockParams['param']]) ?
                $params[$blockParams['param']] : null;
        }
        $yes = $value != null;

        if (!empty ($blockParams['value'])) {
            $value2 = $blockParams['value'];
            $yes = $value == $value2;

            if (!empty($blockParams['op'])) {
                switch (strtolower($blockParams['op'])) {
                    case 'in':
                        $yes = in_array($value, $value2);
                        break;

                    case 'same':
                        $yes = mb_strtolower($value) === mb_strtolower($value2);
                        break;

                    case '===':
                    case 'is':
                        $yes = $value === $value2;
                        break;

                    case '!=':
                    case '<>':
                    case 'not':
                        $yes = $value != $value2;
                        break;

                    case '!==':
                        $yes = $value !== $value2;
                        break;

                    case '<':
                        $yes = $value < $value2;
                        break;

                    case '>':
                        $yes = $value > $value2;
                        break;

                    case '<=':
                        $yes = $value <= $value2;
                        break;

                    case '>=':
                        $yes = $value >= $value2;
                        break;
                }
            }
        }
        return $yes;
    }

    /**
     * The parsing of $html parameter depends on the evaluation of conditions
     * specified in $blockParams
     *
     * @param string $html Input
     * @param string $blockName
     * @param string $blockParams
     * @return string
     */
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $instance = preg_replace('/^[^:]+/', '', $blockName);
        $elseTag = sprintf($this->_elseTag, $instance);
        $elseIfTag = sprintf($this->_elseIfTag[0], $instance);

        list($ifHtml, $elseHtml) = explode($elseTag, $html.$elseTag);
        $blocks = explode($elseIfTag, $ifHtml);

        $html = array_shift($blocks);
        if ($this->_eval($blockParams)) {
            return parent::parse($html);
        }

        foreach ($blocks as $block) {
            if (strpos($block, $this->_elseIfTag[1]) === false) {
                continue;
            }
            list($json, $html) = explode($this->_elseIfTag[1], $block, 2);
            if ($this->_eval(json_decode('{'.$json.'}', true))) {
                return parent::parse($html);
            }
        }

        return parent::parse($elseHtml);
    }
}

/**
 * Escapes content, equivalent to htmlentities(...)
 */
class PhemeHtmlEntitiesParser extends PhemeParser {

    /**
     * Escapes content, equivalent to htmlentities(...)
     *
     * @param string $html Input
     * @param string $blockName
     * @param string $blockParams
     * @return string
     */
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'charset' => 'UTF-8',
            'doubleEncode' => false,
            'maxLength' => false,
            'nl2br' => true,
        );

        $html = parent::parse($html);
        if ($blockParams['maxLength'] && mb_strlen($html) > $blockParams['maxLength']) {
            $html = mb_substr($html, 0, $blockParams['maxLength']).'...';
        }
        $html = htmlentities(
            $html,
            ENT_QUOTES,
            $blockParams['charset'],
            !$blockParams['doubleEncode']
        );
        if ($blockParams['nl2br']) {
            $html = nl2br($html);
        }
        return $html;
    }
}

/**
 * Parse a teplate variable (just as it was a skin)
 */
class PhemeVarParser extends PhemeParser {

    /**
     * Escapes content, equivalent to htmlentities(...)
     *
     * @param string $html Input
     * @param string $blockName
     * @param string $blockParams
     * @return string
     */
    public function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'inc' => null,
        );

        $value = $this->_getVar($html);
        if (is_numeric($blockParams['inc'])) {
            $value += $blockParams['inc'];
            return $value;
        }
        return parent::parse($value, '_var');
    }
}

/**
 * Black hole
 */
class PhemeNullParser {

    /**
     * Does nothing
     */
    public function parse() {
    }
}

PhemeParser::$coreBlocks = array(
    "group" => new PhemeParser(),
    "foreach" => new PhemeForeachParser(),
    "if" => new PhemeConditionalParser(),
    "c" => new PhemeNullParser(),
    "e" => new PhemeHtmlEntitiesParser(),
    'skin' => new PhemeSkinParser(),
    'var' => new PhemeVarParser(),
);
