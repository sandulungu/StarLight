<?php

/**
 * Sample site configuration file
 *
 * See /app/config/default.php for default core settings.
 * See /app/extensions/{$extension_name}/config/{$extension_name}.php for default extension settings.
 *
 * Some generic settings with possible values, usually defined per-site are commented bellow
 */

/*
 * Before using any new language the database tables are altered automatically,
 * so it will be best to configure the languages before deploying the site
 */
//$config['global']['I18n'] = array(
//    'languages' => array( // a list of active languages, format: $langCode => $localizedLanguageName
//        'en' => 'English',
//        'ru' => 'Русский',
//        'ro' => 'Română',
//        '!merge' => false, // exclude any languages defined in /app/comfig/default.php
//    ),
//    'lang' => 'ro', // default language to be used if Accept-Language header was not relevant
//);

/*
 * If using a custom theme, let the system know its name and the master css file
 *
 * Themes are located in /app/views/themed
 * Webroot overrides may be in either /app/views/themed/{$theme_name}/webroot or  /app/webroot/theme/{$theme_name}
 */
//$config['global']['View']['theme'] = 'theme_name';
//$config['global']['Asset']['css']['theme'] = 'css_filename';

/*
 * You may set here any database configurations you'll need.
 * This way, the user won't need to remeber the database password when setting up the site in the Web UI
 */
//$config['global']['Db'] = array(
//    'default' => array(
//        'driver' => 'mysql',
//        'persistent' => false,
//        'host' => 'localhost',
//        'login' => 'root',
//        'password' => '',
//        'database' => 'sl',
//        'prefix' => '',
//    ),
//);

