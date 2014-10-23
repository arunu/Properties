<?php namespace Devio\Properties;

use Devio\Properties\Observers\EntityObserver;
use Devio\Properties\Relations\PropertyHasMany;

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
        $instance = new static;

        static::$properties = $instance->properties()->get()->lists('name', 'id');

        // Registering value observer
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
        return in_array($key, static::$properties);
    }

    /**
     * Get the property primary key by property name
     *
     * @param $name
     *
     * @return mixed
     */
    protected function findPropertyKey($name)
    {
        return array_search($name, static::$properties);
    }

    /**
     * Finds into the eloquent collection results the collection that belongs
     * to the foreign key
     *
     * @param $foreignKey
     *
     * @return mixed
     */
    protected function getValueElement($foreignKey)
    {
        foreach ($this->values as $value)
        {
            if ($value->{$this->getPropertyForeignKey()} == $foreignKey)
                return $value;
        }

        return null;
    }

    /**
     * Returns the property value into the relations collections.
     *
     * @param $key
     *
     * @return mixed
     */
    public function getPropertyValue($key)
    {
        $foreignKey = $this->findPropertyKey($key);

        // If the property is found into the values collection return its value.
        // If not is found just return the getPropertyElement result which
        // is null.
        if ($element = $this->getValueElement($foreignKey))
            return $element->value;

        return $element;
    }

    /**
     * Sets a property value. Checks if the value already exists or a new one
     * needs to be created.
     *
     * @param $key
     * @param $value
     */
    public function setPropertyValue($key, $value)
    {
        $foreignKey = $this->findPropertyKey($key);

        $element = $this->getValueElement($foreignKey);

        // Is any value element was found, update the value and that's all we need.
        // If no element is found means it doesn't even exist into the database,
        // just add a new item to the value creation queue for later creation.
        if ($element)
        {
            $element->value = $value;
        }
        else
        {
            $this->queueValueCreation($foreignKey, $value);
        }
    }

    /**
     * Adds a new item to the property creation queue.
     *
     * @param $foreignKey
     * @param $value
     */
    protected function queueValueCreation($foreignKey, $value)
    {
        $this->valueCreationQueue[] = [
            'property' => $foreignKey,
            'value' => $value
        ];
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