<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\Advisor\AuthController;
use App\Http\Controllers\Advisor\DashboardController;

Route::group(['prefix' => 'advisor', 'as' => 'advisor.'], function() {
    Route::get('login', [AuthController::class, 'index'])->name('login');
    Route::post('signup-otp', [AuthController::class, 'signupOtp'])->name('signup-otp');
    Route::post('send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');
    Route::get('sign-up', [AuthController::class, 'signup'])->name('signup');
    Route::post('signup-post', [AuthController::class, 'postSignup'])->name('post-signup');

    Route::group(['prefix' => 'auth'], function() {
        Route::post('login', [AuthController::class, 'authenticate'])->name('login-auth');
    });
    
    Route::group(['middleware' => 'web'], function() {
        Route::get('logout', function() {
            \Auth::logout();
            session()->forget('token');
            return redirect()->route('advisor.login');
        })->name('logout');

        Route::post('joined-notification', [DashboardController::class, 'joinNotification'])->name('send-joined-notification');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
        Route::get('availability', [DashboardController::class, 'availability'])->name('availability');
        Route::get('call-history', [DashboardController::class, 'callHistory'])->name('call-history');
        Route::get('chat-history', [DashboardController::class, 'chatHistory'])->name('chat-history');
        Route::get('wait-time', [DashboardController::class, 'waitTime'])->name('wait-time');
        Route::get('transactions', [DashboardController::class, 'transactions'])->name('transactions');
        Route::get('withdrawls', [DashboardController::class, 'withdrawls'])->name('withdrawls');
        Route::get('create-withdrawls', [DashboardController::class, 'createWithdrawls'])->name('create-withdrawls');
        Route::post('submit-withdrawl-request', [DashboardController::class, 'submitWithdrawlRequest'])->name('submit-withdrawl-request');
        Route::post('update-call-status', [DashboardController::class, 'updateCallStatus'])->name('update-call-status');
        Route::post('update-availability', [DashboardController::class, 'updateAvailability'])->name('update-availability');
        Route::post('update-profile', [DashboardController::class, 'updateProfile'])->name('update-profile');

        Route::get('start-call/{chatId?}', [DashboardController::class, 'startCall'])->name('start-call');

        Route::get('start-chat/{chatId?}', [DashboardController::class, 'startChat'])->name('start-chat');
    });
});