<?php

/**
 * Plain-text email ContactForm template
 *
 * Parameters:
 *      array $fields
 */

if (false) {
    //$this = new AppView();
}

echo $cmsNode['CmsNode']['title']."\n--------------------------\n\n";
$text = 'IP: '. env('REMOTE_ADDR')."\n\n";
foreach ($fields as $name => $field) {
    $n = $field['type'] === 'textarea' ? "\n" : '';
    $t = is_array($this->data['CmsContactForm'][$name]) ?
        implode(', ', $this->data['CmsContactForm'][$name]) :
        $this->data['CmsContactForm'][$name];
    $text .= "{$field['label']}: $n$t\n\n";
}
echo $text;