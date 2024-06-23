<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group([
//     'prefix' => 'auth'
// ], function () {
//   //  Route::post('login', 'AuthController@login');
//    // Route::post('signup', 'AuthController@signUp');
//    Route::post('/register', [UserController::class, 'register']);
// Route::post('/login', [UserController::class, 'login']);
  
//     Route::group([
//       'middleware' => 'auth:api'
//     ], function() {
//         Route::get('logout', 'AuthController@logout');
//         Route::get('user', 'AuthController@user');
//     });
// });
Route::post('/players', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
//Route::get('/players', [UserController::class, 'getAllPlayers']);
//Route::post('/logout', [UserController::class, 'logout']);

Route::middleware(['auth:api'])->group(function () {
    Route::put('/players/{id} ', [UserController::class, 'update']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/show', [UserController::class, 'show']);

    Route::middleware(['auth:api', 'adminRole'])->group(function () {
        Route::get('/players', [UserController::class, 'getAllPlayers']);
        Route::get('/players/ranking', [UserController::class, 'getRanking']);
        Route::get('/players/ranking/loser', [UserController::class, 'getLoser']);
        Route::get('/players/ranking/winner', [UserController::class, 'getWinner']);
    });

    Route::middleware(['auth:api', 'userRole'])->group(function () {
        Route::post('/players/{id}/games', [GameController::class, 'play']);
        Route::delete('/players/{id}/games', [GameController::class, 'deleteGames']);
        Route::get('/players/{id}/games', [GameController::class, 'getGames']);
    });
});
