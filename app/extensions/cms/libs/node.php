<?php

class SlNode {

    /**
     * Node hits and other persistent statistics
     *
     * @var array
     */
    protected static $_stats = null;

    private static $__Node = null;

    public static function getModel() {
        if (self::$__Node === null) {
            self::$__Node = ClassRegistry::init('Node');
        }
        return self::$__Node;
    }

    /**
     * Get all data of a node (cached)
     *
     * @param int $id
     * @parsm bool $hit True to increment the hit counter
     * @return array
     */
    public static function getNode($id = null, $options = array()) {
        if (empty($id)) {
            $id = self::getModel()->getID();
        }

        $options += array(
            'hit' => false,
            'auth' => false,
        );

        $data = Cache::read($id, 'nodes');
        if ($data === false) {
            $data = self::getModel()->find('first', array(
                    'conditions' => array('Node.id' => $id)
                ));
            if (empty($data)) {
                Cache::write($id, null, 'nodes');
                return;
            }

            $data['path'] = self::getModel()->getPath($id, 'id', -1);
            $data['nodes'] = Set::extract('{n}.Node.id', $data['path']);
            $data['nodes'] = array_reverse($data['nodes']);

            // recursively read tags
            $data['primary_tags'] = $data['tags'] = array();
            foreach ($data['Tag'] as $tag) {
                $data['primary_tags'][] = $tag['id'];
                $data['tags'] = array_unique(am($data['tags'], Set::extract('{n}.Tag.id',
                    self::getModel()->Tag->getPath($tag['id'], 'id', -1)
                )));
            }
            unset($data['Tag']);

            // add custom node model and their associations data
            if ($data['Node']['model']) {
                $plugin = SL::r("Node.Types.{$data['Node']['model']}.plugin");
                $modelClass = $plugin ? "$plugin.{$data['Node']['model']}" : $data['Node']['model'];
                $model = ClassRegistry::init($modelClass);
                if ($model) {
                    $model->unbindModel(array('belongsTo' => array('Node')));
                    $data += $model->find('first', array(
                            'conditions' => array("{$data['Node']['model']}.node_id" => $id)
                        ));
                }
            }

            Cache::write($id, $data, 'nodes');
        } else {
            self::swapLocalizedFields($data);
        }
        if (empty($data)) {
            return;
        }

        if ($options['auth']) {
            if (!self::getModel()->check(is_bool($options['auth']) ? 'view' : $options['auth'], $data)) {
                return false;
            }
        }

        if (self::$_stats === null) {
            self::$_stats = Cache::read('node_stats', 'config');
        }
        if (!isset(self::$_stats[$id])) {
            self::$_stats[$id] = array(
                'hits' => 0,
                'votes' => 0,
                'points' => 0,
            );
        }
        if ($options['hit']) {
            self::$_stats[$id]['hits']++;
            Cache::write('node_stats', self::$_stats, 'config');
        }
        $data += self::$_stats[$id];

        return $data;
    }

    public static function getPath($id = null) {
        $node = self::getNode($id);
        if (empty($node)) {
            return false;
        }

        $path = array($node);
        while (!empty($node['Node']['parent_id'])) {
            $node = self::getNode($node['Node']['parent_id']);
            $path[] = $node;
        }
    }

    /**
     * Return a list of nodes with complete data
     *
     * @param string $what Find operation
     * @param array $options Find options
     * @return array
     */
    public static function findNodes($what, $options = array()) {
        return self::getModel()->findNodes($what, $options);
    }

    public static function url($data = null, $options = array()) {
        $options += array(
            "routed" => false,
        );
        $url = SL::nodeUrl(!is_array($data) ? self::getNode($data) : $data);
        return $options['routed'] ? SL::url($url, $options) : $url;
    }


    public static function swapLocalizedFields(&$data, $locale = null) {
        if (is_array($data)) {
            if (empty($locale)) {
                $locale = SL::r('I18n.locale');
            }
            foreach ($data as $key => &$val) {
                if (is_array($val)) {
                    self::swapLocalizedFields($val, $locale);
                }
                elseif (preg_match("/_$locale\$/", $key)) {
                    $data[preg_replace("/_$locale\$/", '', $key)] = $val;
                }
            }
        }
        return $data;
    }
}
