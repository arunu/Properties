<?php namespace Devio\Properties;

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
        return PropertyCategory::with('properties')->where('name', $category)->first()->properties;
    }

} 