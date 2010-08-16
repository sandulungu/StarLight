<?php

class AppModel extends Model {

    function validBool($check) {
        foreach ($check as $field => $value) {
            return preg_match('/^0|1$/', $value);
        }
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
