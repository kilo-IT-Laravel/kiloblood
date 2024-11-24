<?php

namespace Database\QueryBuilder;

use InvalidArgumentException;

class Update extends BaseQuery
{
    public function updateWithCondition(array $params)
    {
        $params = $this->extractParams($params , 'update');

        $model = $params['model'];
        $data = $params['data'];
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

        return $query->update($data);
    }

    public function updateManyWithCondition(array $params)
    {
        $params = $this->extractParams($params , 'update');

        $model = $params['model'];
        $data = $params['data'];
        $condition = $params['condition'];

        if (!class_exists($model)) {
            throw new InvalidArgumentException("The model $model does not exist.");
        }

        foreach ($data as $item) {
            $query = $model::query();

            if (is_callable($condition)) {
                $query->where($condition);
            } elseif (is_array($condition)) {
                $query->where($condition);
            } else {
                throw new InvalidArgumentException('Condition must be a closure or an array');
            }

            $query->update($item);
        }

        return true;
    }
}
