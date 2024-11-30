<?php

namespace App\Services;

use Storage\utils\ServiceTrait;

class BaseService {

    use ServiceTrait;

    public function __construct() {
        $this->bootService();
    }

}