<?php

$config['global']['Api']['facebook'] = array(
    // 'appId' => null,
    // 'applicationSecret' => null,
    // 'canvasPageName' => 'sample_app', // as http://apps.facebook.com/$appName/
    // 'appName' => 'Sample application'
    'from' => 'StarLight',
    'oauthSuccess' => array('action' => 'index'),
);

$config['global']['Api']['hqSms'] = array(
    'secure' => true,
    'username' => 'USERNAME',
    'password' => md5('PASSWORD'),
    //'from' => 'StarLight',
);

$config['global']['Api']['swiftMailer'] = array(
    'method' => 'native', // 'smtp'
    
    //'smtpType' => 'tls',
    //'smtpHost' => 'smtp.gmail.com',
    //'smtpPort' => 465,
    //'smtpUsername' => 'account@yourdomain.com',
    //'smtpPassword' => 'yourpassword',

    'sendAs' => 'both',
    'from' => 'noreply@starlightcms.info',
    'fromName' => 'StarLight Mail System',
    'subject' => 'StarLight Notification',
);
