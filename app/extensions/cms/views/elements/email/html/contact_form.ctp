<?php

/**
 * Html email ContactForm template
 *
 * Parameters:
 *      array $fields
 */

if (false) {
    //$this = new AppView();
}

echo $this->SlHtml->h3($cmsNode['CmsNode']['title']);
echo '<ul>';
$text = '<li><em>IP</em>: '. env('REMOTE_ADDR')."</li>";
foreach ($fields as $name => $field) {
    $n = $field['type'] === 'textarea' ? "<div class='blockquote'>" : '';
    $n2 = $field['type'] === 'textarea' ? "</div>" : '';
    $t = is_array($this->data['CmsContactForm'][$name]) ?
        implode(', ', h($this->data['CmsContactForm'][$name])) :
        h($this->data['CmsContactForm'][$name]);
    $text .= "<li><em>{$field['label']}</em>: $n$t$n2</li>";
}
echo nl2br($text);
echo '</ul>';