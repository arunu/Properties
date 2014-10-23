<?php namespace Devio\Properties\Models;

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
    public function value()
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

} 