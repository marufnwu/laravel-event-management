<?php
namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait CanLoadRelationships{

    public function loadRelationShips(
        QueryBuilder|Model|EloquentBuilder $for,
        ?array $relations = null
    ) :  QueryBuilder|Model|EloquentBuilder{
        $relations = $relations ?? $this->relations ?? [];
        foreach ($relations as $relation) {
            $for->when(
                $this->shouldIncludeRelation($relation),
                fn($q) => $for instanceof Model ? $for->load($relation) : $q->with($relation)
            );
        }

        return $for;

    }

    protected function shouldIncludeRelation(string $relation) : bool{
        $include = request('include');

        if(!$include){
            return false;
        }

        $relations = array_map('trim', explode(",", $include));

        return in_array($relation, $relations);

    }
}
