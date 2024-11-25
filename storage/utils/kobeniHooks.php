<?php

namespace Storage\utils;

use Illuminate\Support\Facades\Log;

class kobeniHooks
{
    private $beforeHooks = [];
    private $afterHooks = [];

    public function registerBeforeHook($methodName, callable $callback)
    {
        $this->beforeHooks[$methodName][] = $callback;
    }

    public function registerAfterHook($methodName, callable $callback)
    {
        $this->afterHooks[$methodName][] = $callback;
    }

    public function executeBeforeHooks($methodName, &$data)
    {
        if (isset($this->beforeHooks[$methodName])) {
            foreach ($this->beforeHooks[$methodName] as $hook) {
                
                $data = $hook($data);
            }
        }
    }

    public function executeAfterHooks($methodName, &$result)
    {
        if (isset($this->afterHooks[$methodName])) {
            foreach ($this->afterHooks[$methodName] as $hook) {
                $result = $hook($result);
            }
        }
    }

    public function callMethodWithHooks($object, $methodName, $params)
    {
        $this->executeBeforeHooks($methodName, $params);

        $result = call_user_func_array([$object, $methodName], $params);

        $this->executeAfterHooks($methodName, $result);

        return $result;
    }
}
