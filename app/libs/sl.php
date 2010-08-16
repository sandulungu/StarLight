<?php

/**
 * Core application class
 *
 * Handles initialization, current context management and debugging
 */
 
class Sl {
    /**
     * @var array
     */
    protected static $_instances = array();

    /*
     * @var bool
     */
    private static $__halted = false;

    /**
     * Get or set version info (DB)
     *
     * @param string $extension
     * @param string $newVersion
     */
    public static function version($extension = 'core', $newVersion = null) {
        if (!SlConfigure::read('Mirror.version')) {
            App::import('Core', 'ConnectionManager');
            $db = @ConnectionManager::getDataSource('default');
            if (!$db->isConnected() ||
                !in_array("{$db->config['prefix']}core_versions", $db->listSources())
            ) {
                if (strpos(Sl::url(false), '/install') === false) {
                    Router::connect(Sl::url(false), array('controller' => 'install'));
                }
                return;
            }

            App::import('Core', 'ClassRegistry');
            ClassRegistry::init('Version')->refreshMirror();
        }

        if ($newVersion) {
            $versionModel = ClassRegistry::init('Version');
            $id = $versionModel->field('Version.id', array('Version.name' => $extension));
            $versionModel->create();
            return $versionModel->save(array('id' => $id, 'name' => $extension, 'version' => $newVersion));
        }
        
        return SlConfigure::read("Mirror.version.$extension");
    }

    /**
     * Get the current instance of a helper or initilizes a new one
     *
     * @param string $name
     * @param bool $init True to create the helper in the current view if not loaded
     * @return AppHelper
     */
    public static function getHelper($name, $init = true) {
        $view = self::getInstance()->view;

        // attempt load from current view
        if ($view) {
            $helperName = Inflector::variable($name);
            if (!empty($view->loaded[$helperName])) {
                return $view->loaded[$helperName];
            }
        }

        // attempt load from ClassRegistry
        $className = "{$name}Helper";
        $object = ClassRegistry::getObject($className);
        if ($object) {
            return $object;
        }

        // create and register object
        if ($init) {
            if ($view) {
                $loaded = array('dummy');
                $view->_loadHelpers($loaded, array($name));
                if (isset($loaded[$name])) {
                    ClassRegistry::addObject($className, $loaded[$name]);
                    return $loaded[$name];
                }
            } else {
                return ClassRegistry::init($className, 'helper');
            }
        }
    }

    /**
     * Humanize (in lower case)
     */
    public static function humanize($str) {
        return str_replace('_', ' ', Inflector::underscore($str));
    }

    /**
     * Improved requestAction. AutoRenders by default
     *
     * @param mixed $url
     * @param array $params If set to true, the return value of the controller
     *      action is returned instead of the renderred output
     * @return mixed
     */
    static public function requestAction($url, $params = array()) {
        if (is_bool($params)) {
            $params = array('return' => $params);
        }
    	$params += array(
            'return' => false,
            'bare' => true,
            'requested' => true
        );
        
        if (is_array($url)) {
            $url = self::url(array('base' => false) + $url);
        }
        
        // just in case...
        self::getInstance();

        $savedCollections = SlConfigure::rememberCollections();
        self::_push($url);
        ob_start();
        $result = Object::requestAction($url, $params);
        $html = ob_get_clean();
        self::pop();
        SlConfigure::restoreCollections($savedCollections);

        if ($html && (is_string($result) || empty($result))) {
            $result .= $html;
        }
        if ($result instanceof AppError && !$params['return']) {
            $result = $html;
        }
        return $result;
    }

    /**
     * IP address subnet match. <br><br>
     *
     * Example: Sl::ipMatch(env('REMOTE_ADDR'), array('192.168.0.0/16', '10.0.0.0/24'))
     *
     * @param string $IP
     * @param mixed $CIDR
     * @return bool
     */
    static public function ipMatch($IP, $CIDR) { 
        if (is_array($CIDR)) {
            foreach ($CIDR as $cidr) {
                if (self::ipMatch($IP, $cidr))
                    return true;
            }
            return false;
        }
        if (strpos($CIDR, '/') === false) {
            return $CIDR === $IP;
        }
        list ($net, $mask) = explode ('/', $CIDR);
        return ( ip2long ($IP) & ~((1 << (32 - $mask)) - 1) ) === ( ip2long ($net) & ~((1 << (32 - $mask)) - 1) );
    }

    /**
     * Called prior to requestAction()
     *
     * @return Sl instance
     */
    static protected function _push($url = null) {
        $main = empty(self::$_instances);
        $newInstance = new Sl(
            $main,
            $url ? $url : r('//', '/', empty($_GET['url']) ? '/' : '/'.$_GET['url'])
        );
        self::$_instances[] = $newInstance;
        return $newInstance;
    }

    /**
     * Called after requestAction()
     */
    static public function pop() {
        return array_pop(self::$_instances);
    }

    /**
     * @return Sl instance
     */
    static public function getInstance($main = false) {
		$count = count(self::$_instances);
		if ($count === 0) {
			return self::_push();
        }
        return self::$_instances[$main ? 0 : $count-1];
    }

    /**
     * True if this is the first instance
     *
     * @var bool
     */
    public $main = false;

    /**
     * Current request URI
     *
     * @var string
     */
    public $url = null;

    /**
     * @var AppController
     */
    public $controller = null;

    /**
     * @var AppView
     */
    public $view = null;

    /**
     * @param bool $main
     */
    protected function  __construct($main, $url) {
        $this->main = $main;
        $this->url = $url;
    }

    /**
     * Request time
     *
     * @param bool $request Set to true to include request processing
     * @return float
     */
    public static function uptime($request = false) {
        if (!$request) {
            global $TIME_START;
            $startTime = $TIME_START;
		} else {
			$startTime = env('REQUEST_TIME');
		}
		return $startTime ? round(getMicrotime() - $startTime, 4) : '?';
	}

    protected static $_here;

    /**
     * Check whether $url is equivalent to the currently active address
     *
     * @var mixed $url
     *
     * @deprecated Move to SLRouter
     */
    static public function isHere($url) {
        if (empty(self::$_here)) {
            self::$_here = r(' ', '+', urldecode(env('REQUEST_URI')));
        }
        return self::$_here == r(' ', '+', self::url($url));
    }

    /**
     * Used for boosting link generation performance
     *
     * @var array
     */
    private static $__linkCache = array();

    /**
     * Extended Router::url()
     *
     * @param mixed $url Set to bool (true = 'with base' or false) to get current requestUri
     * @param bool $full
     * @return string
     * @static
     */
    static public function url($url = null, $full = false) {
        $hash = serialize($url).$full;
        if (isset(self::$__linkCache[$hash])) {
            return self::$__linkCache[$hash];
        }

        if (is_array($url)) {
            if (!array_key_exists('lang', $url) || $url['lang'] === true) {
                $url = am($url, array('lang' => SlConfigure::read('I18n.lang')));
            }
            elseif (!is_string($url['lang'])) {
                unset($url['lang']);
            }
            
            if (!array_key_exists('ext', $url) && SlConfigure::read('Router.htmlExtension')) {
                $url['ext'] = 'html';
            }
        }
        elseif (is_string($url)) {
            if (strpos($url, '/:lang') !== false) {
                $url = r('/:lang', '/'.SlConfigure::read('I18n.lang'), $url);
            }
        }
        else {
            $noBase = $url === false;
            $full = (bool)$url;
            $url = isset($this) && $this instanceof Sl ? $this->url : self::getInstance()->url;
            if ($noBase) {
                return $url;
            }
        }

        // use trim() to remove a trailing space cake sometimes appends
        return self::$__linkCache[$hash] = trim(Router::url($url, $full));
    }
    
    /**
     * Set current language
     *
     * @param string $lang
     * @return bool Success
     */
    static public function setLocale($lang = null, $setCookie = false) {
        $locales = SlConfigure::read('I18n.locales');
        if (empty($locales)) {
            $languages = SlConfigure::read1('I18n.languages');
            $langs = array_keys($languages);
            $locales = array();
            $catalogs = array();
            if (!$langs) {
                $langs = array('en');
            }
            App::import('Core', 'l10n');
            $l10n = new L10n();
            foreach($langs as $lang_) {
                $catalog = $l10n->catalog($lang_);
                if ($catalog) {
                    $catalogs[$lang_] = $catalog;
                    $locales[$lang_] = $catalog['locale'];
                }
            }
            $langs = array_keys($locales);
            
            SlConfigure::write('I18n.langs', $langs);
            SlConfigure::write('I18n.catalogs', $catalogs);
            SlConfigure::write('I18n.locales', $locales);

            if (empty($lang)) {
                $lang = SlCookie::read('I18n.lang');

                // guess language based on Accept-Language header
                if (empty($lang)) {
                    $envLangs = explode(',', env('HTTP_ACCEPT_LANGUAGE'));
                    foreach ($envLangs as $envLang) {
                        list($envLang) = explode(';', $envLang);
                        if (isset($locales[$envLang])) {
                            $lang = $envLang;
                            break;
                        }
                    }

                    if (empty($lang)) {
                        $lang = SlConfigure::read('I18n.lang');
                    }
                }

                // convert locale_id to lang_id
                $lang_ = array_search($lang, $locales);
                if ($lang_) {
                    $lang = $lang_;
                }

                if (empty($lang) || !isset($locales[$lang])) {
                    $lang = $langs[0];
                }
            }
        } else {
            $catalogs = SlConfigure::read('I18n.catalogs');
        }

        if ($lang) {
            // convert locale_id to lang_id
            $lang_ = array_search($lang, $locales);
            if ($lang_) {
                $lang = $lang_;
            }

            if (isset($locales[$lang])) {
                SlConfigure::write('I18n.lang', $lang);
                SlConfigure::write('I18n.catalog', $catalogs[$lang]);
                SlConfigure::write('I18n.locale', $locales[$lang]);
                Configure::write('Config.language', $locales[$lang]);
                if ($setCookie) {
                    SlCookie::write('I18n.lang', $lang, false, "+1 year");
                }
                SlConfigure::localeChanged();
                return true;
            }
        }
        return false;
    }

    /**
     * Generate uniqid (10 characters, alphanumeric, high entropy)
     *
     * @return string
     */
    static public function uniqid() {
        return 'sl'.substr(md5(uniqid('', true)), 0, 10);
    }

    /**
     * A more ajax and user friendly way to var_dump.
     * Use instead of pr()
     *
     * @param mixed $var
     * @param bool $useFireCakeOutsideViews
     */
    static function krumo($var, $options = array()) {
        $options = array(
             'fireCake' => true,
             'debug' => true,
        );

        if ($options['debug'] && !Configure::read()) {
            return;
        }

        if ($options['fireCake'] &&
            empty(Sl::getInstance()->view) &&
            class_exists('FireCake')
        ) {
            return FireCake::fb($var);
        }

        // force Pheme to keep the whitespace and line breaks
        echo "<!--{!preserveWhitespace}-->";

        App::import('vendor', 'krumo', array('file' => 'krumo/class.krumo.php'));
        return krumo::dump($var);
    }

}

/**
 * Alias for __($string, true) with some extensions
 *
 * @param string $string
 * @param array $args Formats the $string with these
 * @param string $domain Domain name or true for auto-detection
 * @return string
 */
function __t($string, $args = array(), $domain = true) {
    if (SlConfigure::read('I18n.locale') !== 'eng') {
        $new = is_string($domain) ? __d($domain, $string, true) : __($string, true);
        if ($domain === true && $new === $string) {
            foreach (SlConfigure::read('I18n.domains') as $domain) {
                $new = __d($domain, $string, true);
                if ($new !== $string) {
                    break;
                }
            }
        }
        $string = $new;
    }
    if ($args) {
        if (!class_exists('String')) {
            App::import('Core', 'String');
        }
        $string = String::insert($string, $args, array('before' => '{$', 'after' => '}'));
    }
    return $string;
}



if (!ob_get_level()) {
    ob_start();
}
