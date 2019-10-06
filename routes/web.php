<?php
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

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('/backup/{id}/download','BackupController@download');
    Route::get('/message/{message_uuid}/attachment/{attachment_uuid}/download','MessageController@download');
    Route::get('/announcement/{id}/attachment/{attachment_uuid}/download','AnnouncementController@download');
    Route::get('/job/{uuid}/attachment/{attachment_uuid}/download','JobController@download');
    Route::get('/job/{uuid}/sub-job/{suuid}/attachment/{attachment_uuid}/download','SubJobController@download');
    Route::get('/job/{uuid}/note/{nuuid}/attachment/{attachment_uuid}/download','JobNoteController@download');
    Route::get('/job/{uuid}/attachment/{auuid}/attachment/{attachment_uuid}/download','JobAttachmentController@download');

    Route::post('/notification', 'NotificationController@post');

});
Route::get('/auth/social/{provider}', 'SocialLoginController@providerRedirect');
Route::get('/auth/{provider}/callback', 'SocialLoginController@providerRedirectCallback');




// Used to get translation in json format for current locale

Route::get('/js/lang', function () {
    if(App::environment('local'))
        Cache::forget('lang.js');
    $strings = Cache::rememberForever('lang.js', function () {
        $lang = config('app.locale');
        $files   = glob(resource_path('lang/' . $lang . '/*.php'));
        $strings = [];
        foreach ($files as $file) {
            $name           = basename($file, '.php');
            $strings[$name] = require $file;
        }
        return $strings;
    });
    header('Content-Type: text/javascript');
    echo('window.i18n = ' . json_encode($strings) . ';');
    exit();
})->name('assets.lang');

Route::get('/{vue?}', function () {
    return view('home');
})->where('vue', '[\/\w\.-]*')->name('home');
