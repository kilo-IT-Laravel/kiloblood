<?php

namespace App\Hooks;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Storage\utils\kobeniHooks;

class HookService
{
    protected $aop;

    public function __construct(kobeniHooks $aop)
    {
        $this->aop = $aop;
        $this->registerHooks();
    }

    public function registerHooks()
    {
        $hookFiles = File::files(app_path('Hooks'));

        foreach ($hookFiles as $hookFile) {
            $className = 'App\\Hooks\\' . basename($hookFile, '.php');
            $this->registerHooksForClass($className);
        }
    }

    public function getAop(){
        return $this->aop;
    }

    private function registerHooksForClass($className)
    {
        if (class_exists($className)) {
            $beforeMethod = $className . '::registerBeforeHook';
            $afterMethod = $className . '::registerAfterHook';

            if (method_exists($className, 'registerBeforeHook')) {
                $this->aop->registerBeforeHook('registerUserBusiness', $beforeMethod);
            }

            if (method_exists($className, 'registerAfterHook')) {
                $this->aop->registerAfterHook('registerUserBusiness', $afterMethod);
            }
        } else {
            Log::error("Class $className does not exist.");
        }
    }
}