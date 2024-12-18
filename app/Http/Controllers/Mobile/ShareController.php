<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\Share;
use App\Models\SocialShare;
use Exception;
use Illuminate\Support\Facades\Auth;

class ShareController extends Koobeni
{
    public function index()
    {
        try {
            $shares = Share::where('is_active', true)->select('title' , 'link' , 'message' , 'image_url' , 'is_active' , 'language' , 'order')
                ->where('language', $this->req->language ?? 'en')
                ->get();

            return $this->dataResponse($shares);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function recordShare()
    {
        try {

            $this->req->validate([
                'platform' => 'required|string|in:facebook,twitter,telegram,instagram,whatsapp',
                'link' => 'required|string|url'
            ]);

            SocialShare::create([
                'link' => $this->req->link,
                'user_id' => Auth::id(),
                'platform' => $this->req->platform
            ]);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
