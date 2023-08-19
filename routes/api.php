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

Route::group(['namespace' => 'v1', 'as' => 'v1.', 'prefix' => 'v1'], function () {

    Route::middleware('auth:api')->group(function () {
    });

    /** listener routes */
    Route::name('station.')->group(function () {
        Route::get('getAllStations', 'StationController@getAllStations')->name('getAllStations');
        Route::get('getAllPublicStations', 'StationController@getAllPublicStations')->name('getAllPublicStations');
        Route::get('getStationDetail', 'StationController@getStationDetail')->name('getStationDetail');
    });

    Route::name('programme.')->group(function () {
        Route::get('getAllProgrammes', 'ProgrammeController@getAllProgrammes')->name('getAllProgrammes');
        Route::get('getAllProgrammes/{station}/type/{type}', 'ProgrammeController@getAllProgrammesByStation')->name('getAllProgrammesByStation');
        Route::get('getProgrammeDetail', 'ProgrammeController@getProgrammeDetail')->name('getProgrammeDetail');
        Route::get('getProgrammeDetail', 'ProgrammeController@getProgrammeDetail')->name('getProgrammeDetail');
        Route::get('programmeSearch', 'ProgrammeController@search')->name('search');
    });

    Route::name('volunteer.')->group(function () {
        Route::post('loginVolunteer', 'VolunteerController@login')->name('login');
        Route::post('requestVolunteer', 'VolunteerController@request')->name('request');
        // Protected with APIToken Middleware
        Route::middleware('APIToken')->group(function () {
            Route::post('updateVolunteer', 'VolunteerController@update')->name('update');
            Route::post('logoutVolunteer', 'VolunteerController@logout')->name('logout');
        });
    });

    Route::name('content.')->group(function () {
        Route::post('postContentFile', 'ContentController@postContentFile')->name('postContentFile');
    });

     /** Editor routes */
    Route::name('audio.')->middleware('APIToken')->group(function () {
        Route::get('getUploadedRecord', 'AudioController@getUploadedRecord')->name('getUploadedRecord');
        Route::post('postRecordFile', 'AudioController@postRecordFile')->name('postRecordFile');
    });

     /** About routes */
     Route::name('about.')->group(function () {
        Route::get('getAbout', 'AboutController@about')->name('get');
    });

    /* Force update route */
    Route::get('getLatestVersion', 'AndriodVersionController@getLatestVersion')->name('getLatestVersion');

     /** Editor routes */
    Route::name('document.')->middleware('APIToken')->group(function () {
        Route::get('getAllDocuments', 'DocumentController@getAllDocuments')->name('getUploadedRecord');
        Route::post('postDocumentFile', 'DocumentController@postDocumentFile')->name('postRecordFile');
    });

    /** Contact methods */
    Route::get('contactMethods', 'AboutController@getContactMethods')->name('getContactMethods');
});

require(app_path(). '/ApiRoutes/ApiV2.php');