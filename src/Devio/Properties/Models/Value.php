<?php namespace Devio\Properties\Models;

use Devio\Properties\Contracts\Valuable;
use Devio\Properties\Observers\ValueObserver;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Value extends Eloquent implements Valuable {

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string
     */
    public static $valueField = 'value';

    /**
     * Booting the model. Just registering the observer instance.
     */
    public static function boot()
    {
        parent::boot();

        static::observe(new ValueObserver);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo('Devio\Properties\Models\Property');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * Should return the model value field
     *
     * @return mixed
     */
    public function getValueField()
    {
        return $this->{static::$valueField};
    }

    /**
     * @return string
     */
    public function getValueFieldName()
    {
        return static::$valueField;
    }
}