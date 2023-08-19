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

Route::group(['namespace' => 'Web'], function() {
    Route::get('privacy-policy', 'CustomLinkController@privacyPolicy')->name('privacy-policy');
    Route::get('application-download', 'CustomLinkController@appDownload')->name('application-download');
});

Route::group(['namespace' => 'Admin'], function() {
    Route::redirect('/', 'programmes');

    Auth::routes();

    Route::name('admin.')->group(function () {
        Route::group(['middleware' => 'auth'], function () {
            /** Home */
            // Route::get('/home', 'HomeController@index')->name('home');
            Route::redirect('/home', 'programmes')->name('home');

            Route::group(['middleware' => 'checkPermission'], function () {
                /** User */
                Route::name('users.')->group(function () {
                    Route::put('users/{id}/restore', 'UserController@restore')->name('restore');
                    Route::put('users/{id}/forceDelete', 'UserController@forceDelete')->name('forceDelete');
                    Route::get('users/about', 'UserController@aboutGet')->name('aboutGet');
                    Route::get('users/aboutStore', 'UserController@aboutStore')->name('aboutStore');
                });
                Route::resource('users', 'UserController');

                /** Station */
                Route::name('stations.')->group(function () {
                    Route::put('stations/{id}/restore', 'StationController@restore')->name('restore');
                    Route::get('stations/trash/', 'StationController@trash')->name('trash');
                    Route::put('stations/{id}/forceDelete', 'StationController@forceDelete')->name('forceDelete');
                });
                Route::resource('stations', 'StationController');

                /** Station Manager */
                Route::name('stationManagers.')->group(function () {
                    Route::put('stationManagers/{id}/restore', 'StationManagerController@restore')->name('restore');
                    Route::put('stationManagers/{id}/forceDelete', 'StationManagerController@forceDelete')->name('forceDelete');
                });
                Route::resource('stationManagers', 'StationManagerController');

                /** Volunteer */
                Route::name('volunteers.')->group(function () {
                    Route::put('volunteers/{id}/restore', 'VolunteerController@restore')->name('restore');
                    Route::get('getVolunteersByStation', 'VolunteerController@getVolunteersByStation')->name('getVolunteersByStation');
                    Route::put('volunteers/{id}/forceDelete', 'VolunteerController@forceDelete')->name('forceDelete');
                    Route::post('volunteers/deleteSelected', 'VolunteerController@deleteSelected')->name('deleteSelected');
                });
                Route::resource('volunteers', 'VolunteerController');

                /** Audio */
                Route::name('audios.')->group(function () {
                    Route::put('audios/{id}/restore', 'AudioController@restore')->name('restore');
                    Route::post('audios/{id}/share', 'AudioController@share')->name('share');
                    Route::post('audios/{id}/private', 'AudioController@private')->name('private');
                    Route::put('audios/{id}/forceDelete', 'AudioController@forceDelete')->name('forceDelete');
                    Route::get('audios/{id}/image', 'AudioController@imageDownload')->name('image');
                    Route::get('audios/{id}/audio', 'AudioController@audioDownload')->name('audio');
                    Route::post('audios/deleteSelected', 'AudioController@deleteSelected')->name('deleteSelected');
                });

                Route::resource('audios', 'AudioController');

                /** Programme */
                Route::name('programmes.')->group(function () {
                    Route::put('programmes/{id}/restore', 'ProgrammeController@restore')->name('restore');
                    Route::put('programmes/{id}/approve', 'ProgrammeController@approve')->name('approve');
                    Route::put('programmes/{id}/reject', 'ProgrammeController@reject')->name('reject');
                    Route::put('programmes/{id}/forceDelete', 'ProgrammeController@forceDelete')->name('forceDelete');
                    Route::post('programmes/deleteSelected', 'ProgrammeController@deleteSelected')->name('deleteSelected');
                });
                Route::resource('programmes', 'ProgrammeController');

                Route::resource('shares', 'ShareController');

                /** Documents */
                Route::name('documents.')->group(function () {
                    Route::put('documents/{id}/restore', 'DocumentController@restore')->name('restore');
                    Route::put('documents/{id}/forceDelete', 'DocumentController@forceDelete')->name('forceDelete');
                    Route::post('documents/deleteSelected', 'DocumentController@deleteSelected')->name('deleteSelected');
                });

                Route::resource('documents', 'DocumentController');

                /** Contents */
                Route::name('contents.')->group(function () {
                    Route::put('contents/{id}/restore', 'ContentController@restore')->name('restore');
                    Route::post('contents/{id}/share', 'ContentController@share')->name('share');
                    Route::post('contents/{id}/private', 'ContentController@private')->name('private');
                    Route::put('contents/{id}/forceDelete', 'ContentController@forceDelete')->name('forceDelete');
                    Route::get('contents/{id}/file', 'ContentController@contentDownload')->name('file');
                    Route::get('contents/{id}/meta', 'ContentController@metaDownload')->name('meta');
                    Route::post('contents/deleteSelected', 'ContentController@deleteSelected')->name('deleteSelected');
                });

                Route::resource('contents', 'ContentController');

                Route::resource('andriodVersions', 'AndriodVersionController');
            });
        });
    });
});
