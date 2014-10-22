<?php namespace Devio\Properties;

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
     * Booting the trait. Filling the properties array with the available properties
     * to the current entity.
     */
    public static function bootPropertyTrait()
    {
        $instance = new static;

        static::$properties = $instance->properties()->get()->lists('name', 'id');
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
        return $this->morphMany('Devio\Properties\Value', $this->propertyForeignKey);
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
     * Finds the property value into the relations collections.
     *
     * @param $key
     *
     * @return mixed
     */
    public function getProperty($key)
    {
        $foreignKey = $this->findPropertyKey($key);

        // Finds into the eloquent collection results the collection that belongs
        // to the property_id
        $propertyCollection = $this->values->find($foreignKey, 'property_id');

        // If is a string means that no value record was found, return just null.
        if (is_string($propertyCollection))
            return null;

        return $propertyCollection->value;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->isProperty($key))
        {
            return $this->getProperty($key);
        }

        return parent::__get($key);
    }

} 