<?php namespace Devio\Properties\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyHasMany extends HasMany {

    protected $entity;

    public function __construct(Builder $query, Model $model, $entity)
    {
        $this->entity = $entity;

        parent::__construct($query, $model, 'entity', '');
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $this->query->where($this->foreignKey, $this->entity);
//        $this->query->whereIn($this->foreignKey, [$this->entity]);
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @param  string  $type
     * @return array
     */
    protected function matchOneOrMany(array $models, Collection $results, $relation, $type)
    {
        foreach ($models as $model)
        {
            $model->setRelation($relation, $results);
        }

        return $models;
    }

    /**
     * Get the key value of the parent's local key.
     *
     * @return mixed
     */
    public function getParentKey()
    {
        return $this->entity;
    }

}