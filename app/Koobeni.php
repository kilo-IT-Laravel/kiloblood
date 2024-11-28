<?php

namespace App;

use App\Hooks\HookService;
use App\Services\AuditLog;
use Storage\utils\CustomResponse;
use Storage\utils\Exceptions;
use Storage\utils\KobeniQuery;
use Storage\utils\kobeniSecurity;
use Storage\utils\kobeniToken;
use Storage\utils\useExceptions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Storage\utils\kobeniCollection;
use Storage\utils\KobeniS3;
use Storage\utils\ModelNameFormatterTrait;
use Storage\utils\ParamExtractor;

class Koobeni extends BaseController
{
    use Exceptions, 
    CustomResponse, 
    useExceptions, 
    KobeniQuery, 
    kobeniToken, 
    kobeniSecurity, 
    kobeniCollection, 
    KobeniS3,
    ModelNameFormatterTrait;

    public Request $req;

    public $aop;

    public $logService;

    protected $paramExtractor;

    public function __construct(HookService $hookService,Request $req)
    {
        $this->req = $req;
        $this->bootKobeniQuery();
        $this->paramExtractor = new ParamExtractor();
        $this->aop = $hookService->getAop();
        $this->logService = new AuditLog();
    }

    // public function getCollection()
    // {
    //     $data = [
    //         'test' => [
    //             'test1' => [
    //                 'test2' => [
    //                     'test3' => [
    //                         'name' => 'renko',
    //                         'test4' => []
    //                     ]
    //                 ]
    //             ]
    //         ],
    //         'anotherTest' => [
    //             'test1' => [
    //                 'test2' => [
    //                     'test3' => [
    //                         'name' => 'anotherName'
    //                     ]
    //                 ]
    //             ]
    //         ],
    //         'newTest' => [
    //             'name' => 'newName'
    //         ]
    //     ];

    //     $results = $this->recursivePluck($data, 'name');

    //     return $this->dataResponse($results);
    // }
}
