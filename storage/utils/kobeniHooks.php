<?php

namespace Storage\utils;

trait kobeniHooks
{
    protected $methodAfterhook = [];
    protected $methodBeforehook = [];

    public function registerMethodAfterhook($methodName , callable $callback){
        return $this->methodAfterhooks[$methodName][] = $callback;
    }

    public function registerMethodBeforehook($methodName , callable $callback){
        return $this->methodBeforehooks[$methodName][] = $callback;
    }

    public function executeMethodNameAfterhook($methodName , $data){
        if(isset($this->methodAfterhooks[$methodName])){
            foreach($this->methodAfterhooks[$methodName] as $hook){
                $data = $hook($data);
            }
        }

        return $data;
    }

    public function executeMethodNameBeforehook($methodName , $data){
        if(isset($this->methodBeforehooks[$methodName])){
            foreach($this->methodBeforehooks[$methodName] as $hook){
                $data = $hook($data);
            }
        }

        return $data;
    }
}
