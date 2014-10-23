<?php namespace Devio\Properties;

/*
 * This class will be responsible of managing attributes:
 *  - Create new
 *  - Edit existing
 *  - Delete and recursively its values
 *  - List them
 */
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