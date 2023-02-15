<?php

namespace Salah3id\Domains\Repository\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\JoinClause;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Salah3id\Domains\Repository\Exceptions\InvalidRelation;
use Salah3id\Domains\Repository\Exceptions\InvalidAggregateMethod;
use Salah3id\Domains\Repository\Exceptions\InvalidRelationGlobalScope;
use Salah3id\Domains\Repository\Exceptions\InvalidRelationWhere;

class SortByRelation extends Builder implements Sort
{

    //constants
    const AGGREGATE_SUM = 'SUM';
    const AGGREGATE_AVG = 'AVG';
    const AGGREGATE_MAX = 'MAX';
    const AGGREGATE_MIN = 'MIN';
    const AGGREGATE_COUNT = 'COUNT';


    //aggregate method
    protected $aggregateMethod = self::AGGREGATE_MAX;

    //leftJoin
    protected $leftJoin = true;

    //store joined tables, we want join table only once (e.g. when you call orderByJoin more time)
    protected $joinedTables = [];

    //store if ->select(...) is already called on builder (we want only one groupBy())
    protected $selected = false;


    public function __invoke(Builder $query, bool $descending, string $property, $aggregateMethod = null)
    {
        $direction = $descending ? 'desc' : 'asc';

        $performJoin = $this->performJoin($query,$property);
        $property = $performJoin[0];
        $query = $performJoin[1];

        $aggregateMethod = $aggregateMethod ? $aggregateMethod : $this->aggregateMethod;
        $this->checkAggregateMethod($aggregateMethod);
        $sortsCount = count($query->query->orders ?? []);
        $sortAlias = 'sort'.(0 == $sortsCount ? '' : ($sortsCount + 1));

        $grammar = \DB::query()->getGrammar();
        $query->selectRaw($aggregateMethod.'('.$grammar->wrap($property).') as '.$sortAlias);
        return $query->orderByRaw($sortAlias.' '.$direction);
    }


    //helpers methods
    protected function performJoin($query,$relations, $leftJoin = null)
    {
        //detect join method
        $leftJoin = null !== $leftJoin ? $leftJoin : $this->leftJoin;
        $joinMethod = $leftJoin ? 'leftJoin' : 'join';

        //detect current model data
        $relations = explode('.', $relations);
        $property = end($relations);
        $baseModel = $query->getModel();
        $baseTable = $baseModel->getTable();
        $basePrimaryKey = $baseModel->getKeyName();

        $currentModel = $baseModel;
        $currentTableAlias = $baseTable;

        $relationsAccumulated = [];
        foreach ($relations as $relation) {
            if ($relation == $property) {
                //last item in $relations argument is sort|where property
                break;
            }

            /** @var Relation $relatedRelation */
            $relatedRelation = $currentModel->$relation();
            $relatedModel = $relatedRelation->getRelated();
            $relatedPrimaryKey = $relatedModel->getKeyName();
            $relatedTable = $relatedModel->getTable();
            $relatedTableAlias = $relatedTable;

            $relationsAccumulated[] = $relatedTableAlias;
            $relationAccumulatedString = implode('_', $relationsAccumulated);

            if (!in_array($relationAccumulatedString, $this->joinedTables)) {
                $joinQuery = $relatedTable;
                if ($relatedRelation instanceof BelongsTo) {
                    $relatedKey = is_callable([$relatedRelation, 'getQualifiedForeignKeyName']) ? $relatedRelation->getQualifiedForeignKeyName() : $relatedRelation->getQualifiedForeignKey();
                    $relatedKey = last(explode('.', $relatedKey));
                    $ownerKey = is_callable([$relatedRelation, 'getOwnerKeyName']) ? $relatedRelation->getOwnerKeyName() : $relatedRelation->getOwnerKey();

                    $query->$joinMethod($joinQuery, function ($join) use ($relatedRelation, $relatedTableAlias, $relatedKey, $currentTableAlias, $ownerKey) {
                        $join->on($relatedTableAlias.'.'.$ownerKey, '=', $currentTableAlias.'.'.$relatedKey);

                        $this->joinQuery($join, $relatedRelation, $relatedTableAlias);
                    });
                } elseif ($relatedRelation instanceof HasOne || $relatedRelation instanceof HasMany) {
                    $relatedKey = $relatedRelation->getQualifiedForeignKeyName();
                    $relatedKey = last(explode('.', $relatedKey));
                    $localKey = $relatedRelation->getQualifiedParentKeyName();
                    $localKey = last(explode('.', $localKey));

                    $query->$joinMethod($joinQuery, function ($join) use ($relatedRelation, $relatedTableAlias, $relatedKey, $currentTableAlias, $localKey) {
                        $join->on($relatedTableAlias.'.'.$relatedKey, '=', $currentTableAlias.'.'.$localKey);

                        $this->joinQuery($join, $relatedRelation, $relatedTableAlias);
                    });
                } else {
                    throw new InvalidRelation();
                }
            }

            $currentModel = $relatedModel;
            $currentTableAlias = $relatedTableAlias;

            $this->joinedTables[] = implode('_', $relationsAccumulated);
        }

        if (!$this->selected && count($relations) > 1) {
            $this->selected = true;
            $query->selectRaw($baseTable.'.*');
            $query->groupBy($baseTable.'.'.$basePrimaryKey);
        }

        return [$currentTableAlias.'.'.$property,$query];
    }


    protected function joinQuery($join, $relation, $relatedTableAlias)
    {
        /** @var Builder $relationQuery */
        $relationBuilder = $relation->getQuery();

        //apply clauses on relation
        if (isset($relationBuilder->relationClauses)) {
            foreach ($relationBuilder->relationClauses as $clause) {
                foreach ($clause as $method => $params) {
                    $this->applyClauseOnRelation($join, $method, $params, $relatedTableAlias);
                }
            }
        }

        //apply global SoftDeletingScope
        foreach ($relationBuilder->scopes as $scope) {
            if ($scope instanceof SoftDeletingScope) {
                $this->applyClauseOnRelation($join, 'withoutTrashed', [], $relatedTableAlias);
            } else {
                throw new InvalidRelationGlobalScope();
            }
        }
    }

    private function applyClauseOnRelation(JoinClause $join, string $method, array $params, string $relatedTableAlias)
    {
        if (in_array($method, ['where', 'orWhere'])) {
            try {
                if (is_array($params[0])) {
                    foreach ($params[0] as $k => $param) {
                        $params[0][$relatedTableAlias.'.'.$k] = $param;
                        unset($params[0][$k]);
                    }
                } else {
                    $params[0] = $relatedTableAlias.'.'.$params[0];
                }

                call_user_func_array([$join, $method], $params);
            } catch (\Exception $e) {
                throw new InvalidRelationWhere();
            }
        } elseif (in_array($method, ['withoutTrashed', 'onlyTrashed', 'withTrashed'])) {
            if ('withTrashed' == $method) {
                //do nothing
            } elseif ('withoutTrashed' == $method) {
                call_user_func_array([$join, 'where'], [$relatedTableAlias.'.deleted_at', '=', null]);
            } elseif ('onlyTrashed' == $method) {
                call_user_func_array([$join, 'where'], [$relatedTableAlias.'.deleted_at', '<>', null]);
            }
        } else {
            throw new InvalidRelationClause();
        }
    }

    private function checkAggregateMethod($aggregateMethod)
    {
        if (!in_array($aggregateMethod, [
            self::AGGREGATE_SUM,
            self::AGGREGATE_AVG,
            self::AGGREGATE_MAX,
            self::AGGREGATE_MIN,
            self::AGGREGATE_COUNT,
        ])) {
            throw new InvalidAggregateMethod();
        }
    }
}