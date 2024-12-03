<?php

namespace Database\QueryBuilder;

use Closure;
use Illuminate\Support\Facades\DB;
use Storage\utils\ParamExtractor;

class BaseQuery
{

    protected $paramExtractor;

    public function __construct()
    {
        $this->paramExtractor = new ParamExtractor();
    }

    public function extractParams(array $params, string $operation)
    {
        return $this->paramExtractor->extractParams($operation, $params);
    }

    protected function addCondition($query, $field, $operator, $value)
    {
        if ($value instanceof Closure) {
            $query->whereIn($field, $value);
        } elseif ($value !== null && $value !== '') {
            $query->where($field, $operator, $value);
        }
    }

    protected function handleSearchConditions($query, array $search)
    {
        if (empty($search)) return;

        $query->where(function ($query) use ($search) {
            $firstField = true;
            foreach ($search as $field => $term) {
                if ($term !== null && $term !== '') {
                    $method = $firstField ? 'where' : 'orWhere';

                    if (strpos($field, '.') !== false) {
                        [$relation, $column] = explode('.', $field);
                        $query->$method(function ($q) use ($relation, $column, $term) {
                            $q->whereHas($relation, function ($q) use ($column, $term) {
                                $q->where($column, 'like', "%{$term}%");
                            });
                        });
                    } else {
                        $query->$method($field, 'like', "%{$term}%");
                    }

                    $firstField = false;
                }
            }
        });
    }

    protected $rawSelects = [];

    protected function handleRawSelects($query)
    {
        foreach ($this->rawSelects as $rawSelect) {
            $query->selectRaw($rawSelect);
        }
    }

    public function buildQuery(
        $Data,
        $sort,
        $relations = null,
        $select = null,
        $where = null,
        $aggregate = null,
        $dateRange = null,
        $search = [],
        $onlyTrash = false,
        $rawSelects = [],
        $groupBy = null
    ) {
        $query = $Data::query();

        $this->rawSelects = $rawSelects;

        if ($onlyTrash) {
            $query->onlyTrashed();
        }

        if ($relations) {
            $withArray = [];
            foreach ($relations as $relation => $closure) {
                if ($closure instanceof Closure) {
                    $withArray[$relation] = $closure;
                } else {
                    $withArray[] = $relation;
                }
            }

            $query->with($withArray);
        }

        if ($select) {
            $query->select($select);
        }

        $this->handleRawSelects($query);

        if (is_array($sort)) {
            $query->orderBy($sort[0], $sort[1]);
        } else {
            switch ($sort) {
                case 'latest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
            }
        }

        if ($where) {
            $query->where(function ($query) use ($where) {
                foreach ($where as $condition) {
                    if ($condition instanceof Closure) {
                        $query->where($condition);
                    } else {
                        if (isset($condition[2]) && $condition[2] !== null && $condition[2] !== '') {
                            $query->where($condition[0], $condition[1], $condition[2]);
                        }
                    }
                }
            });
        }

        if ($aggregate) {
            foreach ($aggregate as $alias => $function) {
                if (in_array($aggregate, ['count', 'sum', 'avg', 'min', 'max'])) {
                    $query->addSelect([
                        $alias => DB::raw("$function(*) as $alias")
                    ]);
                }
            }
        }

        if ($dateRange && isset($dateRange['startDate']) && isset($dateRange['endDate'])) {
            $query->whereBetween('created_at', [$dateRange['startDate'], $dateRange['endDate']]);
        }

        $this->handleSearchConditions($query, $search);

        if ($groupBy) {
            $query->groupBy($groupBy);
        }

        return $query;
    }
}
