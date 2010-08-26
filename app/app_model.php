<?php

class AppModel extends Model {

    function dummy($check) {
        foreach ($check as $field => $value) {
            return true;
        }
    }

//    public function beforeFind($model, $query) {
//        return true;
//    }
    
    public function afterFind($results, $primary = false) {
		if (!$primary || empty($results)) {
			return $results;
        }

        // Virtual field 'level'
        if ($this->Behaviors->enabled('Tree')) {
            $stack = array();

            foreach ($results as &$result) {
                if (!isset($result[$this->alias]['rght'])) {
                    return $results;
                }

                while ($stack && ($stack[count($stack) - 1] < $result[$this->alias]['rght'])) {
                    array_pop($stack);
                }
                $result[$this->alias]['level'] = count($stack);
                $stack[] = $result[$this->alias]['rght'];
            }
        }

        return $results;
    }
    
    /**
     * Returns a result set array.
     *
     * Also used to perform new-notation finds, where the first argument is type of find operation to perform
     * (all / first / count / neighbors / list / threaded / treelist ),
     * second parameter options for finding ( indexed array, including: 'conditions', 'limit',
     * 'recursive', 'page', 'fields', 'offset', 'order')
     *
     * Eg:
     * {{{
     *	find('all', array(
     *		'conditions' => array('name' => 'Thomas Anderson'),
     * 		'fields' => array('name', 'email'),
     * 		'order' => 'field3 DESC',
     * 		'recursive' => 2,
     * 		'group' => 'type'
     * ));
     * }}}
     *
     * Specifying 'fields' for new-notation 'list':
     *
     *  - If no fields are specified, then 'id' is used for key and 'model->displayField' is used for value.
     *  - If a single field is specified, 'id' is used for key and specified field is used for value.
     *  - If three fields are specified, they are used (in order) for key, value and group.
     *  - Otherwise, first and second fields are used for key and value.
     */
    function find($what, $options = array()) {
        if (strtolower($what) == 'treelist') {
            if ($this->Behaviors->enabled('Tree')) {
                $options += array(
                    'conditions' => null,
                    'keyPath' => null,
                    'valuePath' => null,
                    'spacer' => '- ',
                    'recursive' => null,
                );
                extract($options);
                return $this->generatetreelist($conditions, $keyPath, $valuePath, $spacer, $recursive);
            }
            return;
        }

        $args = func_get_args();
        return call_user_func_array(array("Model", "find"), $args);
    }



    //////////////////////////////// HACKS /////////////////////////////////////

    // if someday this lazy loader stops working, there is an alternative
    // at http://github.com/Phally/lazy_model

    

    protected $_lazyLoaderBusy = false;

    protected $_lazyLoadedModels = array();

    /**
     * Private helper method to create associated models of a given class.
     *
     * @param string $assoc Association name
     * @param string $className Class name
     * @deprecated $this->$className use $this->$assoc instead. $assoc is the 'key' in the associations array;
     * 	examples: var $hasMany = array('Assoc' => array('className' => 'ModelName'));
     * 					usage: $this->Assoc->modelMethods();
     *
     * 				var $hasMany = array('ModelName');
     * 					usage: $this->ModelName->modelMethods();
     * @return void
     * @access private
     */
	function __constructLinkedModel($assoc, $className = null) {
        if (!SlConfigure::read('Sl.options.lazyLoadModels')) {
            return parent::__constructLinkedModel($assoc, $className);
        }

		if (empty($className)) {
			$className = $assoc;
		}

        $this->_lazyLoaderBusy = true;
		if (!isset($this->{$assoc}) || $this->{$assoc}->name !== $className) {
			$model = array('class' => $className, 'alias' => $assoc);
			$this->_lazyLoadedModels[$assoc] = $model; // <-- the magic starts here
		}
        $this->_lazyLoaderBusy = false;
	}

    public function __isset($assoc) {
        if ($this->_lazyLoaderBusy) {
            return false;
        }
        return $this->__get($assoc) !== null;
    }

    /**
     * Lazy loading for models magic
     *
     * @param string $assoc
     * @return AppModel
     */
    public function  __get($assoc) {
        if (empty($this->_lazyLoadedModels[$assoc])) {
            return;
        }
        $model = $this->_lazyLoadedModels[$assoc];
        $className = $model['class'];

        $this->{$assoc} = ClassRegistry::init($model);
        if (strpos($className, '.') !== false) {
            ClassRegistry::addObject($className, $this->{$assoc});
        }
        if ($assoc) {
            $this->tableToModel[$this->{$assoc}->table] = $assoc;
        }
        return $this->{$assoc};
    }

}
