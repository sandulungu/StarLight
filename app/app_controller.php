<?php

/**
 *
 * @property-read SessionComponent $Session
 * @property-read CookieComponent $Cookie
 * @property-read CsvIoComponent $CsvIo
 * @property-read HqSmsComponent $HqSms
 * @property-read FacebookComponent $Facebook
 * @property-read RequestHandlerComponent $RequestHandler
 */
class AppController extends Controller {

    public $components = array('RequestHandler', 'Session', 'Cookie');

    public $helpers = array('Html', 'Javascript', 'SlHtml', 'SlForm', 'Theme');

    public $view = 'Sl';

    public $theme = null;

    /**
     * Id of current item or null; useful in *_add_edit composite actions
     *
     * @var int
     */
    public $id = null;

    public function __construct() {
        Sl::getInstance()->controller = $this;
        parent::__construct();
    }

    public function beforeFilter() {
        SlConfigure::setCollections();
        
        if (isset($this->data[$this->modelClass]['id'])) {
            $this->id = $this->data[$this->modelClass]['id'];
        } 
        elseif(isset($this->params['pass'][0])) {
            $this->id = $this->params['pass'][0];
        }

        // Make AJAX errors and warnings readable
        if (class_exists('Debugger')) {
            if ($this->RequestHandler->isAjax()) {
                Debugger::output('base');
            }
        }

        // update current language
        if (!empty($this->params['named']['lang'])) {
            $this->params['lang'] =& $this->params['named']['lang'];
        }
        if (!empty($this->params['lang'])) {
            Sl::setLocale($this->params['lang'], true);
        }

        $languages = SlConfigure::read('I18n.languages');
        $currLang = SlConfigure::read('I18n.lang');
        $languageLinks = array();
        foreach ($languages as $lang => $language) {
            $languageLinks[$lang] = array(
                'title' => $language,
                'active' => $lang == $currLang,
                'url' => am(
                    $this->passedArgs,
                    array('action' => $this->action, 'lang' => $lang)
                )
            );
        }
        SlConfigure::write('Navigation.languages', $languageLinks);
        
    }

    public function beforeRender() {
        //Sl::krumo($this->params);

        if ($this->RequestHandler->isAjax()) {
            if (is_array($this->output)) {
                $this->output = json_encode($this->output);
                $this->autoRender = false;
                SlConfigure::write('Sl.debug.requestTime', false);
                return;
            }
        }

        if ($this->layout == 'default') {
            $this->layout = SlConfigure::read('View.layout');
        }
        if (empty($this->layout)) {
            $this->layout = empty($this->params['prefix']) || 
                ($this instanceof CakeErrorController && $this->params['prefix'] != 'admin') ?
                'default' : $this->params['prefix'];
        }

        $this->theme = SlConfigure::read('View.theme');

        if (empty($this->viewVars['title'])) {
            $this->viewVars['title'] = null;
        }
        elseif (empty($this->viewVars['title_for_layout'])) {
            $this->viewVars['title_for_layout'] = $this->viewVars['title'];
        }
        
        // merge 'site title' and 'view title'
        if (empty($this->viewVars['title_for_layout'])) {
            $this->viewVars['title_for_layout'] = SlConfigure::read2('Site.title');
        } else {
            $this->viewVars['title_for_layout'] .= SlConfigure::read('View.options.titleSep') . SlConfigure::read('Site.title');
        }

        if (Sl::getInstance()->main && ob_get_level()) {
            SlConfigure::write('View.bufferedOutput', ob_get_clean());
        }
        
        if (class_exists('Debugger')) {
            Debugger::output('js');
        }
    }

    /**
     * Redirects to given $url, after turning off $this->autoRender.
     * Script execution is halted after the redirect.
     *
     * @param mixed $url A string or array-based URL pointing to another location within the app, or an absolute URL
     * @param integer $status Optional HTTP status code (eg: 404)
     * @access public
     * @link http://book.cakephp.org/view/425/redirect
     */
    public function redirect($url, $status = null) {

        // cyclic check
        if (Sl::url($url) === Sl::url()) {
            die('Infinite redirection loop detected.');
        }

        if ($this->RequestHandler->isAjax()) {
            
            // pop main SL instance and save it to prevent premature destruction
            if (Sl::getInstance()->main) {
                $SL = Sl::pop();
            }

            foreach ($_POST as $key => $val) {
                unset($_POST[$key]);
            }
            if (!empty($status)) {
                $statusCode = $controller->httpCodes($status);
                $code = key($statusCode);
                $msg = $statusCode[$code];
                $controller->header("HTTP/1.1 {$code} {$msg}");
            }
            echo Sl::requestAction($url, array('bare' => false));
    		$this->_stop();
        }

        // show a human readable redirect message allowing debug output to be read
        if (headers_sent() || ($this->output && Configure::read())) {
            $url = h(SL::url($url));
            if (empty($status)) {
                $status = 'null';
            }
            echo "<p style='background: #ff7; color: #000; padding: 1em;'>Redirect to <a href='$url'>$url</a> (code: $status) cancelled.</p>";
            while (ob_get_level()) {
                ob_end_flush();
            }
            $this->_stop();
		}

        parent::redirect(Sl::url($url, true), $status);
    }

  	function cakeError($method = 'error500', $messages = array()) {
        return parent::cakeError($method, $messages);
    }

    //////////////////////////////// HACKS /////////////////////////////////////

    /**
     * Loads Components and prepares them for initialization.
     * Models will be lazy loaded by default
     *
     * @return mixed true if models found and instance created, or cakeError if models not found.
     * @access public
     * @see Controller::loadModel()
     * @link http://book.cakephp.org/view/977/Controller-Methods#constructClasses-986
     */
	function constructClasses() {
        SlExtensions::trigger('constructClasses', $this);

		$this->__mergeVars();

        // Component class sets components by reference, hence triggering an uneeded read operation
        // Avoid magic __get() method calls by setting null values
        $this->components = Set::normalize($this->components);
        foreach ($this->components as $component => $settings) {
            if (strpos($component, '.') !== false) {
                list($plugin, $component) = explode('.', $component);
            }
            $this->{$component} = null;
        }

        if (!SlConfigure::read('Sl.options.lazyLoadModels')) {
            return parent::constructClasses();
        }

		$this->Component->init($this);

		if ($this->uses !== null || ($this->uses !== array())) {
			if ($this->uses) {
				$uses = is_array($this->uses) ? $this->uses : array($this->uses);
				$modelClassName = $uses[0];
				if (strpos($uses[0], '.') !== false) {
					list($plugin, $modelClassName) = explode('.', $uses[0]);
				}
				$this->modelClass = $modelClassName;
			}
		}
		return true;
	}

    public function __isset($assoc) {
        return $this->__get($assoc) !== null;
    }

    /**
     * Lazy loading for models magic
     *
     * @param string $assoc
     * @return AppModel
     */
    public function  __get($model) {
        if ($this->uses !== null || ($this->uses !== array())) {
			if ($this->uses === false) {
                if (empty($this->passedArgs) || !isset($this->passedArgs['0'])) {
                    $id = isset($this->data[$this->modelClass]['id']) ?
                        $this->data[$this->modelClass]['id'] : false;
                } else {
                    $id = $this->passedArgs['0'];
                }

                if ($model == $this->modelClass) {
                    $this->loadModel($this->modelClass, $id);
                    return $this->{$model};
                }
			} elseif ($this->uses) {
				$uses = is_array($this->uses) ? $this->uses : array($this->uses);

				foreach ($uses as $modelClass) {
                    $modelClassName = $modelClass;
                    if (strpos($modelClass, '.') !== false) {
                        list($plugin, $modelClassName) = explode('.', $modelClass);
                    }
                    if ($model == $modelClassName) {
                        $this->loadModel($modelClass);
                        return $this->{$model};
                    }
				}
			}
		}
    }

}

/**
 * Base class for all Sl components
 */
class SlComponent {

    /**
     *
     * @var AppController
     */
    public $controller = null;

    public $params = array();

    public $settings = array();

    /**
     * Set controller, settings and params properties
     *
     * @param AppController $controller
     * @param array $settings
     */
    protected function _initialize($controller, $settings = array()) {
        $this->settings = $settings;
        $this->controller = $controller;
        $this->params =& $controller->params;
    }
}

