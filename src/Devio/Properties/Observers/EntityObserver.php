<?php namespace Devio\Properties\Observers;

class EntityObserver {

    /**
     * Handling entity saved event. Manages to create or delete (just empty) instances
     * from the value table.
     *
     * @param $model
     */
    public function saved($model)
    {
        foreach ($model->getValueCreationQueue() as $item)
        {
            $model->values()->create([
                // Property foreign key attirbute name
                $model->getPropertyForeignKey() => $item['property'],
                'value'                         => $item['value']
            ]);
        }
    }

}