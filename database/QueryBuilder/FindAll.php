<?php

namespace Database\QueryBuilder;

use Illuminate\Support\Facades\Log;

class FindAll extends BaseQuery
{
    public function allWithPagination(array $params)
    {
        $params = $this->extractParams($params , 'findAll');
        Log::info($params['relations']);
        $query = $this->buildQuery($params['data'], $params['sort'], $params['relations'], $params['select'], $params['where'], $params['aggregate'], $params['dateRange'], $params['search']);

        return $query->paginate($params['perPage']);
    }

    public function allWithLimit(array $params)
    {
        $params = $this->extractParams($params , 'findAll');

        $query = $this->buildQuery($params['data'], $params['sort'], $params['relations'], $params['select'], $params['where'], $params['aggregate'], $params['dateRange'], $params['search']);

        return $query->skip($params['offset'])->take($params['limit'])->get();
    }

    public function allData(array $params)
    {
        $params = $this->extractParams($params , 'findAll');

        $query = $this->buildQuery($params['data'], $params['sort'], $params['relations'], $params['select'], $params['where'], $params['aggregate'], $params['dateRange'], $params['search']);

        return $query->get();
    }

    public function allDataWithSelect(array $params)
    {
        $params = $this->extractParams($params , 'findAll');

        $query = $this->buildQuery($params['data'], $params['sort'], $params['relations'], $params['select'], $params['where'], $params['aggregate'], $params['dateRange'], $params['search']);

        return $query->get();
    }
}
