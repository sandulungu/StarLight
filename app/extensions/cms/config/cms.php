<?php

$config['global']['Cms'] = array(
    'nodeSkins' => array(
        'default' => 'Static page (default)',
    ),
    'nodeTypes' => array(
        'default' => array('name' => 'Page/article (default)'),
        'CmsContactForm' => array('plugin' => 'cms', 'name' => 'Contact Form'),
    ),
);



/////////////////////////////// CORE EXTENSIONS ////////////////////////////////



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
                'url' => array('admin' => true, 'controller' => 'cms_nodes', 'action' => 'index', 'plugin' => 'cms'),
                'children' => array(
                    array(
                        'title' => 'Contact Forms',
                        'url' => array('admin' => true, 'controller' => 'cms_contact_forms', 'action' => 'index', 'plugin' => 'cms'),
                    ),
                ),
            ),
            array(
                'title' => 'Content meta',
                'children' => array(
                    array(
                        'title' => 'Tags',
                        'url' => array('admin' => true, 'controller' => 'cms_tags', 'action' => 'index', 'plugin' => 'cms'),
                    ),
                    array(
                        'title' => 'Images',
                        'url' => array('admin' => true, 'controller' => 'cms_images', 'action' => 'index', 'plugin' => 'cms'),
                    ),
                    array(
                        'title' => 'Attachments',
                        'url' => array('admin' => true, 'controller' => 'cms_attachments', 'action' => 'index', 'plugin' => 'cms'),
                    ),
                ),
            ),
            array(
                'title' => 'Blocks',
                'url' => array('admin' => true, 'controller' => 'cms_blocks', 'action' => 'index', 'plugin' => 'cms'),
            ),
            array(
                'title' => 'Navigation',
                'url' => array('admin' => true, 'controller' => 'cms_navigation_links', 'action' => 'index', 'plugin' => 'cms'),
            ),
            array(
                'title' => 'Configuration',
                'url' => array('admin' => true, 'controller' => 'config', 'cms', 'plugin' => false),
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
    'routes' => array(
        '/node/*' => array(
            'plugin' => 'cms',
            'controller' => 'cms_nodes',
            'action' => 'view',
        ),
        '/contact_form/*' => array(
            'plugin' => 'cms',
            'controller' => 'cms_contact_forms',
            'action' => 'view',
        )
    )
);



///////////////////////////// CONTEXT SENSITIVE ////////////////////////////////



// allow everyone access to admin home
$config['CmsCmsController']['Auth'] = array(
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
                'actionAdminView' => true,
                'actionAdminEdit' => true,
                'actionAdminDelete' => true,
            ),
        ),
    ),
);

$config['CmsNodesController']['Cms'] = array(
    'nodeSkins' => array(
        'article' => 'Article (author name and publish date are visible)',
    ),
);

