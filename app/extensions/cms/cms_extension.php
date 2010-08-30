<?php

// TODO Upgrade CmsExtension's controllers and views to use the latest magical CRUD routines

App::import('lib', 'cms.sl_node');

class CmsExtension extends SlExtension {
    public $requires = array('Auth');

}
