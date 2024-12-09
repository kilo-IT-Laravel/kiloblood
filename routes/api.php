<?php
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\test;
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

// Route::post('/testing', function () {
//     // $sid = xxxx;
//     // $token = xxx;
//     // $twilio_number = xxxx;

//     // try {
//     //     // Initialize Twilio client
//     //     $client = new Client($sid, $token);

//     //     // Send SMS
//     //     $message = $client->messages->create(
//     //         '+855715341913', // Replace with the recipient's number
//     //         [
//     //             'from' => $twilio_number,
//     //             'body' => 'Test message from Laravel Twilio!'
//     //         ]
//     //     );

//     //     return response()->json([
//     //         'success' => true,
//     //         'message' => 'SMS sent successfully!',
//     //         'message_sid' => $message->sid
//     //     ]);
//     // } catch (\Exception $e) {
//     //     return response()->json([
//     //         'success' => false,
//     //         'message' => 'Error sending SMS: ' . $e->getMessage()
//     //     ], 500);
//     // }
//     // $event = Event::findOrFail(4);
//     // event(new notification($event));
//     // return response()->json(['data'=>$event]);
// });
