<?php namespace Devio\Properties;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Devio\Properties\Exceptions\ValueIsNotCollection;

class PropertySetter {

    /**
     * @var Eloquent
     */
    protected $entity;

    protected $key;

    protected $value;

    /**
     * @var \Devio\Properties\Models\Property
     */
    protected $property;

    /**
     * Gets the entity we are working with.
     *
     * @param Eloquent $entity
     */
    public function __construct(Eloquent $entity, $key, $value)
    {
        $this->entity = $entity;
        $this->key    = $key;
        $this->value  = $value;
        $this->property = $this->entity->getPropertyByName($key);
    }

    public function perform()
    {
        if ($this->property->isCollection())
        {

        }
        else
        {
            $this->performString();
        }
    }

    protected function performString()
    {
        if ( ! $this->property->isMultiple())
        {
            if ($elements = $this->entity->getValueElement($this->property->id))
            {
                $elements->first()->value = $this->value;
            }
            else
            {
                $this->entity->queueValueCreation($this->property->id, $this->value);
            }
        }
        else
        {
            if ($this->value instanceof Collection)
            {
                foreach ($this->value as $value)
                {

                }
            }
            else
            {
                throw new ValueIsNotCollection;
            }
        }
    }

}