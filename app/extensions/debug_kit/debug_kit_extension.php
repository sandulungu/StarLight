<?php

/**
 * DebugKit integration
 */
class DebugKitExtension extends SlExtension {

    /**
     * @param AppController $controller
     * @return bool
     */
	public function constructClasses($controller) {

        // load FireCake (see FirePhp)
        if (!function_exists('firecake') && Configure::read() && strpos(env('HTTP_USER_AGENT'), 'FirePHP') !== false) {
            App::import('Vendor', 'DebugKit.FireCake');
        }

        // load toolbar
        if (empty($controller->params['bare']) && SlConfigure::read('Sl.debug.debugKit') && Configure::read()) {
            
            // IE6 not supported
            if (strpos(env('HTTP_USER_AGENT'), 'MSIE 6') === false) {

                // sometimes needed in Ajax requests by DebugKit
                $controller->helpers[] = 'Form';
                
                $controller->components['DebugKit.Toolbar'] =
					in_array('Interactive', App::objects('plugin')) ?
					array('panels' => array('Interactive.interactive')) :
					array();
            }
        }
        return true;
    }
}
