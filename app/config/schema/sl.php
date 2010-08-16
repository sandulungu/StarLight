<?php

class SlSchema extends CakeSchema {

    //public $plugin = '';

	public function after($event = array()) {
        if (isset($event['create']) && $event['create'] == 'auth_groups') {
            $groupModel = ClassRegistry::init('Auth.Group');
            $groupModel->saveAll(array(
                array('Group' => array(
                    'title' => 'Administrators',
                    'title_eng' => 'Administrators',
                    'title_rum' => 'Administratori',
                    'description' => "Add/edit user accounts.\nModify security settings.",
                    'description_eng' => "Add/edit user accounts.\nModify security settings.",
                )),
                array('Group' => array(
                    'title' => 'Collaborators',
                    'title_eng' => 'Collaborators',
                    'title_rum' => 'Colaboratori',
                    'description' => "Modify site settings and content.",
                    'description_eng' => "Modify site settings and content.",
                )),
            ));
        }
	}

	/*var $auth_acl_rules = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'who' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'allow' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'what' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'collection' => array('type' => 'string', 'null' => false, 'default' => 'global', 'length' => 1024),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'group_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $auth_groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $auth_groups_users = array(
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $auth_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'password' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'fullname' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'params' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $bc_books = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'borrow_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'author' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'rating' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'isbn' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 13),
		'cover_filename' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $bc_books_tags = array(
		'book_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'tag_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $bc_books_wishlist_users = array(
		'book_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $bc_borrows = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'book_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'start_date' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'deadline' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'reader_rating' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'reader_review' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'book_rating' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'book_review' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'return_date' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'state' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $bc_tag_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $bc_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'tag_type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $bc_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'fbid' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'fullname' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'rating' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_attachments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'filename' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
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
		'placement' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'collection' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_contact_forms = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'fields' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_images = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'filename' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'href' => array('type' => 'string', 'null' => false, 'default' => NULL),
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
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $cms_nodes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'image_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'short_title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'teaser' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'body' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'meta_keywords' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'meta_description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'skin' => array('type' => 'string', 'null' => false, 'default' => 'default'),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'params' => array('type' => 'text', 'null' => true, 'default' => NULL),
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
	var $cms_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'class' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);*/
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
	/*var $mo_features = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'icon_filename' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $mo_features_flats = array(
		'feature_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'flat_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $mo_flats = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'address' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'lng' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'lat' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'currency_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'owner_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'plan_filename' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $mo_flats_visible_rooms = array(
		'flat_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'room_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $mo_offers = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'flat_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'price' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'period_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'min_period' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $mo_owners = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'phone' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 16),
		'credit' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'address' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'owner_type_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'ss_no' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 13),
		'passport_no' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 7),
		'passport_reg_date' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'misc_phones' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $mo_photos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'room_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'filename' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $mo_pois = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'address' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'lat' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'lng' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'owner_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'poi_type_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $mo_rooms = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'flat_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'plan_filename' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1024),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);*/
}
