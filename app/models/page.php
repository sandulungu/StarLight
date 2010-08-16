<?php

/**
 *
 * @property-read Page $Page
 */
class Page extends AppModel {
    public $useTable = 'core_pages';

    public $actsAs = array(
        'Translate' => array('title', 'content'),
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
