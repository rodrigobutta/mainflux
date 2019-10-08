<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login','AuthController@authenticate');
    Route::post('/check','AuthController@check');
    Route::post('/register','AuthController@register');
    Route::get('/activate/{token}','AuthController@activate');
    Route::post('/password','AuthController@password');
    Route::post('/validate-password-reset','AuthController@validatePasswordReset');
    Route::post('/reset','AuthController@reset');
    Route::post('/social/token','SocialLoginController@getToken');
});

Route::get('/configuration/variable','ConfigurationController@getConfigurationVariable');

Route::group(['middleware' => ['jwt.auth']], function () {

    Route::post('/auth/logout','AuthController@logout');
    Route::post('/auth/lock','AuthController@lock');
    Route::post('/demo/message','HomeController@demoMessage');

    Route::post('/change-password','AuthController@changePassword');

    Route::post('/upload','UploadController@upload');
    Route::post('/upload/extension','UploadController@getAllowedExtension');
    Route::post('/upload/image','UploadController@uploadImage');
    Route::post('/upload/fetch','UploadController@fetch');
    Route::post('/upload/{id}','UploadController@destroy');

    Route::get('/dashboard','HomeController@dashboard');

    Route::get('/configuration','ConfigurationController@index');
    Route::post('/configuration','ConfigurationController@store');
    Route::post('/configuration/logo/{type}','ConfigurationController@uploadLogo');
    Route::delete('/configuration/logo/{type}/remove','ConfigurationController@removeLogo');
    Route::get('/fetch/lists','ConfigurationController@fetchList');

    Route::post('/backup','BackupController@store');
    Route::get('/backup','BackupController@index');
    Route::delete('/backup/{id}','BackupController@destroy');

    Route::get('/locale','LocaleController@index');
    Route::post('/locale','LocaleController@store');
    Route::get('/locale/{id}','LocaleController@show');
    Route::patch('/locale/{id}','LocaleController@update');
    Route::delete('/locale/{id}','LocaleController@destroy');
    Route::post('/locale/fetch','LocaleController@fetch');
    Route::post('/locale/translate','LocaleController@translate');
    Route::post('/locale/add-word','LocaleController@addWord');

    Route::get('/role','RoleController@index');
    Route::get('/role/{id}','RoleController@show');
    Route::post('/role','RoleController@store');
    Route::delete('/role/{id}','RoleController@destroy');

    Route::get('/permission','PermissionController@index');
    Route::get('/permission/assign/pre-requisite','PermissionController@preRequisite');
    Route::get('/permission/{id}','PermissionController@show');
    Route::post('/permission','PermissionController@store');
    Route::delete('/permission/{id}','PermissionController@destroy');
    Route::post('/permission/assign','PermissionController@assignPermission');

    Route::get('/ip-filter','IpFilterController@index');
    Route::get('/ip-filter/{id}','IpFilterController@show');
    Route::post('/ip-filter','IpFilterController@store');
    Route::patch('/ip-filter/{id}','IpFilterController@update');
    Route::delete('/ip-filter/{id}','IpFilterController@destroy');

    Route::get('/email-template','EmailTemplateController@index');
    Route::post('/email-template','EmailTemplateController@store');
    Route::get('/email-template/{id}','EmailTemplateController@show');
    Route::patch('/email-template/{id}','EmailTemplateController@update');
    Route::delete('/email-template/{id}','EmailTemplateController@destroy');
    Route::get('/email-template/{category}/lists','EmailTemplateController@lists');
    Route::get('/email-template/{id}/content','EmailTemplateController@getContent');

    Route::get('/todo','TodoController@index');
    Route::get('/todo/{id}','TodoController@show');
    Route::post('/todo','TodoController@store');
    Route::patch('/todo/{id}','TodoController@update');
    Route::delete('/todo/{id}','TodoController@destroy');
    Route::post('/todo/{id}/status','TodoController@toggleStatus');
    Route::post('/todo/recent','TodoController@recent');

    Route::get('/user/pre-requisite','UserController@preRequisite');
    Route::get('/user/detail','UserController@detail');
    Route::get('/user','UserController@index');
    Route::get('/user/{id}','UserController@show');
    Route::post('/user','UserController@store');
    Route::post('/user/{id}/status','UserController@updateStatus');
    Route::patch('/user/{id}','UserController@update');
    Route::patch('/user/{id}/contact','UserController@updateContact');
    Route::patch('/user/{id}/social','UserController@updateSocial');
    Route::patch('/user/{id}/force-reset-password','UserController@forceResetPassword');
    Route::patch('/user/{id}/email','UserController@sendEmail');
    Route::post('/user/profile/update','UserController@updateProfile');
    Route::post('/user/profile/avatar/{id}','UserController@uploadAvatar');
    Route::delete('/user/profile/avatar/remove/{id}','UserController@removeAvatar');
    Route::delete('/user/{uuid}','UserController@destroy');

    Route::get('/message/compose/pre-requisite','MessageController@preRequisite');
    Route::post('/message/statistics','MessageController@statistics');
    Route::post('/message/compose','MessageController@store');
    Route::post('/message/reply','MessageController@reply');
    Route::get('/message/{uuid}/reply','MessageController@loadReply');
    Route::get('/message/draft','MessageController@getDraftList');
    Route::get('/message/{uuid}/draft','MessageController@getDraft');
    Route::get('/message/inbox','MessageController@getInboxList');
    Route::get('/message/sent','MessageController@getSentList');
    Route::get('/message/important','MessageController@getImportantList');
    Route::get('/message/trash','MessageController@getTrashList');
    Route::delete('/message/{uuid}/draft','MessageController@destroyDraft');
    Route::post('/message/{uuid}/trash','MessageController@trash');
    Route::post('/message/{uuid}/restore','MessageController@restore');
    Route::delete('/message/{id}/delete','MessageController@destroy');
    Route::get('/message/{uuid}','MessageController@show');
    Route::post('/message/{uuid}/important','MessageController@toggleImportant');

    Route::get('/email-log','EmailLogController@index');
    Route::get('/email-log/{id}','EmailLogController@show');
    Route::delete('/email-log/{id}','EmailLogController@destroy');

    Route::get('/activity-log','ActivityLogController@index');
    Route::delete('/activity-log/{id}','ActivityLogController@destroy');

    Route::get('/department','DepartmentController@index');
    Route::get('/department/{id}','DepartmentController@show');
    Route::post('/department','DepartmentController@store');
    Route::patch('/department/{id}','DepartmentController@update');
    Route::delete('/department/{id}','DepartmentController@destroy');

    Route::get('/client','ClientController@index');
    Route::get('/client/{id}','ClientController@show');
    Route::post('/client','ClientController@store');
    Route::patch('/client/{id}','ClientController@update');
    Route::delete('/client/{id}','ClientController@destroy');

    Route::get('/contractor','ContractorController@index');
    Route::get('/contractor/{id}','ContractorController@show');
    Route::post('/contractor','ContractorController@store');
    Route::patch('/contractor/{id}','ContractorController@update');
    Route::delete('/contractor/{id}','ContractorController@destroy');

    Route::get('/task/pre-requisite','TaskController@preRequisite');
    Route::get('/task','TaskController@index');
    Route::get('/task/{id}','TaskController@show');
    Route::post('/task','TaskController@store');
    Route::patch('/task/{id}','TaskController@update');
    Route::delete('/task/{id}','TaskController@destroy');

    Route::get('/task-relevance','TaskRelevanceController@index');
    Route::get('/task-relevance/{id}','TaskRelevanceController@show');
    Route::post('/task-relevance','TaskRelevanceController@store');
    Route::patch('/task-relevance/{id}','TaskRelevanceController@update');
    Route::delete('/task-relevance/{id}','TaskRelevanceController@destroy');

    Route::get('/task-frequency','TaskFrequencyController@index');
    Route::get('/task-frequency/{id}','TaskFrequencyController@show');
    Route::post('/task-frequency','TaskFrequencyController@store');
    Route::patch('/task-frequency/{id}','TaskFrequencyController@update');
    Route::delete('/task-frequency/{id}','TaskFrequencyController@destroy');

    Route::get('/task-complexity','TaskComplexityController@index');
    Route::get('/task-complexity/{id}','TaskComplexityController@show');
    Route::post('/task-complexity','TaskComplexityController@store');
    Route::patch('/task-complexity/{id}','TaskComplexityController@update');
    Route::delete('/task-complexity/{id}','TaskComplexityController@destroy');

    Route::get('/task-family','TaskFamilyController@index');
    Route::get('/task-family/{id}','TaskFamilyController@show');
    Route::post('/task-family','TaskFamilyController@store');
    Route::patch('/task-family/{id}','TaskFamilyController@update');
    Route::delete('/task-family/{id}','TaskFamilyController@destroy');

    Route::get('/designation/pre-requisite','DesignationController@preRequisite');
    Route::get('/designation','DesignationController@index');
    Route::get('/designation/{id}','DesignationController@show');
    Route::post('/designation','DesignationController@store');
    Route::patch('/designation/{id}','DesignationController@update');
    Route::delete('/designation/{id}','DesignationController@destroy');

    Route::get('/asset/pre-requisite','AssetController@preRequisite');
    Route::get('/asset','AssetController@index');
    Route::get('/asset/{id}','AssetController@show');
    Route::post('/asset','AssetController@store');
    Route::patch('/asset/{id}','AssetController@update');
    Route::delete('/asset/{id}','AssetController@destroy');

    Route::get('/project/pre-requisite','ProjectController@preRequisite');
    Route::get('/project','ProjectController@index');
    Route::get('/project/{id}','ProjectController@show');
    Route::post('/project','ProjectController@store');
    Route::patch('/project/{id}','ProjectController@update');
    Route::delete('/project/{id}','ProjectController@destroy');

    Route::get('/location/pre-requisite','LocationController@preRequisite');
    Route::get('/location','LocationController@index');
    Route::get('/location/{id}','LocationController@show');
    Route::post('/location','LocationController@store');
    Route::patch('/location/{id}','LocationController@update');
    Route::delete('/location/{id}','LocationController@destroy');

    Route::get('/announcement/pre-requisite','AnnouncementController@preRequisite');
    Route::get('/announcement','AnnouncementController@index');
    Route::get('/announcement/{id}','AnnouncementController@show');
    Route::post('/announcement','AnnouncementController@store');
    Route::patch('/announcement/{id}','AnnouncementController@update');
    Route::delete('/announcement/{id}','AnnouncementController@destroy');

    Route::get('/question-set','QuestionSetController@index');
    Route::get('/question-set/{id}','QuestionSetController@show');
    Route::post('/question-set','QuestionSetController@store');
    Route::delete('/question-set/{id}','QuestionSetController@destroy');

    Route::get('/job-category','JobCategoryController@index');
    Route::get('/job-category/{id}','JobCategoryController@show');
    Route::post('/job-category','JobCategoryController@store');
    Route::patch('/job-category/{id}','JobCategoryController@update');
    Route::delete('/job-category/{id}','JobCategoryController@destroy');

    Route::get('/job-priority','JobPriorityController@index');
    Route::get('/job-priority/{id}','JobPriorityController@show');
    Route::post('/job-priority','JobPriorityController@store');
    Route::patch('/job-priority/{id}','JobPriorityController@update');
    Route::delete('/job-priority/{id}','JobPriorityController@destroy');

    Route::post('/job/{uuid}/config','JobController@configuration');
    Route::get('/job/pre-requisite','JobController@preRequisite');
    Route::post('/job/graph','JobController@graph');
    Route::get('/job','JobController@index');
    Route::get('/job/{uuid}','JobController@show');
    Route::post('/job','JobController@store');
    Route::patch('/job/{uuid}','JobController@update');
    Route::delete('/job/{uuid}','JobController@destroy');
    Route::post('/job/{uuid}/progress','JobController@updateProgress');
    Route::post('/job/{uuid}/star','JobController@toggleStar');
    Route::post('/job/{uuid}/archive','JobController@toggleArchive');
    Route::post('/job/{uuid}/rating','JobController@jobRating');
    Route::post('/job/{uuid}/answer','JobController@answer');
    Route::post('/job/{uuid}/rating/sub-job','JobController@subJobRating');
    Route::post('/job/rating/chart','JobController@ratingChart');

    Route::get('/report/job/summary','JobController@summary');

    Route::get('/job/{uuid}/sign-off','JobSignOffLogController@index');
    Route::post('/job/{uuid}/sign-off','JobSignOffLogController@store');
    Route::post('/job/{uuid}/sign-off-action','JobSignOffLogController@storeAction');

    Route::post('/job/{uuid}/copy','JobController@copy');

    Route::post('/job/{uuid}/recurrence','JobController@Recurrence');
    Route::get('/job/{uuid}/recurring','JobController@listRecurring');

    Route::get('/job/{uuid}/sub-job','SubJobController@index');
    Route::post('/job/{uuid}/sub-job','SubJobController@store');
    Route::get('/job/{uuid}/sub-job/{suuid}','SubJobController@show');
    Route::patch('/job/{uuid}/sub-job/{suuid}','SubJobController@update');
    Route::delete('/job/{uuid}/sub-job/{suuid}','SubJobController@destroy');
    Route::post('/job/{uuid}/sub-job/{suuid}/toggle-status','SubJobController@toggleStatus');

    Route::get('/job/{uuid}/comment','JobCommentController@index');
    Route::post('/job/{uuid}/comment','JobCommentController@store');
    Route::delete('/job/{uuid}/comment/{id}','JobCommentController@destroy');

    Route::get('/job/{uuid}/note','JobNoteController@index');
    Route::post('/job/{uuid}/note','JobNoteController@store');
    Route::get('/job/{uuid}/note/{suuid}','JobNoteController@show');
    Route::patch('/job/{uuid}/note/{suuid}','JobNoteController@update');
    Route::delete('/job/{uuid}/note/{suuid}','JobNoteController@destroy');

    Route::get('/job/{uuid}/attachment','JobAttachmentController@index');
    Route::post('/job/{uuid}/attachment','JobAttachmentController@store');
    Route::get('/job/{uuid}/attachment/{auuid}','JobAttachmentController@show');
    Route::patch('/job/{uuid}/attachment/{auuid}','JobAttachmentController@update');
    Route::delete('/job/{uuid}/attachment/{auuid}','JobAttachmentController@destroy');

    Route::get('/notification/fetch/inbox','NotificationController@fetchInbox');
    Route::get('/notification/{origin}/{id}/read', ['as' => 'api.notification.read', 'uses' => 'NotificationController@markAsRead']);    
    Route::get('/notification/{origin}/{id}/dismiss', ['as' => 'api.notification.dismiss', 'uses' => 'NotificationController@dismiss']);    

});




// APP

Route::group([
    'namespace' => 'Api'
], function () {
    
    Route::group([
        'prefix' => 'auth',
    ], function () {
        
        // Route::post('login', 'AuthController@login');
        // Route::post('signup', 'AuthController@signup');

        Route::post('firebase/login', 'AuthController@firebaseLogin');
        
        Route::group([
        'middleware' => 'auth:api'
        ], function() {

            Route::get('logout', 'AuthController@logout');
            Route::get('user', 'AuthController@user');
        
        });

    });


          
    Route::group([
        // 'middleware' => 'auth:api',
        'prefix'    => 'job',
    ], function() {

        Route::post('list', 'JobController@list');
    
    });

        
        
    Route::group([
        'prefix'    => 'test',
    ], function() {

        Route::get('notification-push', 'TestController@pushNotification');
        Route::get('notification-push-background', 'TestController@backgroundPushNotification');
        Route::get('notification-push-user', 'TestController@userPushNotification');
        Route::any('notification-negotiate-token', 'TestController@notificationNegotiateToken');
    
    });



});