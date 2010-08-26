<?php

class IpAddressBehavior extends ModelBehavior {

    /**
     *
     * @param AppModel $model
     */
    public function beforeSave($model) {
        if (!isset($model->data[$model->alias]['ip_address'])) {
            $model->data[$model->alias]['ip_address'] = env('REMOTE_ADDR');
        }
    }

}
