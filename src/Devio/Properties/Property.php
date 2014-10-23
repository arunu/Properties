<?php namespace Devio\Properties;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function value()
    {
        return $this->hasMany('Devio\Properties\Value');
    }

} 