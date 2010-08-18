<?php

/**
 * SL <=> Pheme bridge
 *
 * @property-read XhtmlHelper $Xhtml
 * @property-read JavascriptHelper $Javascript
 */
class Pheme {

    /**
     *
     * @var array
     */
    protected static $_instances = array();

    /**
     * Associative array wih registered block parsers
     *
     * @var array
     */
    protected static $_registered = array();

    /**
     * Core parser (skin defaults)
     *
     * @var PhemeParser
     */
    protected static $_core;

    /**
     * Layout parser (user skins container)
     *
     * @var PhemeParser
     */
    protected $_layout = null;

    /**
     * Main layout section
     *
     * @var PhemeParser
     */
    static protected $_body;

    /**
     *
     * @var string
     */
    protected $_layoutSkin = '{$pageContent}';

    function __construct() {
        if (empty(self::$_core)) {
            self::$_core = new PhemeParser();
            self::$_body = new BodyParser();
        }
        $this->_layout = new PhemeParser();
        $this->register('SimpleParser', new PhemeParser());
    }

    protected function _setLayout($layoutSkin) {
        $layoutSkin = self::getSkin($layoutSkin);
        if ($layoutSkin) {
            $this->_layout = new PhemeParser();
            $this->_layout->parse($layoutSkin);
            $this->_layoutSkin = $layoutSkin;
        }
    }

    static public function getSkin($skin) {
        if (is_array($skin)) {
            $skin = implode(" ", $skin);
        }
        elseif ($skin && preg_match('!^[a-z0-9/_]+$!', $skin)) {
            $plugin = null;
            if (strpos($skin, '.') !== false) {
                list($plugin, $skin) = explode('.', $skin);
            }
            $skin = Sl::getInstance()->view->pheme($skin, $plugin);
        }
        
        if ($skin && strpos($skin, '{!preserveWhitespace}') === false) {
            if (SlConfigure::read('View.phemeOptions.stripWhitespace')) {
                
                // remove javascript inline comments
                do {
                    $skin2 = $skin;
                    $skin = preg_replace('!(<script.*?>.*?)//[^:].*?\n(.*?</script>)!s', '\\1\\2', $skin2);
                } while ($skin2 != $skin);
                
                $skin = preg_replace('/\s+/', ' ', $skin);
            }
        } else {
            $skin = r('{!preserveWhitespace}', '', $skin);
        }
        return $skin;
    }

    /**
     *
     * @return Pheme
     */
    static public function getInstance() {
        if (empty(self::$_instances)) {
            self::push();
        }
        return self::$_instances[count(self::$_instances) - 1];
    }

    /**
     * Start a new layout cycle
     *
     * @param string $layoutSkin Text containing user skins overrides
     */
    static public function push($layoutSkin = null) {
        self::$_instances[] = $_this = new Pheme();
        if ($layoutSkin) {
            $_this->_setLayout($layoutSkin);
        }
    }

    /**
     * End a layout cycle
     */
    static public function pop() {
        array_pop(self::$_instances);
    }

    /**
     * Get the parser registered for a specific block.
     * Does not load the block, to autoload use Pheme::init(...) or Pheme::parse(...) instead
     *
     * @param string $blockName
     * @return PhemeParser
     */
    static public function get($blockName = null) {
        if ($blockName === null) {
            $trace = debug_backtrace();
            $blockName = Inflector::camelize(basename($trace[0]['file']));
        }
        if (strpos($blockName, '.') !== false) {
            list($plugin, $blockName) = explode('.', $blockName);
        }
        list($blockName) = explode(':', $blockName); // remove skin instance if present
        return !isset(self::$_registered[$blockName]) ?
            null : self::$_registered[$blockName];
    }

    /**
     * Check for a block, load it (if needed) and returns the parser
     *
     * @param string $blockName
     * @return PhemeParser
     */
    public static function init($blockName) {
        $plugin = null;
        if (strpos($blockName, '.')) {
            list($plugin, $blockName) = explode('.', $blockName);
        }

        if (self::get($blockName) === null) {
            self::$_registered[$blockName] = false;
            $fileName = Inflector::underscore($blockName);
            echo Sl::getInstance()->view->pheme("pheme/$fileName", $plugin);
        }
        
        $params = func_get_args();
        array_shift($params);
        if ($params) {
            foreach ($params as $b) {
                self::init($b);
            }
        }

        return self::get($blockName);
    }

    /**
     * Registers a parser to handle a block in current layout
     *
     * @param string $blockName
     * @param PhemeParser $parser
     * @param string $skin Default (html) skin
     * @param bool $globalContext If true, the registered block could be triggered from layout's {body},
     *      if set to a string the block will be considered a child of the registered block with
     *      that name.
     * @return bool Success or null on error
     */
    static public function register($blockName, $parser = null, $skin = null, $globalContext = true) {

        if (is_object($blockName)) {
            $globalContext = $skin;
            $skin = $parser;
            $parser = $blockName;

            $trace = debug_backtrace();
            $blockName = Inflector::camelize(
                preg_replace('/\.[^.]+$/', '', basename($trace[0]['file']))
            );
        }
        
        if (empty(self::$_registered[$blockName])) {
            
            // set default skin
            $skin = self::getSkin($skin);
            self::$_core->skins[$blockName] = $skin;

            // register parser
            if (is_string($parser)) {
                $parser = self::get($parser);
            }
            if (!is_object($parser)) {
                return;
            }
            self::$_registered[$blockName] = $parser;
            $parser->options['skinnable'] = true;

            // make block callable from layout's {body}
            if (is_string($globalContext)) {
                $parent = self::get($globalContext);
                $parent->blocks[$blockName] = $parser;
            }
            elseif ($globalContext) {
                self::$_body->blocks[$blockName] = $parser;
            }
            return true;
        }
        return false;
    }

    public static function registerOutputBuffer($blockName, $parser = null, $globalContext = true) {
         if (is_object($blockName)) {
            $globalContext = $parser;
            $parser = $blockName;

            $trace = debug_backtrace();
            $blockName = Inflector::camelize(
                preg_replace('/\.[^.]+$/', '', basename($trace[0]['file']))
            );
        }
        
        $result = Pheme::register($blockName, $parser, ob_get_clean(), $globalContext);
        ob_start();
        return $result;
    }

    static public function parseSimple($skin, $vars = array()) {
        return self::parse('SimpleParser', compact('skin', 'vars'));
    }

    static public function parseOutputBufferSimple($vars = array()) {
        $skin = ob_get_clean();
        $result = self::parse('SimpleParser', compact('skin', 'vars'));
        ob_start();
        return $result;
    }

    static public function parseSimpleOutputBuffer($vars = array()) {
        $skin = ob_get_clean();
        $result = self::parse('SimpleParser', compact('skin', 'vars'));
        ob_start();
        return $result;
    }

    static public function parseTranslate($skin, $vars = array()) {
        $blocks = PhemeParser::$coreBlocks;
        PhemeParser::$coreBlocks = array_intersect_key($blocks, array('t' => true, 'h' => true, 'e' => true));
        $result = self::parse('SimpleParser', compact('skin', 'vars'));
        PhemeParser::$coreBlocks = $blocks;
        return $result;
    }

    /**
     * Trigger parse() in a registered block; loads the block if needed
     *
     * @param string $blockName Name of a registered block (instance markers supported) or a PhemeParser instance
     * @param array $params Override params (applicable only to PhemeSubParser descendents) (optional)
     * @param array $vars Override vars (optional)
     * @param array $blockParams Set blockParams for this call (optional)
     * @param string $skin Override skin
     * @return string
     */
    static public function parse($blockName, $options = array()) {
        $blockParams = $params = $vars = $skin = null;
        extract($options);

        /*if (is_string($params)) {
            $skin = $params;
            $params = $vars = $blockParams = null;
        }
        elseif (is_string($vars)) {
            $skin = $vars;
            $vars = $blockParams = null;
        }
        elseif (is_string($blockParams)) {
            $skin = $blockParams;
            $blockParams = null;
        }*/

        if (is_object($blockName)) {
            $parser = $blockName;
            $blockName = 'document';
        } 
        elseif(is_string($blockName)) {
            list($parserName) = explode(':', $blockName);
            if (strpos($blockName, '.') !== false) {
                list($plugin, $blockName) = explode('.', $blockName);
            }
            $parser = self::init($parserName);
        }
        
        if (!empty($parser)) {
            if ($params !== null && is_a($parser, 'PhemeSubParser')) {
                $oldParams = $parser->params;
                $parser->params = $params;
            } else {
                $params = null;
            }

            if ($vars !== null) {
                $parser->vars = $vars;
            }
            
            $skin = self::getSkin($skin);

            // check whether if we have any parse() running calls
            if (empty(PhemeParser::$parseCallStack)) {
                // if not, add core and layout parser to call stack (needed for skinning to work)
                PhemeParser::$parseCallStack = array(
                    self::$_core, // default skins
                    self::getInstance()->_layout, // site-specific skins
                    self::$_body, // registered blocks
                );
                $skin = $parser->parse($skin, $blockName, $blockParams);
                PhemeParser::$parseCallStack = array();
            } else {
                $skin = $parser->parse($skin, $blockName, $blockParams);
            }

            if ($params !== null) {
                $parser->params = $oldParams;
            }
            return $skin;
        }
    }

    static public function parseLayout($vars = array(), $options = array()) {
        $options += array(
            'headAndFooter' => true,
            'generatedIn' => Configure::read(),
        );

        $_this = self::getInstance();
        $vars += Sl::getInstance()->view->viewVars;

        if ($options['headAndFooter']) {
            $vars['head'] = Sl::uniqid();
            $vars['footer'] = Sl::uniqid();
        }

        $_this->_layout->blocks = array('body' => self::$_body);
        if ($options['generatedIn']) {
            $_this->_layout->blocks['generatedIn'] = new GeneratedInParser();
        }
        $_this->_layout->vars = $vars;
        
        $stack = PhemeParser::$parseCallStack;
        PhemeParser::$parseCallStack = array(self::$_core);
        $skin = $_this->_layout->parse($_this->_layoutSkin);
        PhemeParser::$parseCallStack = $stack;

        if ($options['headAndFooter']) {
            $skin = r(
                array($vars['head'], $vars['footer']),
                array(
                    Sl::getInstance()->view->element('head', $vars),
                    Sl::getInstance()->view->element('footer')
                ),
                $skin
            );
        }
        
        $_this->_layout->blocks['body'] = null;
        $_this->_layout->vars = array();
        
        return $skin;
    }

    static function parseOutputBuffer($blockName, $options = array()) {
        $options['skin'] = ob_get_clean();
        $result = Pheme::parse($blockName, $options);
        ob_start();
        return $result;
    }
}

// -----------------------------------------------------------------------------

/**
 * Part of SL <=> Pheme bridge
 */
class PhemeBaseParser {

    public function __construct() {
    }

    protected function _getHelper($name) {
        $view = Sl::getInstance()->view;
        $name2 = Inflector::variable($name);
        return $view && isset($view->loaded[$name2]) ?
            $view->loaded[$name2] :
            ClassRegistry::init("{$name}Helper", 'helper');
    }

    protected function _getView() {
        return Sl::getInstance()->view;
    }

    protected function _getParam($name) {
        $name = explode('.', $name);
        $addr =& Sl::getInstance()->view->params;
        while ($name && isset($addr[$name[0]])) {
            $addr =& $addr[array_shift($name)];
        }
        return $name ? null : $addr;
    }
}

App::import('lib', 'pheme/parser');

// =============================================================================

class GeneratedInParser {
    function parse() {
        return __t('Generated in {$time} ({$request}) sec.', array('time' => Sl::uptime(), 'request' => Sl::uptime(true)));
    }
}

/**
 * SL <=> Pheme bridge: $view->params[...]
 */
class ParamParser extends PhemeParser {
    function parse($html, $blockName = 'document', $blockParams = null) {
        return $this->_getParam(parent::parse($html));
    }
}

/**
 * SL <=> Pheme bridge: Cake url support
 */
class UrlParser extends PhemeParser {
    function parse($html, $blockName = 'document', $blockParams = null) {
        $full = !empty($blockParams['full']);
        unset($blockParams['full']);
        return h(r(' ', '+', Sl::url($html ? $html : $blockParams, $full)));
    }
}

/**
 * SL <=> Pheme bridge: SlConfigure::read(...)
 */
class ConfigParser extends PhemeParser {
    function parse($html, $blockName = 'document', $blockParams = null) {

        // security check
        if (preg_match('/\.password$/', $html)) {
            return '';
        }

        $value = SlConfigure::read(
            parent::parse($html),
            !empty($blockParams['collection']) ? $blockParams['collection'] : '*'
        );
        return (string)$value; // prohibit read of unsecured data
    }
}

/**
 * SL <=> Pheme bridge: $view->elemant(...)
 */
class ElementParser extends PhemeParser {
    function parse($html, $blockName = 'document', $blockParams = null) {
        return $this->_getView()->element(
            parent::parse($html),
            empty($blockParams) ? array() : $blockParams
        );
    }
}

/**
 * SL <=> Pheme bridge: __t(...)
 */
class TranslateParser extends PhemeParser {
    function parse($html, $blockName = 'document', $blockParams = null) {
        return parent::parse(__t(
            $html,
            $blockParams,
            isset($blockParams['domain']) ? $blockParams['domain'] : true
        ));
    }
}

/**
 * SL <=> Pheme bridge: Sl::requestAction(...)
 */
class ActionParser extends PhemeParser {
    function parse($html, $blockName = 'document', $blockParams = null) {
        return Sl::requestAction(trim(parent::parse($html)), (array)$blockParams);
    }
}

/**
 * SL <=> Pheme bridge: Load a user skin file (located in /app/views/pheme_skins/)
 */
class ImportParser extends PhemeParser {
    function parse($html) {
        return parent::parse(Pheme::getSkin(parent::parse($html)), '_skin');
    }
}

/**
 * SL <=> Pheme bridge: Return asset url
 */
class AssetParser extends PhemeParser {
    function parse($html, $blockName = 'document', $blockParams = null) {
        $blockParams = (array)$blockParams;
        $blockParams += array(
            'type' => null,
        );
        return parent::parse($this->_getHelper('SlHtml')->assetUrl($html, $blockParams['type']));
    }
}

/**
 * SL <=> Pheme bridge: Load a block parser (located in /app/views/pheme_blocks/)
 */
class RequireParser extends PhemeParser {
    function parse($html) {
        $items = explode(',', parent::parse($html));
        foreach ($items as $item) {
            Pheme::init(trim($item));
        }
    }
}

/**
 * SL <=> Pheme bridge: Content block (separates block calls from block skinning in layout files)
 */
class BodyParser extends PhemeParser {
    function parse($html, $blockName = 'document', $blockParams = null) {
        $html = parent::parse($html);
        if (!empty($blockParams['ajax'])) {
            return $html;
        }
        return "<div id='sl-viewport'>$html</div>";
    }
}

/**
 * SL <=> Pheme bridge: Extended "if" block handler
 */
class ConditionalParser extends PhemeConditionalParser {
    protected function _eval($blockParams) {
        $var = null;

        if (!empty($blockParams['blockCount'])) {
            $data = SlConfigure::read2("Block.".$blockParams['blockCount']);
            $var = count($data);
        }
        elseif (!empty($blockParams['config'])) {
            $var = SlConfigure::read2($blockParams['config']);
        }
        elseif (!empty($blockParams['collection'])) {
            $collections = SlConfigure::read();
            $var = in_array($blockParams['collection'], $collections);
        }
        elseif (!empty($blockParams['param'])) {
            $var = $this->_getParam($blockParams['param']);
        }

        if ($var !== null) {
            $blockParams['var'] = '_sl';
            $this->vars['_sl'] = $var;
        }

        return parent::_eval($blockParams);
    }
}

PhemeParser::$coreBlocks['webroot'] =
    PhemeParser::$coreBlocks['asset'] =
    PhemeParser::$coreBlocks['assetUrl'] = new AssetParser();
PhemeParser::$coreBlocks['url'] = new UrlParser();
PhemeParser::$coreBlocks['config'] = new ConfigParser();
PhemeParser::$coreBlocks['param'] = new ParamParser();
PhemeParser::$coreBlocks['if'] = new ConditionalParser();
PhemeParser::$coreBlocks['import'] = new ImportParser();
    PhemeParser::$coreBlocks['include'] = new ImportParser();
PhemeParser::$coreBlocks['h'] = PhemeParser::$coreBlocks['e'];
PhemeParser::$coreBlocks['t'] = new TranslateParser();
PhemeParser::$coreBlocks['element'] = new ElementParser();
PhemeParser::$coreBlocks['request'] =
    PhemeParser::$coreBlocks['action'] =
    PhemeParser::$coreBlocks['requestAction'] = new ActionParser();
PhemeParser::$coreBlocks['init'] =
    PhemeParser::$coreBlocks['require'] = new RequireParser();
