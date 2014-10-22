<?php namespace Devio\Attributes; 

use Illuminate\Database\Eloquent\Model as Eloquent;

class Value extends Eloquent {

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute()
    {
        return $this->belongsTo('Devio\Attributes\Attribute');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo();
    }

} 