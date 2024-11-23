<?php

namespace Database\QueryBuilder;

use Illuminate\Support\Facades\Log;

class FindAll extends BaseQuery
{
    private function extractParams(array $params)
    {
        if (isset($params['relations']) && is_array($params['relations'])) {
            if (array_values($params['relations']) === $params['relations']) {
                $params['relations'] = array_fill_keys($params['relations'], null);
            }
        }

        return [
            'data'      => $params['data'] ?? null,
            'sort'      => $params['sort'] ?? 'latest',
            'perPage'   => $params['perPage'] ?? 10,
            'relations' => $params['relations'] ?? null,
            'select'    => $params['select'] ?? null,
            'where'     => $params['where'] ?? null,
            'limit'     => $params['limit'] ?? 10,
            'offset'    => $params['offset'] ?? 0,
            'aggregate'  => $params['aggregate'] ?? null,
            'dateRange'  => $params['dateRange'] ?? null,
            'search'    => $params['search'] ?? []
        ];
    }

    public function allWithPagination(array $params)
    {
        $params = $this->extractParams($params);
        Log::info($params['relations']);
        $query = $this->buildQuery($params['data'], $params['sort'], $params['relations'], $params['select'], $params['where'], $params['aggregate'], $params['dateRange'], $params['search']);

        return $query->paginate($params['perPage']);
    }

    public function allWithLimit(array $params)
    {
        $params = $this->extractParams($params);

        $query = $this->buildQuery($params['data'], $params['sort'], $params['relations'], $params['select'], $params['where'], $params['aggregate'], $params['dateRange'], $params['search']);

        return $query->skip($params['offset'])->take($params['limit'])->get();
    }

    public function allData(array $params)
    {
        $params = $this->extractParams($params);

        $query = $this->buildQuery($params['data'], $params['sort'], $params['relations'], $params['select'], $params['where'], $params['aggregate'], $params['dateRange'], $params['search']);

        return $query->get();
    }

    public function allDataWithSelect(array $params)
    {
        $params = $this->extractParams($params);

        $query = $this->buildQuery($params['data'], $params['sort'], $params['relations'], $params['select'], $params['where'], $params['aggregate'], $params['dateRange'], $params['search']);

        return $query->get();
    }
}
