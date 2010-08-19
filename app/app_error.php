<?php

/**
 * Application-wide error handler.
 *
 * @property-read AppController $controller
 */
class AppError extends ErrorHandler {

    /**
     * True if the current context is requested => must not halt execution
     *
     * @var bool
     */
    protected $_requested = false;

	/**
	 * Class constructor.
	 *
	 * @param string $method Method producing the error
	 * @param array $messages Error messages
	 */
	function __construct($method, $messages) {
		$this->_requested = !SL::getInstance()->main;

        // Make a more user-friendly "No Database Connection"
        if ($method === 'missingTable') {
			$db = @ConnectionManager::getDataSource('default');
			if (!$db->isConnected()) {
				$method = 'dbConnectionError';
			}
		}
		
		parent::__construct($method, $messages);
	}

    /**
     *
     * @param array $params Parameters for controller
     * @access public
     */
	function missingDependence($params) {
		$this->controller->set(am($params, array(
			'title_for_layout' => __t('Extension dependency eror'),
			'title' => __t('Extension dependence eror'),
		)));
		$this->_outputMessage('missingDependence');
	}

    /**
     *
     * @param array $params Parameters for controller
     * @access public
     */
	function dbConnectionError() {
		extract(SlConfigure::read('Db.default'));

		$this->controller->set('title', __t(
            'No database connection to {$params} ({$password})',
            array(
                'params' => "$login@$host/$database",
                'password' => __t($password ? "uses password" : "no password"),
            )
        ));
		$this->_outputMessage('dbConnectionError');
	}

    /**
     * Convenience method to display a 403 page.
     *
     * @param array $params Parameters for controller
     * @access public
     */
	function error403($params) {
		extract($params, EXTR_OVERWRITE);

		if (!isset($url)) {
			$url = $this->controller->here;
		}
		$url = Router::normalize($url);
        
		$this->controller->header("HTTP/1.0 403 Forbidden");
		$this->controller->set(array(
			'title_for_layout' => __t('Forbidden'),
			'title' => __t('403 Forbidden'),
			'url' => h($url),
		));
		$this->_outputMessage('error403');
	}

    /**
     * Stop execution of the current script (only if the script is in the main dispatcher context)
     *
     * @param $status see http://php.net/exit for values
     */
    public function _stop($status = 0) {
        if ($this->_requested) {
            return;
        }
        parent::_stop($status);
    }

    /**
     * Output message
     *
     * @access protected
     */
    function _outputMessage($template) {

        $isAjax = env('HTTP_X_REQUESTED_WITH') === "XMLHttpRequest";
        if ($this->_requested || $isAjax) {
            $this->controller->autoLayout = false;
            $this->controller->params['bare'] = true;
        }

        // show 'page title' only once
        if (empty($this->controller->viewVars['title_for_layout']) && !empty($this->controller->viewVars['title'])) {
            $this->controller->viewVars['title_for_layout'] = $this->controller->viewVars['title'];
            $this->controller->viewVars['title'] = null;
        }

		parent::_outputMessage($template);
	}
}
