<?php

namespace Database\QueryBuilder;

use Closure;
use Illuminate\Support\Facades\DB;
use Storage\utils\ParamExtractor;

class BaseQuery {

    protected $paramExtractor;

    public function __construct() {
        $this->paramExtractor = new ParamExtractor();
    }

    public function extractParams(array $params , string $operation){
        return $this->paramExtractor->extractParams($operation , $params);
    }

    public function buildQuery(
        $Data, 
        $sort, 
        $relations = null, 
        $select = null , 
        $where = null,
        $aggregate = null,
        $dateRange = null,
        $search = [],
        $onlyTrash = false 
    ){
        $query = $Data::query();

        if($onlyTrash){
            $query->onlyTrashed();
        }

        if ($relations) {
            foreach ($relations as $relation => $closure) {
                if ($closure instanceof Closure) {
                    $query->with([$relation => $closure]);
                } else {
                    $query->with($relation);
                }
            }
        }

        if ($select) {
            $query->select($select);
        }

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
            foreach ($where as $condition) {
                if($condition instanceof Closure) {
                    $query->where($condition);
                }else{
                    $query->where($condition[0], $condition[1], $condition[2]);
                }
            }
        }

        if($aggregate){
            foreach($aggregate as $alias => $function){
                if(in_array($aggregate, ['count', 'sum', 'avg', 'min', 'max'])){
                    $query->addSelect([
                        $alias => DB::raw("$function(*) as $alias") 
                    ]);
                }
            }
        }

        if($dateRange){
            if(isset($dateRange['startDate']) && isset($dateRange['endDate'])){
                $query->whereBetween('created_at', [$dateRange['startDate'], $dateRange['endDate']]);
            }
        }

        $query->when($search && is_array($search), function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                foreach ($search as $field => $term) {
                    if ($term) {
                        $query->orWhere($field, 'like', "%{$term}%");
                    }
                }
            });
        });

        return $query;
    }
}