<?php

Route::get('/', function() {
    return View::make('index');
});
// Route::controllers([
//     'auth' => 'Auth\AuthController',
//     'password' => 'Auth\PasswordController',
// ]);
$router->group(['prefix' => 'Api/v1'], function($router) {
    Route::get('logout', 'StudentController@logout');
    //Website
    //Route::resource('website', 'WebsiteController');
    //Source
    Route::resource('source', 'SourceController');
    //User
    Route::resource('user', 'UserController');
    Route::get('user', 'UserController@getVersionDetails');
    Route::resource('editUserDetails','UserController@editUserDetails');
    Route::resource('users', 'UserController');
    Route::resource('ckupload', 'UserController@getckupload');
    //Section
    Route::resource('section','SectionController');
    Route::resource('getSectionDetails','SectionController@getSectionDetails');
    Route::resource('getSubSection','SectionController@getSubSection');
    Route::resource('getSubSubSectionDetails','SectionController@getSubSubSectionDetails');
    Route::resource('addSubSection','SectionController@addSubSection');
    Route::resource('addSubSubSection','SectionController@addSubSubSection');
    Route::resource('deleteSubsection','SectionController@deleteSubsection');
    Route::resource('editSubSection','SectionController@editSubSection');
    Route::resource('deleteSubSubSection','SectionController@deleteSubSubSection');
    Route::resource('editSubSubSection','SectionController@editSubSubSection');
    //Role
    Route::resource('role','RoleController');
    Route::resource('getroles','RoleController@getRoles');
    Route::resource('adduser','RoleController@addUser');
    Route::resource('addRole','RoleController@addRole');
    Route::resource('getModules', 'RoleController@getModules');
    Route::resource('getallmodules', 'RoleController@getAllModuleForCheckRole');
    //Tag
    Route::resource('tag','TagsController');
    Route::resource('getTagDetails','TagsController@getTagDetails');
    Route::resource('getQuestionType','TagsController@getQuestionType');
    Route::resource('getTagsForViewQuestions','TagsController@getTagsForViewQuestions');
    Route::resource('getTagsForCopyQuestion','TagsController@getTagsForCopyQuestion');
    Route::resource('copyQuestionToAnotherTag','TagsController@copyQuestionToAnotherTag');
    Route::resource('getTypeFromTag','TagsController@getTypeFromTag');
    Route::resource('getTagNameFromTagId','TagsController@getTagNameFromTagId');
    Route::resource('getTagsCountForQuestion','TagsController@getTagsCountForQuestion');
    
    // download Query
    Route::resource('downloadquery','DownloadQueryController@downloadQuery');
    Route::resource('downloadIndividualQuery','DownloadQueryController@downloadIndividualQuery');
    Route::resource('getSqlFileName', 'DownloadQueryController@getSqlFileName');
    Route::resource('getSqlFileNameQueryMenu', 'DownloadQueryController@getSqlFileNameQueryMenu');
});