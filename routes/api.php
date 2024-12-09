<?php
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::prefix('/admin')->group(function(){
//     include('test/test.php');
// });

Route::post('/register', [Authentication::class, 'register']);
Route::post('/login', [Authentication::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [Authentication::class, 'logout']);
    Route::get('/user', [Authentication::class, 'show']);
    Route::put('/update-profile', [Authentication::class, 'updateProfile']); /// not yet
    Route::put('/update-password', [Authentication::class, 'updatePassword']); /// noet yet
    Route::delete('/delete-account', [Authentication::class, 'deleteAccount']); /// not yet
    Route::put('/toggle-status', [Authentication::class, 'updateAvailability']);
    // Device management
    Route::get('/devices', [Authentication::class, 'getDeviceHistory']);
    Route::post('/devices/logout-all', [Authentication::class, 'logoutAllDevices']);
    Route::post('/devices/{$tokenId}/logout', [Authentication::class, 'logoutDevice']);
});

Route::prefix('/mobile')->middleware('auth:sanctum')->group(function () {
    include('mobile.php');
});

Route::prefix('/admin')->middleware(['auth:sanctum', 'doctor'])->group(function () {
    include('admin.php');
});

Route::get('/test/{id}', [test::class, 'bruh']);

// Route::get('/deleteTokens/{userId}', [Authentication::class, 'terminateAllDeviceTokens']);

// Route::get('/collection', [Koobeni::class , 'getCollection']);

// Route::post('/testvalidate' , [test::class , 'testbruh']);

// Route::post('/postey' , [test::class , 'testBruh1']);

Route::post('/testing' , function(Request $req){
    try{
        $content = $req->content;
        $subscriptionIds = $req->subscription_ids;
        $url = $req->url;

        $response = Http::withHeaders([
            'Authorization' => 'Basic os_v2_app_gvyht3zibfcrjdtq3ohgtzjleg7ie2n7jmpuhf5kgs52tp3dinsnpkva7ry2l4tpkjdlvlgzdpccqmuiidyttbdi5334o4m7hwljuia',
            'accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post('https://oneSignal.com/api/v1/notifications',[
            'app_id' => '357079ef-2809-4514-8e70-db8e69e52b21',
            'include_player_ids' => $subscriptionIds,
            'contents' => ['en' => $content],
            'url' => $url
        ]);

        return response()->json($response);
    }catch(Exception $e){
        return response()->json($e);
    }
});