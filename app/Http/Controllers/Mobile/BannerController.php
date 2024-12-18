<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\Banner;
use Exception;

class BannerController extends Koobeni
{
    public function index()
    {
        try{
            $banners = Banner::where('is_active', true)
            ->select('image','title','description','link' , 'order' , 'is_active' , 'event_id')
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