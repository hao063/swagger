<?php
/**
 *File name : api.php / Date: 2/18/25 - 9:25 PM
 *Code Owner: Haonp/ Email: haonp@omt.com.vn/ Phone: 098888889
 */

//@include base_path('swagger_auto_generated.php');


use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('test/{index2}', [TestController::class, 'index']);
Route::get('test-2', [TestController::class, 'index2']);
Route::get('test-3', [TestController::class, 'index3']);

