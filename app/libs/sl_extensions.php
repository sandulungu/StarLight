<?php

/**
 * Extension singleton class
 *
 * Extensions are special plugins with callbacks and/or autoloaded configuration file(s).
 * Can be found in app/extensions folder
 */
class SlExtensions {

    /**
     * @var SlExtensions
     */
    static protected $_instance = null;

    /**
     * @var SlExtensions
     */
    static protected $_all = null;

    /**
     * @return SlExtensions
     */
    static public function getInstance() {
        if (self::$_instance === null) {
            SlConfigure::read(); // make sure core configuration is loaded BEFORE plugins'
        }
        if (self::$_instance === null) {
            self::$_instance = new SlExtensions();
        }
        return self::$_instance;
    }

    /**
     * Returns a list with active extensions
     *
     * @param bool $onlyActive Set to false to retrieve ALL the extensions
     * @return array
     */
    static public function all() {
        if (empty(self::$_all)) {
            self::$_all = Cache::read('extensions', 'sl');
        }
        
        if (empty(self::$_all)) {
            App::import('folder');
            $folder = new Folder(APP . 'extensions');
            $contents = $folder->read();
            self::$_all = $contents[0];
            Cache::write('extensions', self::$_all, 'sl');
        }

        return self::$_all;
    }

	/**
	 * List of loaded extensions
	 *
	 * @var array
	 */
	protected $_loaded = array();

    /**
     * Autoload extensions
     */
    function  __construct() {
        $extensions = self::all();
		foreach ($extensions as $extension) {
    		$this->_load($extension);
		}
    }

	/**
	 * Checks whether a extension has been loaded
	 *
	 * Returns the list of loaded extensions if $extensionName is empty
	 *
	 * @param string $extensionName
	 * @return bool
	 */
	static public function loaded($extensionName = null) {
		return $extensionName ? in_array($extensionName, self::getInstance()->_loaded) : self::getInstance()->_loaded;
	}

	/**
	 * Trigger a hook <br><br>
     *
     * Hooks should return true to continue, false to break
	 *
	 * @param string $hook
	 * @param array $options
	 * @param mixed $params
	 * @return bool
	 */
	protected function _trigger($hook, $params = null, $options = array()) {
		$options = am(array(
			'return' => $params !== null && !is_object($params),
            'defaultReturnValue' => true,
			'break' => true,
		), $options);

		$result = $options['return'] ? $params : false;
        $count = 0;
		foreach ($this->_loaded as $extension) {
			if ($this->$extension->enabled && method_exists($this->$extension, $hook)) {
				$result = $this->$extension->$hook($params);
                if ($result === null) {
                    $result = $options['defaultReturnValue'];
                }
				if ($options['return'] && !is_bool($result)) {
					$params = $result;
				}
				elseif ($options['break'] && $result === false) {
					return false;
				}
                $count++;
			}
		}
		return $options['return'] ? $params : true;
	}

    static public function trigger($hook, $params = null, $options = array()) {
        return SlExtensions::getInstance()->_trigger($hook, $params, $options);
    }

    /**
	 * Loads a extension and initializes it <br><br>
	 *
	 * If the hook return false the extension is deactivated for current controller instance
	 *
	 * @param string $name
	 * @param array $settings
     * @param bool $loadConfig
	 * @return bool
	 */
	protected function _load($name, $settings = null, $loadConfig = true) {
		$extensionPath = APP . 'extensions/' . Inflector::underscore($name) . '/';
        /*App::build(
            'locales' => array(
            'localePaths', am(Configure::read('localePaths'), array($extensionPath.'locale/')))
        );*/
        if ($loadConfig) {
            SlConfigure::load("{$extensionPath}config/".Inflector::underscore($name).".php");
        }
        $extensionPath .=  Inflector::underscore($name).'_extension.php';
		$name = Inflector::camelize($name);
		$className = $name.'Extension';
		if (in_array($name, $this->_loaded)) {
			return true;
		}
		if (is_readable($extensionPath)) {
			include_once $extensionPath;
		}
		if (class_exists($className)) {
			$this->_loaded[] = $name;
			$this->$name = new $className();
			return true;
		}
		return false;
	}
}

/**
 * Extension base class
 */
class SlExtension {

    /**
     * @var bool
     */
    public $enabled = true;
    
}
