<?php namespace Devio\Properties;

use Illuminate\Database\Eloquent\Collection;

class OutputFormatter {

    /**
     * @var
     */
    private $elements;

    /**
     * @var
     */
    private $property;

    /**
     * @var bool
     */
    private $returnValueObject;

    public function __construct($elements, $property, $returnValueObject = false)
    {
        $this->elements          = $elements;
        $this->property          = $property;
        $this->returnValueObject = $returnValueObject;
    }

    /**
     * Format the element return, will call the right formatter
     *
     * @return mixed
     */
    public function format()
    {
        if ($this->property->isCollection())
            $result = $this->formatCollectionOutput();
        else
            $result = $this->formatStringOutput();

        // If it's a multiple property will return always a collection
        if ($this->isMultiple())
            return $result;

        // However, if it's not, just get the only element supposed to be
        // in the values relationship and return it well formatted or
        // even as model object if it was specified when created
        $result = $result->pop();

//        var_dump($result);

        return $this->returnValueObject ? $result : $result->getValueField();
    }

    /**
     * Formatting a collection output, just map it and set its "value" field
     * to the readable value from the related collection.
     *
     * @return mixed
     */
    public function formatCollectionOutput()
    {
        $collection = $this->property->getCollection();
        $returnValueObject = $this->returnValueObject;
        $elements = new Collection();

        // Mapping every single element and replacing the "value" field
        // to the proper value to be shown. This avoids to create a
        // new collection re-using the Value collection as base.
        // The item is being cloned as this way it does not
        // modifies any model structure.
        $this->elements->each(function($item) use ($collection, $returnValueObject, $elements)
        {
            $clone = $item->replicate();

            if ( ! $returnValueObject)
                $clone->{$clone->getValueFieldName()} = $collection->find($clone->getValueField())->getValueField();

            $elements->push($clone);
        });

        return $elements;
    }

    /**
     * Custom plain formatting.
     *
     * @return mixed
     */
    public function formatStringOutput()
    {
        return $this->elements;
    }

    /**
     * Returns true if the property manages multiple values.
     *
     * @return mixed
     */
    protected function isMultiple()
    {
        return $this->property->multiple;
    }

} 