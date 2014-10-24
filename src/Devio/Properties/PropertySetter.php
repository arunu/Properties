<?php namespace Devio\Properties;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Devio\Properties\Exceptions\ObjectValueNotAllowed;
use Devio\Properties\Exceptions\CollectionValueRequired;

class PropertySetter {

    /**
     * @var Eloquent
     */
    protected $entity;

    /**
     * @var
     */
    protected $key;

    /**
     * @var
     */
    protected $value;

    /**
     * @var \Devio\Properties\Models\Property
     */
    protected $property;

    /**
     * Gets the entity we are working with.
     *
     * @param Eloquent $entity
     * @param          $key
     * @param          $value
     */
    public function __construct(Eloquent $entity, $key, $value)
    {
        $this->entity   = $entity;
        $this->key      = $key;
        $this->value    = $value;
        $this->property = $this->entity->getPropertyByName($key);
    }

    /**
     * Perform the new set.
     */
    public function perform()
    {
        if ($this->property->isMultiple())
            return $this->performMultiple();

        return $this->performSimple();
    }

    /**
     * Performs a simple assignation or creation. Value must be a simple element
     * not a collection nor an object or an array.
     *
     * @throws CollectionValueRequired
     * @throws ObjectValueNotAllowed
     */
    protected function performSimple()
    {
        $this->checkBeforePerform('simple');

        if ($elements = $this->entity->getValueElement($this->property->id))
        {
            $elements->first()->value = $this->value;

            return;
        }

        $this->queueValue($this->value);
    }

    /**
     * Performing multiple elements. Iterates the existing, if exists, update its
     * value, otherwise add a new one to the creation queue.
     *
     * @throws CollectionValueRequired
     * @throws ObjectValueNotAllowed
     */
    protected function performMultiple()
    {
        $this->checkBeforePerform('multiple');

        // It'll allow a collection or an array of elements
        if (is_array($this->value))
            $this->value = Collection::make($this->value);

        foreach ($this->value as $value)
        {
            if ($value->id)
                $this->entity->values->find($value->id)->value = $value->value;
            else
                $this->queueValue($value->value);
        }
    }

    /**
     * Adds a new element to be created in the queue.
     *
     * @param $value
     */
    protected function queueValue($value)
    {
        $this->entity->queueValueCreation($this->property->id, $value);
    }

    /**
     * Check if the action can be performed based on the input type.
     *
     * @param $performType
     *
     * @throws CollectionValueRequired
     * @throws ObjectValueNotAllowed
     */
    protected function checkBeforePerform($performType)
    {
        if ($performType == 'simple' && (is_object($this->value)))
            throw new ObjectValueNotAllowed;

        if ($performType == 'multiple' && ! $this->value instanceof Collection && ! is_array($this->value))
            throw new CollectionValueRequired;
    }
}