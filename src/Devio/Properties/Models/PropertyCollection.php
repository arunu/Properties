<?php namespace Devio\Properties\Models; 

use Devio\Properties\Contracts\Collectionable;
use Illuminate\Database\Eloquent\Model as Eloquent;

class PropertyCollection extends Eloquent implements Collectionable {

    /**
     * Relationship to the properties table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo('Devio\Properties\Models\Property');
    }

    /**
     * Should return the model value field
     *
     * @return mixed
     */
    public function getValueField()
    {
        return $this->name;
    }
}