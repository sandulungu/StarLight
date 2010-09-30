<?php
    /**
     * Default HTML email template
     */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $title_for_layout; ?></title>
    <style type="text/css">
        body {
            /* email body styles */
            text-align: center;
            background: #fff;
            color: #000;
            font-family: Verdana, Tahoma, sans-serif;
            font-size: 85%;
            line-height: 1.4em;
        }
        h1 {
            font-size: 120%;
        }
        .blockquote {
            background: #eee;
        }
        #wrapper {
            /* email content wrapper styles */
            text-align: left;
            margin: 0 auto;
            padding: 1em;
        }
        #notice {
            /* bottom notice */
            text-align: left;
            margin-top: 1em;
            border-top: 1px solid #000;
            padding-top: .5em;
            font-size: 80%;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <?php echo $content_for_layout; ?>
    </div>
    <div id="notice">
        <?php echo h(SlConfigure::read('Site.copyright')); ?>
    </div>
</body>
</html>
