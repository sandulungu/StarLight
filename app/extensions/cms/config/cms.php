<?php

$config['global']['Auth'] = array(
    'acl' => array(
        'Group2' => array(
            'configCms' => true,
        ),
    ),
);
$config['global']['Config'] = array(
    'sections' => array(
        'cms' => 'Content options',
    ),
    'settings' => array(
        'cms' => array(
        ),
    ),
);

$config['global']['I18n'] = array(
    'domains' => array(
        'cms' => 'cms',
    )
);

$config['global']['Navigation'] = array(
    'sections' => array(
        'cms' => array(
            array(
                'title' => 'Content nodes',
                'url' => array('controller' => 'nodes', 'action' => 'index', 'plugin' => 'cms'),
                'children' => array(
                    array(
                        'title' => 'Email contact forms',
                        'url' => array('controller' => 'contact_forms', 'action' => 'index', 'plugin' => 'cms'),
                    ),
                ),
            ),
            array(
                'title' => 'Content meta',
                'children' => array(
                    array(
                        'title' => 'Tags',
                        'url' => array('controller' => 'tags', 'action' => 'index', 'plugin' => 'cms'),
                    ),
                    array(
                        'title' => 'Images',
                        'url' => array('controller' => 'images', 'action' => 'index', 'plugin' => 'cms'),
                    ),
                    array(
                        'title' => 'Attachment',
                        'url' => array('controller' => 'attachment', 'action' => 'index', 'plugin' => 'cms'),
                    ),
                ),
            ),
            array(
                'title' => 'Blocks',
                'url' => array('controller' => 'blocks', 'action' => 'index', 'plugin' => 'cms'),
            ),
            array(
                'title' => 'Navigation',
                'url' => array('controller' => 'navigation_links', 'action' => 'index', 'plugin' => 'cms'),
            ),
            array(
                'title' => 'Configuration',
                'url' => array('controller' => 'config', 'cms', 'plugin' => false),
            ),
        ),
    ),
    // 'cms' => array(),
);

$config['global']['Routing'] = array(
    'home' => array(
        'plugin' => 'cms',
        'controller' => 'cms',
        'home' => true,
    ),
    'prefixes' => array(
        'admin' => array(
            'admin' => true,
            'plugin' => 'cms',
            'controller' => 'cms',
        ),
    ),
);



///////////////////////////// CONTEXT SENSITIVE ////////////////////////////////



// allow everyone access to admin home
$config['CmsController']['Auth'] = array(
    'acl' => array(
        'everyone' => array(
            'actionAdminIndex' => true,
        ),
    ),
);

$config['CmsPlugin'] = array(
    'Navigation' => array(
        'sections' => array(
            'active' => 'cms',
            'hint' => '<p>Site content is organized in Nodes that may have several Images, Attachments and Tags.</p>',
        ),
    ),

    // allow Colaborators CRUD access
    'Auth' => array(
        'acl' => array(
            'Group2' => array(
                'actionAdminIndex' => true,
                'actionAdminAdd' => true,
                'actionAdminEdit' => true,
                'actionAdminDelete' => true,
            ),
        ),
    ),
);
