<?php

/**
 * Configuration engine
 *
 * Colection priority ranges:
 *        0: 'general'
 *       10: 'cookie'
 *       20: 'session'
 *       50: controller (by name)
 *    60-61: active plugin (by name, by controller)
 *       70: 'home' (by route)
 *       80: block/content zone (by name)
 *       90: block (by name and/or id)
 *  200-299: user, groups (by id)
 *  400-499: tags (by id)
 *  600-699: nodes (by id)
 *  800-899: active model (by name and id)
 *     1000: 'important'
 */
class SlConfigure {

    /**
     * Default colection(s) name(s), used in SLConfigure::read(...) method
     *
     * var mixed
     */
    static public $defaultCollection = 'global'; // you may change this to '*' to force default recursive behavior

    /**
     * True if initialized
     *
     * @var bool
     */
    static protected $_loaded = false;

    /**
     * History of collections sets
     *
     * @var array
     */
    static protected $_savedCollections = array();

    /**
     * List of active collections
     *
     * @var array
     */
    static protected $_collections = array();

    /**
     * List of active non-localized collections
     *
     * @var array
     */
    static protected $_collectionsNoLocale = array();

    /**
     * Collection indexed array of variables
     *
     * @var array
     */
    static protected $_data = array();

    /**
     * Collection indexed array of variables
     *
     * @var array
     */
    static protected $_persistentData;

    /**
     * List of non-empty active collections
     *
     * @var array
     */
    static protected $_collectionsWithData;

    /**
     * Current locale
     *
     * @var string
     */
    static protected $_locale;

    /**
     * Refresh the list of non-empty collections.
     *
     * To be called after data change
     */
    static protected function _refresh() {
        self::$_collectionsWithData = array();
        foreach (self::$_collections as $collection) {
            if (!empty(self::$_data[$collection])) {
                self::$_collectionsWithData[] = $collection;
            }
        }
    }

    /**
     * Load from file.
     *
     * PHP, YAML and JSON formats supported
     *
     * @param string $filename
     * @return bool
     */
    static public function load($filename, $merge = true) {
        if (file_exists(CONFIGS . $filename)) {
            $filename = CONFIGS . $filename;
        }
        elseif (!file_exists($filename)) {
            return false;
        }

        if (preg_match('/\.php$/', $filename)) {
            include $filename;
        }
        elseif (preg_match('/\.yml$/', $filename)) {
            if (App::import('Vendor', 'Yaml.Spyc')) {
                $config = SPYC::YAMLload(file_get_contents($filename));
            }
        }
        elseif (preg_match('/\.js(on)?$/', $filename)) {
            $config = json_decode(file_get_contents($filename), true);
        }
        else {
            return; // null
        }

        if (isset($config) && is_array($config)) {
            if ($merge) {
                self::$_data = self::_merge(self::$_data, $config);
                self::_refresh();
            }
            return $config;
        }
        return true;
    }

    static protected function _load() {
        self::$_loaded = true;

        // load core configurations
        self::load('default.php');

        // load extensions' configurations
        SlExtensions::getInstance();
        
        if (!self::load('site.php')) {
            self::load('site.sample.php');

            $isHtml = isset($_GET['url']) && !preg_match('!(^|/)(js|css|img)/|\.(rss|xml|atom)|/isAjax:1$!', '/'.$_GET['url']);
            if (!Configure::read() && $isHtml && env('HTTP_X_REQUESTED_WITH') !== "XMLHttpRequest") {
                self::write('Message.noConfig', '<b>Site configuration file not found!</b><br />Please create <em>/app/config/site.php</em>. You may use <em>/app/config/site.sample.php</em> as a template.');
            }
        }

        self::$_persistentData = self::load(TMP . 'sl_configuration.php');

        if (SlSession::started()) {
            self::$_data['session'] =& SlSession::$data;
        }
        SlCookie::ready();

        self::setCollections();
        Sl::setLocale();

        // If you are on PHP 5.3 correct your server timezone to fix the date & time related errors.
        $tz = self::read('I18n.options.timeZone');
        if ($tz) {
            date_default_timezone_set($tz);
        }
    }

    /**
     * Save changes to disk (persistence)
     */
    static protected function _flush() {
        $data = var_export(self::$_persistentData, true);
        file_put_contents(TMP . 'sl_configuration.php', "<?php \$config = $data;");
    }

    /**
     * Is called automatically by Sl::setLocale()
     */
    static public function localeChanged() {
        $temp = self::read('I18n.locale');
        if ($temp != self::$_locale) {
            self::$_locale = $temp;
            self::setCollections(self::$_collectionsNoLocale, false);
        }
    }

    /**
     * Clear a collection
     *
     * @param string $collection
     * @param bool $persist
     */
    static public function clear($collection, $persist = false) {
        unset(self::$_data[$collection]);
        if ($persist) {
            unset(self::$_persistentData[$collection]);
            self::_flush();
        }
        self::_refresh();
    }

    /**
     * Restore a previous collections set
     *
     * @param string $key
     * @return bool Success
     */
    static public function restoreCollections($key) {
        if (!empty(self::$_savedCollections[$key])) {
            self::$_collections = self::$_savedCollections[$key];
            self::_refresh();
            return true;
        }
        return false;
    }

    static function addCollections($collections = array()) {
        return self::setCollections(am($collections, self::$_collectionsNoLocale), false);
    }

    /**
     * Set the list of active collections
     *
     * @param array $collections names
     * @param bool $setDefault
     *
     * @return string Key to be used in restoreColections(...)
     */
    static public function setCollections($collections = array(), $setDefault = true) {
        if ($setDefault) {
            if (SlExtensions::loaded('Auth')) {
                $user = SlAuth::user();
                if (isset($user['id'])) {
                    $groups = SlSession::read('Auth.groups');
                    $collections["users"] = 200;
                    if ($groups) {
                        foreach ($groups as $i => $group) {
                            $collections["Group{$group['id']}"] = 201 + $i;
                        }
                    }
                    $collections["User{$user['id']}"] = 299;
                } else {
                    $collections["guest"] = 299;
                }
            }

            $controller = Sl::getInstance()->controller;
            if ($controller) {
                $collections["{$controller->name}Controller"] = 50;
                if (!empty($controller->params['home'])) {
                    $collections['home'] = 70;
                }
                if (!empty($controller->params['plugin'])) {
                    $plugin = Inflector::camelize($controller->params['plugin']);
                    $collections["{$plugin}Plugin"] = 60;
                    $collections["{$plugin}{$controller->name}"] = 61;
                }
            }

            $collections = am(array('important' => 1000, 'cookie' => 10, 'session' => 20, 'global' => 0), $collections);
        }
        
        $collections = Set::normalize($collections);
        arsort($collections);
        self::$_collectionsNoLocale = $collections;
        
        $localizedCollections = array();
        foreach ($collections as $collection => $priority) {
            if (self::$_locale) {
                $localizedCollections[] = $collection.".".self::$_locale;
            }
            $localizedCollections[] = $collection;
        }

        $key = self::rememberCollections();
        self::$_collections = $localizedCollections;
        self::_refresh();
        return $key;
    }

    public static function rememberCollections() {
        $key = md5(serialize(self::$_collections));
        self::$_savedCollections[$key] = self::$_collections;
        return $key;
    }

    /**
     * Automatically determine active collections based on the passed $item
     *
     * @param mixed $item
     */
    static public function setCollectionsMagic($item = null) {
        $controller = Sl::getInstance()->controller;

        $collections = array();
        if (is_string($item)) {
            $collections[] = $item;
        }
        else {
            if ($controller) {
                if (isset($item[$controller->modelClass]['id'])) {
                    $collections["$controller->modelClass{$item[$controller->modelClass]['id']}"] = 899;
                }
                
                if (empty($item['nodes']) && !empty($item['path'][0][$controller->modelClass]['id'])) {
                    $ids = Set::extract("{n}.$controller->modelClass.id", $data['path']);
                    foreach ($ids as $i => $id) {
                        $collections["$controller->modelClass$id"] = 800 + $i;
                    }
                }
            }

            if (!empty($item['nodes'])) {
                foreach ($item['nodes'] as $i => $nodeId) {
                    $collections["Node$nodeId"] = 600 + $i;
                }
            }
            if (!empty($item['tags'])) {
                foreach ($item['tags'] as $i => $tagId) {
                    $collections["Tag$tagId"] = 400 + $i;
                }
            }

            if (empty($collections) && is_array($item) && Set::numeric($item) && Set::countDim($item, true) == 1) {
                $collections = $item;
            }
        }

        return self::setCollections($collections);
    }

    /**
     * Read a variable and strip markers
     *
     * @param string $name
     * @return mixed
     */
    static public function read1($name) {
        return self::read($name, is_array(self::$defaultCollection) ? self::$defaultCollection : array(self::$defaultCollection));
    }

    /**
     * Read a variable from active collections
     *
     * @param string $name
     * @return mixed
     */
    static public function read2($name) {
        return self::read($name, '*');
    }

    static protected function stripMergeMarkers(&$data) {
        if (is_array($data)) {
            unset($data['!merge']);
            foreach ($data as &$item) {
                if (is_array($item)) {
                    self::stripMergeMarkers($item);
                }
            }
        }
    }

    /**
     * Reads a variable or the list of active collections if $name is null
     *
     * @param string $name
     * @param mixed $collection Collection name(s);
     *      to merge variables from all active collections, set to '*'
     * @param bool $onlyPersistent True to read only persistent data
     * @return mixed
     */
    static public function read($name = null, $collection = null, $onlyPersistent = false) {
        if (!self::$_loaded) {
            self::_load();
        }

        if ($name === null) {
            if ($collection == 'populated') {
                return $onlyPersistent ?
                    array_keys(self::$_persistentData) :
                    array_keys(self::$_data);
            }
            return self::$_collections;
        }

        if (empty($collection)) {
            $collection = self::$defaultCollection;
        }

        if ($collection == '*' || is_array($collection)) {
            $result = null;
            $noResult = true;
            if (!is_array($collection)) {
                $collection = self::$_collectionsWithData;
            }
            foreach ($collection as $c) {
                $temp = self::read($name, $c, $onlyPersistent);
                if (is_array($temp)) {
                    $result = $noResult ? $temp : self::_merge($temp, $result);
                    $noResult = false;
                }
                elseif ($temp !== null) {
                    return $temp;
                }
            }
            self::stripMergeMarkers($result);
            return $result;
        }

        $collection = (string)$collection;
        if (!isset(self::$_data[$collection])) {
            return null;
        }
        if ($onlyPersistent) {
            $addr =& self::$_persistentData[$collection];
        } else {
            $addr =& self::$_data[$collection];
        }
        if (empty($name)) {
            return $addr;
        }

        $keys = explode('.', $name);
        foreach ($keys as $key) {
            if (!is_array($addr) || !isset($addr[$key])) {
                return null;
            }
            $addr =& $addr[$key];
        }
        return $addr;
    }

    /**
     * Merge a variable with the new $value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $persist True to make cross-request and cross-session persistent
     * @param string $collection
     */
    static public function merge($name, $value, $persist = false, $collection = 'global') {
        self::write(
            $name,
            self::_merge(
                self::read($name, $collection),
                $value
                ),
            $persist,
            $collection
            );
    }

    /**
     * Write a variable
     *
     * @param string $name
     * @param mixed $value
     * @param bool $persist True to make cross-request and cross-session persistent
     * @param string $collection
     */
    static public function write($name, $value, $persist = false, $collection = 'global') {
        if (!self::$_loaded) {
            self::_load();
        }

        $collection = (string)$collection;
        if (!isset(self::$_data[$collection])) {
            self::$_data[$collection] = array();
        }
        $addr =& self::$_data[$collection];

        $keys = explode('.', $name);
        foreach ($keys as $key) {
            if (!is_array($addr)) {
                $addr = array();
            }
            if (!isset($addr[$key])) {
                $addr[$key] = array();
            }
            $addr =& $addr[$key];
        }
        $addr = $value;

        if ($persist) {
            if (!isset(self::$_persistentData[$collection])) {
                self::$_persistentData[$collection] = array();
            }
            $addr =& self::$_persistentData[$collection];

            foreach ($keys as $key) {
                if (!is_array($addr)) {
                    $addr = array();
                }
                if (!isset($addr[$key])) {
                    $addr[$key] = array();
                }
                $addr =& $addr[$key];
            }
            $addr = $value;
            self::_flush();
        }
        self::_refresh();
    }

    /**
     * Delete a variable
     *
     * @param string $name
     * @param bool $persist
     * @param string $collection
     * @return bool True if variable found
     */
    static public function delete($name, $persist = false, $collection = 'global') {
        if (!self::$_loaded) {
            self::_load();
        }

        if ($collection == '*' || is_array($collection)) {
            $result = false;
            if (!is_array($collection)) {
                $collection = self::$_collectionsWithData;
            }
            foreach ($collection as $c) {
                $result = self::delete($name, $persist, $c) || $result;
            }
            return $result;
        }

        $collection = (string)$collection;
        if (!isset(self::$_data[$collection])) {
            return false;
        }
        $addr =& self::$_data[$collection];

        $keys = explode('.', $name);
        foreach ($keys as $key) {
            if (!is_array($addr) || !isset($addr[$key])) {
                return false;
            }
            $lastAddr =& $addr;
            $addr =& $addr[$key];
        }
        unset($lastAddr[$key]);
        self::_refresh();

        if ($persist) {
            if (!isset(self::$_persistentData[$collection])) {
                self::$_persistentData[$collection] = array();
            }
            $addr =& self::$_persistentData[$collection];

            foreach ($keys as $key) {
                if (!is_array($addr) || !isset($addr[$key])) {
                    return true;
                }
                $lastAddr =& $addr;
                $addr =& $addr[$key];
            }
            unset($lastAddr[$key]);
            self::_flush();
        }
        return true;
    }

    /**
     * Adds an item at the beginning of an array-type variable
     *
     * @param string $name Variable name of the (parent) array that the item will be added to
     * @param mixed $value
     * @param bool $persist
     * @param string $collection
     * @param int $offset Position to insert the item at
     */
    static public function unshift($name, $value, $persist = false, $collection = 'global') {
        self::insert($name, $value, $persist, $collection, 0);
    }

    /**
     * Alias for SLConfigure::add(...)
     */
    static public function append($name, $value, $persist = false, $collection = 'global') {
        self::insert($name, $value, $persist, $collection);
    }

    /**
     * Adds an item to an array-type variable
     *
     * @param string $name Variable name of the (parent) array that the item will be added to
     * @param mixed $value
     * @param bool $persist
     * @param string $collection
     * @param int $offset Position to insert the item at
     */
    static public function insert($name, $value, $persist = false, $collection = 'global', $offset = false) {
        if (!self::$_loaded) {
            self::_load();
        }

        $collection = (string)$collection;
        if (!isset(self::$_data[$collection])) {
            self::$_data[$collection] = array();
        }
        $addr =& self::$_data[$collection];

        $keys = explode('.', $name);
        foreach ($keys as $key) {
            if (!is_array($addr)) {
                $addr = array();
            }
            if (!isset($addr[$key])) {
                $addr[$key] = array();
            }
            $addr =& $addr[$key];
        }
        if (!is_array($addr)) {
            $addr = array();
        }
        if (is_string($offset)) {
            $offset = array_search($offset, array_keys($addr));
        }
        if ($offset === false) {
            $addr[] = $value;
        }
        else {
            array_splice($addr, $offset, 0, array($value));
        }


        if ($persist) {
            if (!isset(self::$_persistentData[$collection])) {
                self::$_persistentData[$collection] = array();
            }
            $addr =& self::$_persistentData[$collection];

            foreach ($keys as $key) {
                if (!is_array($addr)) {
                    $addr = array();
                }
                if (!isset($addr[$key])) {
                    $addr[$key] = array();
                }
                $addr =& $addr[$key];
            }
            if (!is_array($addr)) {
                $addr = array();
            }
            if ($offset === false) {
                $addr[] = $value;
            }
            else {
                array_splice($addr, $offset, 0, array($value));
            }
            self::_flush();
        }
        self::_refresh();
    }

    /**
     * This function can be thought of as a hybrid between PHP's array_merge and array_merge_recursive. The difference
     * to the two is that if an array key contains another array then the function behaves recursive (unlike array_merge)
     * but does not do if for keys containing strings (unlike array_merge_recursive). See the unit test for more information. <br><br>
     *
     * Note: This function will uses a special '!merge' flag for defining data conflict resolution
     *
     * @param array $arr1 Array to be merged
     * @param array $arr2 Array to merge with
     * @return array Merged array
     * @access public
     * @static
     */
    static protected function _merge($arr1, $arr2) {
        $mergeRule = isset($arr1['!merge']) ? $arr1['!merge'] : 'default';
        $mergeRule = isset($arr2['!merge']) ? $arr2['!merge'] : $mergeRule;

        if (!is_array($arr2) || !is_array($arr1) || $mergeRule === false || $mergeRule == 'overwrite') {
            $arr1 = $arr2;
        }
        else { // default
            foreach ($arr2 as $key => $val) {
                if (is_array($val) && isset($arr1[$key]) && is_array($arr1[$key])) {
                    $arr1[$key] = self::_merge($arr1[$key], $val);
                /*} elseif (is_int($key)) {
                    $arr1[] = $val;*/
                } else {
                    $arr1[$key] = $val;
                }
            }
        }
        return $arr1;
    }

}
