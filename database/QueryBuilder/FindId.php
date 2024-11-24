<?php

namespace Database\QueryBuilder;

class FindId extends BaseQuery {

    public function findById(array $params)
    {
        $params = $this->extractParams($params , 'findId');
        
        $model = $params['model']; 
        $id = $params['id'];        
        $relations = $params['relations'] ?? null; 
        $select = $params['select'] ?? null;

        $query = $this->buildQuery($model, $relations, $select);

        return $query->find($id);
    }

    public function findByIds(array $params)
    {
        $params = $this->extractParams($params , 'findId');
        
        $model = $params['model']; 
        $ids = $params['ids']; 
        $relations = $params['relations'] ?? null;  
        $select = $params['select'] ?? null;    

        $query = $this->buildQuery($model, $relations, $select);

        return $query->whereIn('id', $ids)->get();
    }
}