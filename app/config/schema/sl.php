<?php

class SlSchema extends CakeSchema {

    //public $plugin = '';

	public function after($event = array()) {
        if (isset($event['create']) && $event['create'] == 'auth_groups') {
            $groupModel = ClassRegistry::init('Auth.Group');
            $groupModel->saveAll(array(
                array('Group' => array(
                    'title' => 'Administrators',
                    'description' => "Add/edit user accounts.\nModify security settings.",
                    'description_eng' => "Add/edit user accounts.\nModify security settings.",
                )),
                array('Group' => array(
                    'title' => 'Collaborators',
                    'description' => "Modify site settings and content.",
                    'description_eng' => "Modify site settings and content.",
                )),
            ));
        }
	}

	var $core_pages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'content' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $core_versions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'version' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $cms_blocks = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'body' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'cache_time' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'placement' => array('type' => 'string', 'null' => false, 'default' => 'Left'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'collection' => array('type' => 'string', 'null' => false, 'default' => 'global'),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'weight' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_contact_forms = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'fields' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_images = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'filename' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'href' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'weight' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_navigation_links = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'node_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'class' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'target' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'rel' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'hint' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'collection' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_nodes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'image_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'short_title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'teaser' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'body' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'meta_keywords' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'meta_description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'skin' => array('type' => 'string', 'null' => false, 'default' => 'default'),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'params' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_nodes_tags = array(
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'tag_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_tag_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'tag_type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
}
