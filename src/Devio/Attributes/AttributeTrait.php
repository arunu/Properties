<?php namespace Devio\Attributes;

use Devio\Attributes\Relations\AttributeHasMany;

trait AttributeTrait {

    /**
     * Attribute custom foreign key name used for relating the attributes
     * table to any element and also used as base for the polymorphic
     * relationship fields to the values table key_type and key_id
     *
     * @var string
     */
    private $attributeForeignKey = 'entity';

    /**
     * Relationship to the attributes table using the custom AttributeHasMany
     * relation.
     *
     * @return AttributeHasMany
     */
    public function properties()
    {
        $instance = new Attribute();

        return new AttributeHasMany($instance->newQuery(), $this, $this->getMorphClass());
    }

    /**
     * Polymorphic relationship to the values table.
     *
     * @return mixed
     */
    public function values()
    {
        return $this->morphMany('Devio\Attributes\Value', $this->attributeForeignKey);
    }

    /**
     * @param $key
     */
    public function __get($key)
    {
        dd($this->attributes);
    }

} 