<?php namespace Devio\Attributes;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Attribute extends Eloquent {

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function value()
    {
        return $this->hasMany('Devio\Attributes\Value');
    }

} 