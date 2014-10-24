<?php namespace Devio\Properties\Models;

use Devio\Properties\Contracts\Valuable;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Value extends Eloquent implements Valuable {

    /**
     * @var array
     */
    protected $guarded = [];

    public static $valueField = 'value';

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