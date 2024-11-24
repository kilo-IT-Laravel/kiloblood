<?php

namespace Database\QueryBuilder;

use InvalidArgumentException;

class Insert extends BaseQuery
{
    public function insertWithCondition(array $params)
    {
        $params = $this->extractParams($params , 'insert');

        $model = $params['model'];
        $data = $params['data'];
        $condition = $params['condition'];

        if (!class_exists($model)) {
            throw new InvalidArgumentException("The model $model does not exist.");
        }

        if (is_callable($condition)) {
            $exists = $model::where($condition)->exists();
        } elseif (is_array($condition)) {
            $exists = $model::where($condition)->exists();
        } else {
            throw new InvalidArgumentException('Condition must be a closure or an array');
        }

        if (!$exists) {
            return $model::create($data);
        }

        return null;
    }

    public function insertManyWithCondition(array $params)
    {
        $params = $this->extractParams($params , 'insert');

        $model = $params['model'];
        $data = $params['data'];
        $condition = $params['condition'];

        if (!class_exists($model)) {
            throw new InvalidArgumentException("The model $model does not exist.");
        }

        foreach ($data as $item) {
            if (is_callable($condition)) {
                $exists = $model::where($condition)->exists();
            } elseif (is_array($condition)) {
                $exists = $model::where($condition)->exists();
            } else {
                throw new InvalidArgumentException('Condition must be a closure or an array');
            }

            if (!$exists) {
                $model::create($item);
            }
        }

        return true;
    }
}
