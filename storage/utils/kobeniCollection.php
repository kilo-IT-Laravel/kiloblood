<?php

namespace Storage\utils;
use Illuminate\Support\Facades\Storage;
trait kobeniCollection
{
    public function recursivePluck(array $array, $key)
    {
        $result = [];

        foreach ($array as $item) {
            if (is_array($item)) {
                if (array_key_exists($key, $item)) {
                    $result[] = $item[$key];
                }

                $result = array_merge($result, $this->recursivePluck($item, $key));
            }
        }

        return $result;
    }



    public  function recursive($req){
        $data = [];
        if($req->name === "renko"){
            $data = true;
        }
        $data = $this->recursive($req);
        return $data;
    }



    public function filterByKeyValue(array $array, $key, $value)
    {
        return array_filter($array, function ($item) use ($key, $value) {
            return isset($item[$key]) && $item[$key] == $value;
        });
    }

    public function mapToKeyValue(array $array, $keyField, $valueField)
    {
        $result = [];

        foreach ($array as $item) {
            if (isset($item[$keyField]) && isset($item[$valueField])) {
                $result[$item[$keyField]] = $item[$valueField];
            }
        }

        return $result;
    }

    public function nestByKey(array $array, $parentKey)
    {
        $nested = [];
        foreach ($array as $item) {
            if (isset($item[$parentKey])) {
                $nested[$item[$parentKey]][] = $item;
            }
        }
        return $nested;
    }

    public function sortByKey(array $array, $key)
    {
        usort($array, function ($a, $b) use ($key) {
            return strcmp($a[$key], $b[$key]);
        });
        return $array;
    }


}
