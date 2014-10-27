<?php namespace Devio\Properties;

use Devio\Properties\Models\Property;
use Devio\Properties\Observers\EntityObserver;
use Devio\Properties\Relations\PropertyHasMany;
use Devio\Properties\Exceptions\ValueIsNotInteger;

trait PropertyTrait {

    /**
     * Attribute custom foreign key name used for relating the attributes
     * table to any element and also used as base for the polymorphic
     * relationship fields to the values table key_type and key_id
     *
     * @var string
     */
    protected $propertyForeignKey = 'entity';

    /**
     * Contains an array of registered properties to the current entity.
     * This can be iterated to determine if an attribute belongs to
     * the properties table.
     *
     * @var array
     */
    public static $properties = [];

    /**
     * List of values that have to be created when the entity model is saved.
     *
     * @var array
     */
    protected $valueCreationQueue = [];

    /**
     * Booting the trait. Filling the properties array with the available properties
     * to the current entity.
     */
    public static function bootPropertyTrait()
    {
        $instance           = new static;
        static::$properties = $instance->properties()->get();

        // Registering entity observer
        static::observe(new EntityObserver);
    }

    /**
     * Relationship to the attributes table using the custom AttributeHasMany
     * relation.
     *
     * @return AttributeHasMany
     */
    public function properties()
    {
        $instance = new Property;

        return new PropertyHasMany($instance->newQuery(), $this, $this->getMorphClass());
    }

    /**
     * Polymorphic relationship to the values table.
     *
     * @return mixed
     */
    public function values()
    {
        return $this->morphMany('Devio\Properties\Models\Value', $this->propertyForeignKey);
    }

    /**
     * Will check if a property exists in the current entity.
     *
     * @param $key
     *
     * @return bool
     */
    protected function isProperty($key)
    {
        return in_array($key, array_column(static::$properties->toArray(), 'name'));
    }

    /**
     * Return the property object that matches the id.
     *
     * @param $id
     */
    public function getProperty($id)
    {
        return static::$properties->find($id);
    }

    /**
     * Return the property from its name
     *
     * @param $name
     *
     * @return mixed
     */
    public function getPropertyByName($name)
    {
        return $this->getProperty($this->findPropertyKey($name));
    }

    /**
     * Retrieve the property object of the elements collection
     *
     * @param $elements
     *
     * @return mixed
     */
    public function getPropertyByElements($elements)
    {
        return $this->getProperty($elements->first()->{$this->getPropertyForeignKey()});
    }

    /**
     * Get the property primary key by property name
     *
     * @param $name
     *
     * @return mixed
     */
    public function findPropertyKey($name)
    {
        return array_search($name, static::$properties->lists('name', 'id'));
    }

    /**
     * Finds into the eloquent collection results the collection that belongs
     * to the foreign key
     *
     * @param $foreignKey
     *
     * @return mixed
     */
    public function getValueElement($foreignKey)
    {
        $values = $this->values->filter(function ($item) use ($foreignKey)
        {
            if ($item->{$this->getPropertyForeignKey()} == $foreignKey)
            {
                return $item;
            }
        });

        if ($values->count())
            return $values;

        return null;
    }

    /**
     * Returns the property value from the value relation. If the property
     * to be accessed is a collection, the readable value for it will
     * be provided.
     *
     * @param $key
     * @param $valueObject
     *
     * @return mixed
     */
    public function getPropertyValue($key, $valueObject = false)
    {
        $foreignKey = $this->findPropertyKey($key);

        // If the property is found into the values collection return its value.
        // If not is found just return the getPropertyElement result which
        // is null.
        if ($elements = $this->getValueElement($foreignKey))
        {
            $property = $this->getPropertyByElements($elements);

            // Instantiating the formatter which will be responsible of determine
            // which type of output the element requires, we will just return
            // its formatted content
            $formatter = new OutputFormatter($elements, $property, $valueObject);

            return $formatter->format();
        }

        return null;
    }

    /**
     * Sets a property value. Checks if the value already exists or a new one
     * needs to be created.
     *
     * @param $key
     * @param $value
     *
     * @throws ValueIsNotInteger
     */
    public function setPropertyValue($key, $value)
    {
        // Create a new instance of the property setter setting the current
        // assignation values and perform it
        $setter = new PropertySetter($this, $key, $value);

        $setter->perform();
    }

    /**
     * Adds a new item to the value creation queue. By default will override
     * any existing value as it's supposed that a same property might be
     * set more than once for the same item before saving. If override
     * is set to false, a new value record will be saved. This last
     * only when working with properties with multiple values.
     *
     * @param      $foreignKey
     * @param      $value
     * @param bool $override
     */
    public function queueValueCreation($foreignKey, $value, $override = true)
    {
        // Stores if the element already exists in the queue. If does and
        // $override is true, just filter it to delete this element
        // which will be added again containing the new value

        if ($override && $this->existsValueInQueue($foreignKey))
        {
            // Filter the queue taking out only the element that is supposed
            // to be replaced.
            $this->valueCreationQueue = array_filter($this->valueCreationQueue, function($item) use ($foreignKey)
            {
                if ($item['property'] != $foreignKey)
                    return $item;
            });
        }

        $this->valueCreationQueue[] = [
            'property' => $foreignKey,
            'value'    => $value
        ];
    }

    /**
     * Returns true if the value already exists in the queue.
     *
     * @param $foreignKey
     *
     * @return bool
     */
    protected function existsValueInQueue($foreignKey)
    {
        return in_array($foreignKey, array_column($this->valueCreationQueue, 'property'));
    }

    /**
     * Provides access to the property value creation queue.
     *
     * @return array
     */
    public function getValueCreationQueue()
    {
        return $this->valueCreationQueue;
    }

    /**
     * Returns the property table foreign key.
     *
     * @return string
     */
    public function getPropertyForeignKey()
    {
        $instance = new Property;

        return $instance->getForeignKey();
    }

    /**
     * Rewritten parent toArray to include the properties in the same
     * array
     *
     * @return mixed
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        $properties = [];

        foreach ($this->properties as $property)
        {
            $element = $property->toArray();

            $element['value'] = $this->getPropertyValue($property->name, true);
            $element['formatted_value'] = $this->getPropertyValue($property->name);

            $properties[$property->category->name][] = $element;
        }

        $attributes['entity'] = $this->getMorphClass();
        $attributes['properties'] = $properties;

        return $attributes;
    }

    /**
     * Provides the property value if it does exist. Custom parent
     * __get function will be called if not.
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->isProperty($key))
        {
            return $this->getPropertyValue($key);
        }

        return parent::__get($key);
    }

    /**
     * If the key matches any property, overrides default behaviour and
     * modifies the property instead.
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        if ($this->isProperty($key))
        {
            $this->setPropertyValue($key, $value);
        }
        else
        {
            parent::__set($key, $value);
        }
    }

    /**
     * Checking if a property is set using the __isset magic method. If not found
     * just returns its default behaviour.
     *
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return ($this->isProperty($key) && $this->getPropertyValue($key)) ? true : parent::__isset($key);
    }
}