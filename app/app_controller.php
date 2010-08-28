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

    protected function _getPassedDefaults() {
        $data = array();
        foreach ($this->params['named'] as $param => $value) {
            if ($this->{$this->modelClass}->schema($param)) {
                $data[$this->modelClass][$param] = $value;
            }
        }
        return $data;
    }

    public function beforeFilter() {
        SlConfigure::setCollections();
        
        if (isset($this->data[$this->modelClass]['id'])) {
            $this->id = $this->data[$this->modelClass]['id'];
        } 
        elseif(isset($this->params['pass'][0])) {
            $this->id = $this->params['pass'][0];
        }

        if (!empty($this->params['named']['ref'])) {
            SlSession::write('Routing.ref', base64_decode($this->params['named']['ref']));
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

        $this->theme = SlConfigure::read2('View.theme');

        if (empty($this->viewVars['title'])) {
            $model = $this->_humanizedModelClass();

            switch ($this->action) {
                case 'index':
                case 'admin_index':
                    $this->set('title', __t(Inflector::pluralize($model)));
                    break;

                case 'admin_add':
                    $this->set('title', __t($this->id ? 'Clone {$model}' : 'Add {$model}', array('model' => __t($model))));
                    break;

                case 'admin_edit':
                    $this->set('title', __t('Edit {$model}',array('model' => __t($model))));
                    break;

                default:
                    $this->set('title', null);
            }
            
        }
        elseif (empty($this->viewVars['title_for_layout'])) {
            $this->viewVars['title_for_layout'] = $this->viewVars['title'];
        }
        
        // merge 'site title' and 'view title'
        if (empty($this->viewVars['title_for_layout'])) {
            $this->viewVars['title_for_layout'] = SlConfigure::read2('Site.title');
        } else {
            $this->viewVars['title_for_layout'] .= SlConfigure::read('View.options.titleSep') . SlConfigure::read2('Site.title');
        }

        if (Sl::getInstance()->main && ob_get_level()) {
            SlConfigure::write('View.bufferedOutput', ob_get_clean());
        }
        
        if (class_exists('Debugger')) {
            Debugger::output('js');
        }
    }

    function _humanizedModelClass() {
        $prefix = SlConfigure::read2('View.options.modelPrefix');
        if (empty($prefix)) {
            $prefix = $this->plugin;
        }
        return Inflector::humanize(preg_replace("/^{$prefix}_/", '', Inflector::underscore($this->modelClass)));
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
    public function redirect($url, $status = null, $useReferer = true) {

        if ($useReferer) {
            $ref = SlSession::read('Routing.ref');
            if ($ref) {
                $url = $ref;
            }
        }

        // cyclic check
        if (Sl::url($url) === Sl::url()) {
            die('Infinite redirection loop detected.');
        }

        // code inspired from RequestHandlerComponent
        if ($this->RequestHandler->isAjax()) {
            foreach ($_POST as $key => $val) {
                unset($_POST[$key]);
            }
            echo Sl::requestAction($url, array('requested' => false));
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

  	public function cakeError($method = 'error500', $messages = array()) {
        return parent::cakeError($method, $messages);
    }



    //////////////////////////////// CRUD //////////////////////////////////////


    
    protected function _admin_index() {
        $options = array(
            'conditions' => $this->postConditions($this->_getPassedDefaults()),
        );

        $this->set(Inflector::pluralize(Inflector::variable($this->modelClass)), $this->{$this->modelClass}->find('all', $options));
    }

    protected function _admin_view() {
        $this->set(Inflector::variable($this->modelClass), $data = $this->{$this->modelClass}->read(null, $this->id));

        $model = $this->_humanizedModelClass();
        $this->set('title', __t($model) . ' "' . $data[$this->modelClass][$this->{$this->modelClass}->displayField] . '"');
    }

    protected function _admin_edit() {
        $this->helpers[] = 'JsValidate.Validation';
        $this->{$this->modelClass};

        if ($this->data) {
            if ($this->{$this->modelClass}->saveAll($this->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        elseif ($this->id) {
            $this->data = $this->{$this->modelClass}->read(null, $this->id);
        }

        if (empty($this->data)) {
            $this->data = $this->_getPassedDefaults();
        }
    }

    protected function _admin_delete() {
        $this->{$this->modelClass}->delete($this->id, true);
        $this->redirect(array('action' => 'index'));
    }

    protected function _admin_add() {
        $this->admin_edit();
        $this->render('admin_edit');
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
        if (SlExtensions::getInstance()->dependencyError) {
            $params = SlExtensions::getInstance()->dependencyError;
            SlExtensions::getInstance()->dependencyError = false;
            $this->cakeError('missingDependence', $params);
        }

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

