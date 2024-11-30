<?php

namespace Storage\utils;

use Database\QueryBuilder\Delete;
use Database\QueryBuilder\FindAll;
use Database\QueryBuilder\FindId;
use Database\QueryBuilder\Insert;
use Database\QueryBuilder\Update;
use Illuminate\Http\Request;

trait ServiceTrait
{
    protected $findAll;
    protected $delete;
    protected $update;
    protected $findId;
    protected $insert;
    protected $req;

    public function bootService(){
        $this->findAll = app(FindAll::class);
        $this->findId = app(FindId::class);
        $this->delete = app(Delete::class);
        $this->insert = app(Insert::class);
        $this->update = app(Update::class);
        $this->req = app(Request::class);
    }
}
