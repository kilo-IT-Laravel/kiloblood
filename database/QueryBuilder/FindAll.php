<?php

namespace Database\QueryBuilder;

class FindAll extends BaseQuery
{
    public function allWithPagination(array $params)
    {
        $params = $this->extractParams($params, 'findAll');
        $query = $this->buildQuery(
            $params['model'],
            $params['sort'],
            $params['relations'],
            $params['select'],
            $params['where'],
            $params['aggregate'],
            $params['dateRange'],
            $params['search'],
            $params['trash'],
            $params['rawSelects'],
            $params['groupBy'],
            $params['whereDoesntHave'],
            $params['whereHas'],
            $params['whereIn']
        );

        return $query->paginate($params['perPage']);
    }

    public function allWithLimit(array $params)
    {
        $params = $this->extractParams($params, 'findAll');

        $query = $this->buildQuery(
            $params['model'],
            $params['sort'],
            $params['relations'],
            $params['select'],
            $params['where'],
            $params['aggregate'],
            $params['dateRange'],
            $params['search'],
            $params['trash'],
            $params['rawSelects'],
            $params['groupBy'],
            $params['whereDoesntHave'],
            $params['whereHas'],
            $params['whereIn']
        );

        return $query->skip($params['offset'])->take($params['limit'])->get();
    }

    public function allData(array $params)
    {
        $params = $this->extractParams($params, 'findAll');

        $query = $this->buildQuery(
            $params['model'],
            $params['sort'],
            $params['relations'],
            $params['select'],
            $params['where'],
            $params['aggregate'],
            $params['dateRange'],
            $params['search'],
            $params['trash'],
            $params['rawSelects'],
            $params['groupBy'],
            $params['whereDoesntHave'],
            $params['whereHas'],
            $params['whereIn']
        );

        return $query->get();
    }

    public function allDataWithSelect(array $params)
    {
        $params = $this->extractParams($params, 'findAll');

        $query = $this->buildQuery(
            $params['model'],
            $params['sort'],
            $params['relations'],
            $params['select'],
            $params['where'],
            $params['aggregate'],
            $params['dateRange'],
            $params['search'],
            $params['trash'],
            $params['rawSelects'],
            $params['groupBy'],
            $params['whereDoesntHave'],
            $params['whereHas'],
            $params['whereIn']
        );

        return $query->get();
    }
}
