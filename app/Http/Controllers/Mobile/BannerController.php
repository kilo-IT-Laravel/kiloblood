<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\Banner;
use App\Services\BannersManagment;
use Exception;

class BannerController extends Koobeni
{
    private $bannerService;

    public function __construct()
    {
        $this->bannerService = new BannersManagment();
    }

    public function index()
    {
        try{
            $banners = Banner::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

            return $this->dataResponse($banners);
        }catch(Exception $e){
            return $this->handleException($e, $this->req);
        }
    }

    public function show(int $bannerId)
    {
        try{
            $banner = Banner::findOrFail($bannerId);

            if (!$banner->is_active) {
                return $this->ModelNotFound();
            }

            return $this->dataResponse($banner);
        }catch(Exception $e){
            return $this->handleException($e, $this->req);
        }
    }
}