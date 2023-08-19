<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v2\NewsFeedController;


Route::group(['namespace' => 'v2', 'as' => 'v2.', 'prefix' => 'v2'], function () {

    Route::name('feed')->group(function() {
        Route::get('feed/all', 'NewsFeedController@index')->name('all');
        Route::get('feed/video', 'NewsFeedController@getVideoProgrammes')->name('video');
        Route::get('feed/photo', 'NewsFeedController@getPhotoProgrammes')->name('photo');
        Route::get('feed/audio', 'NewsFeedController@getAudioProgrammes')->name('audio');
        Route::get('feed/{programme}/detail', 'NewsFeedController@programmeDetail')->name('detail');
        Route::get('feed/{feed_id}/react', 'NewsFeedController@programmeReact')->name('react');
        Route::get('feed/search', 'NewsFeedController@search')->name('search');
    });
});