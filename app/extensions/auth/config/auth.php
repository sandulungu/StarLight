<?php

$config['global']['I18n'] = array(
    'domains' => array(
        'auth' => 'auth',
    )
);

$config['global']['Auth'] = array(
    'options' => array(
        'checkActionsAcl' => true,
        'remember' => '+7 days',
    ),
    'guest' => array(
        'fullname' => 'Guest',
        'roles' => array('everyone', 'guest'),
    ),
    'acl' => array(
        'everyone' => array(
            'actionIndex' => true,
            'actionView' => true,
        ),
        // 'guest' => array(),
        // 'users' => array(),
        // 'Group1' => array(),
        // 'User2' => array(),
    ),
    //'user' => array(), // Currently logged-in user
    'url' => array(
        'afterLogin' => array(
            'action' => 'index',
        ),
        'afterAdminLogin' => '/admin',
        'afterLogout' => '/',
    ),
);

// alow generic access to login/logout and user page
$config['UsersController']['Auth'] = array(
    'acl' => array(
        'everyone' => array(
            'actionLogin' => true,
            'actionAdminLogin' => true,
        ),
        'users' => array(
            'actionIndex' => true,
            'actionLogout' => true,
            'actionAdminLogout' => true,
        ),
    ),
);

// alow guest access to installation routines
$config['InstallController']['Auth'] = array(
    'acl' => array(
        'everyone' => array(
            'actionIndex' => true,
            'actionDb' => true,
            'actionDb2' => true,
            'actionUser' => true,
        ),
        'Group1' => array(
            'actionMigrate' => true,
        ),
    ),
);

// alow Collaborator to edit Pages
$config['PagesController']['Auth'] = array(
    'acl' => array(
        'Group2' => array(
            'actionAdminIndex' => true,
            'actionAdminAdd' => true,
            'actionAdminEdit' => true,
            'actionAdminDelete' => true,
        ),
    ),
);

// allow Collaborator to access Configuration UI
$config['ConfigController']['Auth'] = array(
    'acl' => array(
        'Group2' => array(
            'actionAdminIndex' => true,
            'actionAdminAdd' => true,
            'actionAdminEdit' => true,
            'actionAdminDelete' => true,

            'configSite' => true,
        ),
    ),
);

// root user
$config['Group1']['Navigation'] = array(
    'sections' => array(
        'auth' => array(
            array(
                'title' => 'Users',
                'url' => array('controller' => 'users', 'action' => 'index', 'plugin' => 'auth'),
            ),
            array(
                'title' => 'Groups',
                'url' => array('controller' => 'groups', 'action' => 'index', 'plugin' => 'auth'),
            ),
            array(
                'title' => 'ACL',
                'url' => array('controller' => 'acl_rules', 'action' => 'index', 'plugin' => 'auth'),
            ),
        ),
    ),
    'admin' => array(
        'config' => array(
            'title' => 'Configuration',
            'url' => array('controller' => 'config', 'action' => 'index', 'plugin' => false)
        ),
        'users' => array(
            'title' => 'Users & groups',
            'url' => array('controller' => 'users', 'action' => 'index', 'plugin' => 'auth'),
        ),
    ),
);

// Collaborators group
$config['Group2']['Navigation'] = array(
    'admin' => array(
        'config' => array(
            'title' => 'Configuration',
            'url' => array('controller' => 'config', 'action' => 'index', 'plugin' => false)
        ),
        'pages' => array(
            'title' => 'Pages',
            'url' => array('controller' => 'pages', 'action' => 'index', 'plugin' => false)
        ),
    ),
);

// anonymous visitors
$config['guest']['Navigation'] = array(
    'meta' => array(
        'login' => array(
            'title' => 'Login',
            'url' => array('controller' => 'users', 'action' => 'login', 'plugin' => 'auth')
        ),
    ),
);

// all logged in users
$config['users']['Navigation'] = array(
    'meta' => array(
        'logout' => array(
            'title' => 'Logout',
            'url' => array('controller' => 'users', 'action' => 'logout', 'plugin' => 'auth')
        ),
    ),
);

$config['AuthPlugin'] = array(
    'Navigation' => array(
        'sections' => array(
            'active' => 'auth',
            'hint' => '<p><b>Note 1</b>: the first user is root (has full access to everything and cannot be deleted or disabled).</p><p><b>Note 2</b>: the first 2 groups (Administrators and Collaborators) cannot be deleted and have default ACLs bindings.</p>',
        ),
    ),

    // allow Administrators access to users and groups management
    'Auth' => array(
        'acl' => array(
            'Group1' => array(
                'actionAdminIndex' => true,
                'actionAdminAdd' => true,
                'actionAdminEdit' => true,
                'actionAdminDelete' => true,
            ),
        ),
    )
);
