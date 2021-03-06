<?php namespace Devio\Properties;

use Devio\Properties\Models\PropertyCategory;

class PropertyManager {

    /**
     * Fetches all the properties from a given category
     *
     * @param $category
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getCategoryProperties($category)
    {
        $category = PropertyCategory::with('properties')->where('name', $category)->first();

        if ($category)
            return $category->properties;

        return null;
    }

} 