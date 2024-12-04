<?php

namespace App;

use App\Hooks\HookService;
use App\Services\AuditLog;
use App\Services\BannersManagment;
use App\Services\EventManagement;
use App\Services\FIleService;
use App\Services\ShareManagement;
use App\Services\UserManagment;
use Storage\utils\CustomResponse;
use Storage\utils\Exceptions;
use Storage\utils\kobeniSecurity;
use Storage\utils\kobeniToken;
use Storage\utils\useExceptions;
use Illuminate\Routing\Controller as BaseController;
use Storage\utils\kobeniCollection;
use Storage\utils\KobeniS3;
use Storage\utils\ModelNameFormatterTrait;
use Storage\utils\ParamExtractor;
use Storage\utils\ServiceTrait;

class Koobeni extends BaseController
{
    use Exceptions, 
    CustomResponse, 
    useExceptions, 
    ServiceTrait,
    kobeniToken, 
    kobeniSecurity, 
    kobeniCollection, 
    KobeniS3,
    ModelNameFormatterTrait;

    public $aop;

    public $logService;

    protected $paramExtractor;

    /////// services;
    protected $bannerService;
    protected $eventService;
    protected $shareService;
    protected $userService;
    protected $fileService;

    public function __construct(HookService $hookService)
    {
        $this->bootService();
        $this->paramExtractor = new ParamExtractor();
        $this->aop = $hookService->getAop();
        $this->logService = new AuditLog();
        
        ///// services
        $this->bannerService = new BannersManagment();
        $this->eventService = new EventManagement();
        $this->shareService = new ShareManagement();
        $this->userService = new UserManagment();
        $this->fileService = new FIleService();
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
