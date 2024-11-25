<?php

namespace Storage\utils;

use InvalidArgumentException;

class ParamExtractor
{

    public function extractParams(string $operation, array $params)
    {
        switch ($operation) {
            case 'insert':
                return $this->extractInsertParams($params);
            case 'update':
                return $this->extractUpdateParams($params);
            case 'delete':
                return $this->extractDeleteParams($params);
            case 'findAll':
                return $this->extractFindAllParams($params);
            case 'findId':
                return $this->extractFindIdParams($params);
            case 'kobeniToken':
                return $this->extractKobeniTokenParams($params);
            default:
                throw new InvalidArgumentException("Operation '$operation' is not supported.");
        }
    }

    private function extractInsertParams(array $params)
    {
        return [
            'model' => $params['model'] ?? null,
            'data' => $params['data'] ?? null,
            'condition' => $params['condition'] ?? null
        ];
    }

    private function extractUpdateParams(array $params)
    {
        return [
            'model' => $params['model'] ?? null,
            'data' => $params['data'] ?? null,
            'condition' => $params['condition'] ?? null
        ];
    }

    private function extractDeleteParams(array $params)
    {
        return [
            'model' => $params['model'] ?? null,
            'condition' => $params['condition'] ?? null
        ];
    }

    private function extractFindAllParams(array $params)
    {
        if (isset($params['relations']) && is_array($params['relations'])) {
            if (array_values($params['relations']) === $params['relations']) {
                $params['relations'] = array_fill_keys($params['relations'], null);
            }
        }

        return [
            'model' => $params['model'] ?? null,
            'sort' => $params['sort'] ?? 'latest',
            'perPage' => $params['perPage'] ?? 10,
            'relations' => $params['relations'] ?? null,
            'select' => $params['select'] ?? null,
            'where' => $params['where'] ?? null,
            'limit' => $params['limit'] ?? 10,
            'offset' => $params['offset'] ?? 0,
            'aggregate' => $params['aggregate'] ?? null,
            'dateRange' => $params['dateRange'] ?? null,
            'search' => $params['search'] ?? []
        ];
    }

    private function extractFindIdParams(array $params)
    {
        return [
            'model' => $params['model'] ?? null,
            'id' => $params['id'] ?? null,
            'ids' => $params['ids'] ?? null,
            'relations' => $params['relations'] ?? null,
            'select' => $params['select'] ?? null
        ];
    }

    private function extractKobeniTokenParams(array $params)
    {
        return [
            'model' => $params['model'] ?? null,
            'credentials' => $params['credentials'] ?? null,
            'token' => $params['token'] ?? null,
            'date' => $params['date'] ?? null
        ];
    }
}
