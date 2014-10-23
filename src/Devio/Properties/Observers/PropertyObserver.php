<?php namespace Devio\Properties\Observers;

use Devio\Properties\Value;

class PropertyObserver {

    /**
     * Forcing cascade deleting if enabled.
     *
     * @param $model
     */
    public function deleted($model)
    {
        if (Config::get('properties::config.delete_cascade'))
        {
            Value::destroy($model->id);
        }
    }

}