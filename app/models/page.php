<?php

/**
 *
 * @property-read Page $Page
 */
class Page extends AppModel {
    public $useTable = 'core_pages';

    public $actsAs = array(
        'Markdown' => array('content' => 'markdown_content'),
        'Translate' => array('title', 'content', 'markdown_content'),
    );

    public $validate = array(
        'title' => array(
            'rule' => array('minLength', '1'),
        ),
        'body' => array(
            'rule' => array('minLength', '1'),
        ),
    );

}
