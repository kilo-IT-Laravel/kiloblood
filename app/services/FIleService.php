<?php

namespace App\Services;

use App\Models\File;

class FIleService extends BaseService
{
    public function uploading(string $file , string $path)
    {
        $url = $this->req->file($file)->store($path, 'public');
        return File::create([
            'file_url' => $url,
            'file_type' => $this->req->file($file)->getClientOriginalExtension(),
            'file_name' => $this->req->file($file)->getClientOriginalName(),
        ]);
    }
}
