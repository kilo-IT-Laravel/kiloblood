<?php

namespace Database\QueryBuilder;

use InvalidArgumentException;

class Delete extends BaseQuery
{   
    public function deleteWithCondition(array $params)
    {
        $params = $this->extractParams($params , 'delete');
        $model = $params['model'];
        $condition = $params['condition'];

        if (!class_exists($model)) {
            throw new InvalidArgumentException("The model $model does not exist.");
        }

        $query = $model::query();

        if (is_callable($condition)) {
            $query->where($condition);
        } elseif (is_array($condition)) {
            $query->where($condition);
        } else {
            throw new InvalidArgumentException('Condition must be a closure or an array');
        }

        return $query->delete();
    }

    public function deleteManyWithCondition(array $params)
    {
        $params = $this->extractParams($params , 'delete');
        $model = $params['model'];
        $condition = $params['condition'];

        if (!class_exists($model)) {
            throw new InvalidArgumentException("The model $model does not exist.");
        }

        $query = $model::query();

        if (is_callable($condition)) {
            $query->where($condition);
        } elseif (is_array($condition)) {
            $query->where($condition);
        } else {
            throw new InvalidArgumentException('Condition must be a closure or an array');
        }

        return $query->delete();
    }
}
