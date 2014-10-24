<?php namespace Devio\Properties\Models;

use Devio\Properties\Exceptions\WrongPropertyTypeDeclaration;
use Devio\Properties\Observers\PropertyObserver;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Property extends Eloquent {

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Booting the model. Just registering the observer instance.
     */
    public static function boot()
    {
        parent::boot();

        static::observe(new PropertyObserver);
    }

    /**
     * Regular relationship to all the property values.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany('Devio\Properties\Models\Value');
    }

    /**
     * Parent category relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('Devio\Properties\Models\PropertyCategory');
    }

    /**
     * Relationship to the collections table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function collection()
    {
        return $this->hasMany('Devio\Properties\Models\PropertyCollection');
    }

    /**
     * If the property type is a collection will return true.
     *
     * @return bool
     */
    public function isCollection()
    {
        return (strpos($this->type, 'collection') !== false);
    }

    /**
     * Returns if the property manages multiple values.
     *
     * @return mixed
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * Finds the collection to work with and returns it. The collection might be
     * another eloquent class or a set of recrods saved into the property_collections
     * table. If nothing is found just throws an exception.
     *
     * @return mixed
     * @throws WrongPropertyTypeDeclaration
     */
    public function getCollection()
    {
        if ($this->type === 'collection')
        {
            return $this->collection;
        }
        else
        {
            $class = $this->getCollectionClass();

            // If no collection class is found may mean that the property type
            // string is not well formatted, just notifies this fact.
            if (is_null($class))
                throw new WrongPropertyTypeDeclaration;

            return forward_static_call([$class, 'all']);
        }
    }

    /**
     * Returns the collection associated class:
     * collection(City) -> City
     * collection(Language) -> Language
     *
     * @return mixed
     */
    public function getCollectionClass()
    {
        preg_match('/collection\((\w+)\)/', $this->type, $result);

        if (is_array($result))
            return $result[1];

        return null;
    }

} 