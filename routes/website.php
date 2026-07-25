<?php



use Illuminate\Support\Facades\Route;
use App\Models\AdminModel\StaticPage;


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



Route::get('/', function () {
    return view('index');
});

Route::get('/home', function () {
    return view('index');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

Route::get('/privacy-policy', function () {
    $data = StaticPage::where('slug', 'privacy_policy')->first();
    return view('privacypolicy', compact('data'));
})->name('privacy-policy');

Route::get('/terms-condition', function () {
    $data = StaticPage::where('slug', 'terms_condition')->first();
    return view('privacypolicy', compact('data'));
})->name('terms-condition');

