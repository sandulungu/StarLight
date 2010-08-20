<?php

class SlNode {

    public static function url($id, $options = array()) {
        $options += array(
            'base' => true,
            'full' => false,
            'route' => true,
            'slug' => true,
        );

        $full = $options['full'];
        unset($options['full']);

        $slug = $options['slug'];
        unset($options['slug']);

        $route = $options['route'];
        unset($options['route']);

        if (is_int($id)) {
            $node = self::read($id);
        } else {
            $node = $id;
            $id = $node['CmsNode']['id'];
        }

        if ($node['CmsNode']['model']) {
            $options += array(
                'plugin' => $node['CmsNode']['plugin'],
                'controller' => Inflector::tableize($node['CmsNode']['model']),
            );
        } else {
            $options += array(
                'plugin' => 'cms',
                'controller' => 'cms_nodes',
            );
        }
        $options += array(
            'admin' => false,
            'action' => 'view',
            $id,
        );

        if ($slug && $node['CmsNode']['slug']) {
            $options[] = $node['CmsNode']['slug'];
        }

        return $route ? Sl::url($options, $full) : $options;
    }

    private static $__Node = null;

    /**
     *
     * @return CmsNode
     */
    public static function getModel() {
        if (self::$__Node === null) {
            self::$__Node = ClassRegistry::init('Cms.CmsNode');
        }
        return self::$__Node;
    }

    public static function getTagList() {
        return SlNode::getModel()->CmsTag->find('list', array(
            'fields' => array('CmsTag.id', 'CmsTag.name', 'CmsTagType.name'),
            'recursive' => 0,
        ));
    }

    /**
     * CmsNode hits and other persistent statistics
     *
     * @var array
     */
    protected static $_stats = null;

    /**
     * Alias for SlNode::read(...)
     */
    public static function get($id = null, $options = array()) {
        return self::read($id, $options);
    }

    /**
     * Get all data of a node (cached)
     *
     * @param int $id
     * @parsm bool $hit True to increment the hit counter
     * @return array
     */
    public static function read($id, $options = array()) {
        if (empty($id)) {
            $id = self::getModel()->getID();
        }

        $options += array(
            'hit' => false,
            'auth' => false,
        );

        $key = "node_{$id}_". SlConfigure::read('I18n.locale');
        $data = Cache::read($key, 'models');
        if (empty($data)) {
            $data = self::getModel()->readCached($id);
        }

        if ($options['hit']) {
            //...
        }
        
        if ($options['auth']) {
            //...
        }

        return $data;
    }

    public static function getPath($id = null) {
        $node = self::read($id);
        if (empty($node)) {
            return array();
        }

        $path = array($node);
        while (!empty($node['CmsNode']['parent_id'])) {
            $node = self::read($node['CmsNode']['parent_id']);
            array_unshift($path, $node);
        }
        return $path;
    }

}
