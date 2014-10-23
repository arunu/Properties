<?php namespace Devio\Properties\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Value extends Eloquent {

    /**
     * @var array
     */
    protected $guarded = [];

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

}