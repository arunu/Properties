<?php namespace Devio\Properties;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Property extends Eloquent {

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function value()
    {
        return $this->hasMany('Devio\Properties\Value');
    }

} 