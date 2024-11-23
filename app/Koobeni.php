<?php

namespace App;

use Storage\utils\CustomResponse;
use Storage\utils\Exceptions;
use Storage\utils\KobeniQuery;
use Storage\utils\kobeniSecurity;
use Storage\utils\kobeniToken;
use Storage\utils\useExceptions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Storage\utils\kobeniCollection;

class Koobeni extends BaseController
{
    use Exceptions, CustomResponse, useExceptions, KobeniQuery, kobeniToken , kobeniSecurity , kobeniCollection;

    public Request $req;

    public function __construct(Request $req)
    {
        $this->req = $req;
        $this->bootKobeniQuery();
    }

    public function getCollection()
    {
        $data = [
            'test' => [
                'test1' => [
                    'test2' => [
                        'test3' => [
                            'name' => 'renko',
                            'test4' => []
                        ]
                    ]
                ]
            ],
            'anotherTest' => [
                'test1' => [
                    'test2' => [
                        'test3' => [
                            'name' => 'anotherName'
                        ]
                    ]
                ]
            ]
        ];

        $results = $this->recursivePluck($data, 'name');
        
        return $this->dataResponse($results);
    }
}
