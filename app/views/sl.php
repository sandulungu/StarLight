<?php

App::import('lib', 'pheme/bridge');

/**
 * A custom view class that is used for themeing
 *
 * @property-read SlHelper $Sl
 * @property-read HtmlHelper $Html
 * @property-read JavascriptHelper $Javascript
 * @property-read SessionHelper $Session
 * @property-read ValidationHelper $Validation
 */
class SlView extends View {

    /**
     * @var array
     */
    protected $_cachedPaths = array();

    /**
     * Params passed to the last AppView::element() call
     *
     * @var array
     */
    public $elementParams = array();

    /**
     *
     * @var int
     */
    public $id;

	/**
	 * Class constructor
	 *
	 * @param AppController $controller
	 */
	public function __construct ($controller) {
        Sl::getInstance()->view = $this;

		parent::__construct($controller);
        
		$this->theme =& $controller->theme;
        $this->id =& $controller->id;
	}
	
	/**
	 * Return all possible paths to find view files in order.
     * We may have a 'themed' folder under /app/views for theme overrides
	 *
	 * @param string $plugin
	 * @return array paths
	 * 
	 * @access protected
	 */
	function _paths($plugin = null, $cached = true) {
        if ($cached === true && !empty($this->_cachedPaths[$plugin])) {
			return $this->_cachedPaths[$plugin];
		}
        
		$paths = parent::_paths($plugin, false);
//        $pluginPath = App::pluginPath($plugin);
//
//        // core non-themed views
//        array_splice($paths, $plugin ? 3 : 1, 0, VIEWS.'actions/');
//
//        if ($plugin) {
//            // plugin non-themed views
//            $path = $pluginPath . "views/";
//            array_splice($paths, 2, 0, $path.'actions/');
//            $path = VIEWS . "plugins/$plugin/";
//            array_splice($paths, 1, 0, $path.'actions/');
//        }

		if ($this->theme) {
            // themed views (core app)
			$path = VIEWS . "themed/$this->theme/";
//            array_unshift($paths, $path.'actions/');
			array_unshift($paths, $path);

            // themed views (plugins)
            if ($plugin) {
                $path = VIEWS . "themed/$this->theme/plugins/$plugin/";
//                array_unshift($paths, $path.'actions/');
                array_unshift($paths, $path);
            }
        }

        $this->_cachedPaths[$plugin] = $paths;
		return $paths;
	}

    /**
     * Renders a piece of PHP with provided parameters and returns HTML, XML, or any other string.
     *
     * This realizes the concept of Pheme skins and blocks.
     *
     * @param string $name
     * @param array $plugin
     * @return string Skin text
     */
	public function pheme($name, $plugin = null) {
		if (isset($this->plugin) && !$plugin) {
			$plugin = $this->plugin;
		}

        $eval = true;
		$paths = $this->_paths($plugin);
        $file = null;
		foreach ($paths as $path) {
			if (file_exists("{$path}$name{$this->ext}")) {
				$file = "{$path}$name{$this->ext}";
				break;
			}
			if (file_exists("{$path}$name.php")) {
				$file = "{$path}$name.php";
				break;
			}
            elseif (file_exists("{$path}$name.html")) {
				$file = "{$path}$name.html";
                $eval = false;
				break;
			}
		}

		if (is_file($file)) {
            if ($eval) {
                ob_start();
                include $file;
                return ob_get_clean();
            } else {
                return file_get_contents($file);
            }
		}
	}

    /**
     * Renders a piece of PHP with provided parameters and returns HTML, XML, or any other string.
     *
     * This realizes the concept of Elements, (or "partial layouts")
     * and the $params array is used to send data to be used in the
     * Element.  Elements can be cached through use of the cache key.
     *
     * @param string $name Name of template file in the/app/views/elements/ folder
     * @param array $params Array of data to be made available to the for rendered
     *                      view (i.e. the Element)
     *    Special params:
     *		cache - enable caching for this element accepts boolean or strtotime compatible string.
     *      Can also be an array
     *				if an array,'time' is used to specify duration of cache.  'key' can be used to
     *              create unique cache files.
     *
     * @return string Rendered Element
     * @access public
     */
    public function element($name, $params = array(), $loadHelpers = false) {
        $this->elementParams = $params;

        $result = parent::element($name, $params, $loadHelpers);
        
        return strpos($result, "Not Found: ".ROOT) === 0 ?
            "<div class='error'>$result</div>" :
            $result;
    }

    /**
     * Renders a layout. Returns output from _render(). Returns false on error.
     * Several variables are created for use in layout.
     *	title_for_layout - contains page title
     *	content_for_layout - contains rendered view file
     *	scripts_for_layout - contains scripts added to header
     *  cakeDebug - if debug is on, cake debug information is added.
     *
     * @param string $content_for_layout Content to render in a view, wrapped by the surrounding layout.
     * @return mixed Rendered output, or false on error
     */
    function renderLayout($content_for_layout, $layout = null) {
        unset($this->viewVars['cakeDebug']); // we have better debugging tools than this
        
        return parent::renderLayout($content_for_layout, $layout);
    }

    /**
     * Renders view for given action and layout. If $file is given, that is used
     * for a view filename (e.g. customFunkyView.ctp).
     *
     * @param string $action Name of action to render for
     * @param string $layout Layout to use
     * @param string $file Custom filename for view
     * @return string Rendered Element
     */
     public function render($action = null, $layout = null, $file = null) {
        if ($layout === null) {
            $layout = $this->layout;
        }
        if ($layout && $this->autoLayout) {
            Pheme::push("layouts/pheme/$layout");
        }

        $data = parent::render($action, $layout, $file);

        if ($layout && $this->autoLayout) {
            Pheme::pop();
        }

        // Save title for cross-request manipulations
        SlConfigure::write(
            'View.lastRenderTitle',
            empty($this->viewVars['title']) ? null : $this->viewVars['title']
        );
        
        return $data;
    }

}
