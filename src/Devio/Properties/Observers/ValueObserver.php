<?php namespace Devio\Properties\Observers; 

class ValueObserver {

    /**
     * Captures value saved method. If the value is null
     * just delete it from database.
     *
     * @param $model
     */
    public function saved($model)
    {
        if ( ! $model->value)
            $model->delete();
    }

} 