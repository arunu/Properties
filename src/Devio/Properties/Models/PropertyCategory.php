<?php namespace Devio\Properties\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PropertyCategory extends Eloquent {

    protected $fillable = ['name'];

    /**
     * Relationship to property table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany('Devio\Properties\Models\Property');
    }
} 