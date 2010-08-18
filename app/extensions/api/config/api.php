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
    'username' => '',
    'password' => '',
    'from' => 'StarLight',
);

$config['global']['Api']['swiftMailer'] = array(
    'method' => 'smtp',
    
    'smtpType' => 'tls',
    'smtpHost' => 'smtp.gmail.com',
    'smtpPort' => 465,
    'smtpUsername' => 'noreply@starlightcms.info',
    'smtpPassword' => 'jg5HBfd83h',

    'sendAs' => 'both',
    'from' => 'noreply@starlightcms.info',
    'fromName' => 'StarLight Mail System',
    'subject' => 'StarLight Notification',
);
