<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */

// check installation
if (Sl::version() != SlConfigure::read('Sl.version')) {
    if (!preg_match('!/install|/users/login!', Sl::url(false))) {
        SlConfigure::write('Message.migrate', array(
            'message' => __t('System files have been recently updated. Proceeding to database migration...'),
            'params' => array('class' => 'message')
        ));
        Router::connect(Sl::url(false), array('controller' => 'install', 'action' => 'migrate'));
    }
}

// localized routes
$langRules = array('lang' => implode('|', SlConfigure::read('I18n.langs')));

// home
$home = SlConfigure::read1('Routing.home');
Router::connect('/', $home);
Router::connect('/:lang', $home, $langRules);

// prefixed homes
$prefixedRoutes = SlConfigure::read('Routing.prefixes');
foreach ($prefixedRoutes as $prefix => $route) {
    Router::connect("/$prefix", $route);
}

// custom routes
$routes = SlConfigure::read('Routing.routes');
foreach ($routes as $expr => $route) {
    Router::connect($expr, $route);
    Router::connect("/:lang$expr", $route, $langRules);
}

Router::connect('/:lang/:plugin/:controller/:action/*', array(), $langRules);
Router::connect('/:lang/:controller/:action/*', array(), $langRules);

Router::parseExtensions();