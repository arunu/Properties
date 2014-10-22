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

        static::$properties = $instance->properties()->get()->lists('name');
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
     * @param $key
     *
     * @return bool
     */
    protected function isProperty($key)
    {
        dd($key);
        return isset($this->properties[$key]);
    }

    /**
     * @param $key
     */
    public function __get($key)
    {
        if ($this->isProperty($key))
        {
            dd('is a property');
        }

//        return parent::__get($key);
    }

} 