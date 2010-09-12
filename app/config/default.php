<?php

$config['global']['Sl'] = array(
    'version' => '2.1.alpha3',
    'session' => array(
        'name' => 'SlSession',
        'use_trans_sid' => 'false',
    ),
    'cookie' => array(
        'name' => 'SlCookie',
        'path' => '/',
        // 'domain' => null,
        // 'secure' => null,
    ),
    'options' => array(
        'lazyLoadModels' => true,
        'sslTransport' => true,
    ),
    'debug' => array(
    ),
);

$config['global']['Asset'] = array(
    'cdn' => array(
        'blend' => 'http://cdn.starlightcms.info/assets/blend/1.3',
        'bookmark' => 'http://cdn.starlightcms.info/assets/bookmark/1.3.1',
        'blueprint' => 'http://cdn.starlightcms.info/assets/blueprint/0.9_patched',
        'colorbox' => 'http://cdn.starlightcms.info/assets/colorbox/1.3.6',
        'cufon' => 'http://cdn.starlightcms.info/assets/cufon/1.09',
        'curvycorners' => 'http://cdn.starlightcms.info/assets/curvycorners/2.0.4',
        'ext3' => 'http://cdn.starlightcms.info/assets/ext/3.2.0',
        'filter-row' => 'http://cdn.starlightcms.info/assets/filter-row/0.4',
        'jcarousellite' => 'http://cdn.starlightcms.info/assets/jcarousellite/1.0.1_patched',
        'jquery' => 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2',
        'jquery-ui' => 'http://cdn.starlightcms.info/assets/jquery-ui/1.8.4',

        // Needs a policy file to access cross-domain xml playlists, see http://kb2.adobe.com/cps/142/tn_14213.html
        //'jwplayer' => 'http://cdn.starlightcms.info/assets/jwplayer/5.2',

        'lavalamp' => 'http://cdn.starlightcms.info/assets/lavalamp/0.1.0',
        'pngFix' => 'http://cdn.starlightcms.info/assets/pngFix/1.2',
        'prettyLoader' =>  'http://cdn.starlightcms.info/assets/prettyLoader/1.0.1',
        'prettyPhoto' => 'http://cdn.starlightcms.info/assets/prettyPhoto/2.5.6',
        'scrollTo' => 'http://cdn.starlightcms.info/assets/scrollTo/1.4.2',
        'sl' => 'http://cdn.starlightcms.info/assets/sl/2.1.alpha2',
        'superfish' => 'http://cdn.starlightcms.info/assets/superfish/1.4.8',
        'swfobject' => 'http://cdn.starlightcms.info/assets/swfobject/2.1',
    ),
    'options' => array(
        'alwaysUseCdn' => true,
        'timestamp' => true,
    ),
    'css' => array(
        'theme' => 'auto', // you theme main CSS file
    ),
    'js' => array(
        'jquery' => 'footer',
        'head' => array(
            // 'name' => array('weight' => 0, 'before' => '...code...', 'url' => 'asset/url', 'after' => '...code...'),
        ),
        'footer' => array(
        ),
        'ready' => array(
        ),
    ),
);

$config['global']['Block'] = array(
    'zones' => array(
        // 'Header' => 'Page header',
    ),
    'defaults' => array(
        // 'cache' => array('time' => 5 * 60, 'spread' => 60),
    ),
);

$config['global']['Config'] = array(
    'sections' => array(
        'site' => 'Site settings',
    ),
    'settings' => array(
        'site' => array(
            /*array(
                'name' => 'Configuration.option',
                // 'collection' => 'global', // default
                // 'collection' => 'user', // magically use current User's Collection
                // 'label' => '', // generated from name
            ),*/
            array(
                'name' => 'Site.title',
                'translate' => true,
            ),
            array(
                'name' => 'Site.mission',
                'translate' => true,
            ),
            array(
                'name' => 'Site.copyright',
                'translate' => true,
            ),
            array(
                'name' => 'Site.meta_keywords',
                'label' => 'Meta keywords',
                'translate' => true,
            ),
            array(
                'name' => 'Site.meta_description',
                'label' => 'Meta description',
                'type' => 'textarea',
                'translate' => true,
            ),
            array(
                'name' => 'Site.in_maintance_mode',
                'label' => 'Activate maintnance mode', 
                'type' => 'checkbox',
            ),
        ),
    ),
);

$config['global']['Db'] = array(
    'default' => array(
        'driver' => 'mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'root',
        'password' => '',
        'database' => 'sl',
        'prefix' => '',

        // Very important! If not specified, a manual conversion from you default
        // encoding to utf-8 will be required, when you'll need your DB data properly encoded
        // ex: $sqlDump = mb_convert_encoding($sqlDump, 'Windows-1252', 'utf8');
        'encoding' => 'utf8',
    ),
);

$config['global']['I18n'] = array(
    'languages' => array(
        'en' => 'English',
    ),
    'lang' => 'en',
    'domains' => array(),
    'options' => array(
        'timeZone' => 'Europe/Chisinau',
        'dateFormat' => 'DMY',
        //'timeFormat' => 24,
    ),
);

$config['global']['Message'] = array(
);

$config['global']['Mirror'] = array(
    // 'versions' => array('core' => '2.1.alpha3'),
);

$config['global']['Navigation'] = array(
    // 'languages' => array(),
    'sections' => array(
        // 'active' => null,
        // 'activeSectionName' => array( ...items... ),
    ),
    'meta' => array(),
    'default' => array(
        'home' => array(
            'title' => 'Home',
            'url' => '/',
        ),
        'admin' => array(
            'title' => 'Administration',
            'url' => '/admin',
            'target' => '_blank',
        ),
    ),
    'admin' => array(
        'home' => array(
            'title' => 'Visit site',
            'target' => '_blank',
            'url' => '/',
        ),
        'admin' => array(
            'title' => 'Dashboard',
            'url' => '/admin',
        ),
    ),
);

$config['global']['Routing'] = array(
    'home' => array(
        'controller' => 'sl',
        'home' => true,
    ),
    'prefixes' => array(
        'admin' => array(
            'admin' => true,
            'controller' => 'sl',
        ),
    ),
    'routes' => array(
    ),
);

$config['global']['Security'] = array(
    // 'remoteAddr' => '127.0.0.1' // Session-dependent
);

$config['global']['Site'] = array(
    'title' => 'StarLight',
    //'mission' => '',
    //'copyright' => '',
    'poweredBy' => 'Powered by <a href="http://starlightcms.info" target="_blank">StarLight</a>',
);

$config['global']['View'] = array(
    'options' => array(
        'safeJsCodeBlocks' => true,
        'titleSep' => ' | ',
    ),
    'phemeOptions' => array(
        'stripWhitespace' => true,
    ),

    // 'layout' => null, // based on prefix
    // 'theme' => null,

    'html' => array(
        'head' => array(),
        'footer' => array(),
    ),

    // 'bufferedOutput' => null,
    // 'lastRenderedTitle' => null,
);



///////////////////////////// CONTEXT SENSITIVE ////////////////////////////////



$config['ConfigController'] = array(
    'Navigation' => array(
        'sections' => array(
            'hint' => '<p>Most of the settings should only be configured in <em>/app/config/site.php</em> file. Use this interface only for frequently changing settings.</p>',
        ),
    ),
);

