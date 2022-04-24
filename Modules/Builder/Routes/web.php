<?php

Route::group(['prefix'=>'admin','as'=>'admin.'], function () {
    Route::resource('node', 'NodeController');

    Route::get('node/base-file-builder/{nodeId}', [
        'uses'=>'NodeController@baseFileBuilder',
        'as'=>'node.baseFileBuilder',
    ]);

    Route::get('node/curd-builder/{nodeId}', [
        'uses'=>'NodeController@curdBuilder',
        'as'=>'node.curdBuilder',
    ]);

    Route::get('node/fetch-table/{nodeId}', [
        'uses'=>'NodeController@getFetchTable',
        'as'=>'node.getFetchTable',
    ]);

    Route::post('node/fetch-table/{nodeId}', [
        'uses'=>'NodeController@postFetchTable',
        'as'=>'node.postFetchTable',
    ]);

    Route::post('node/curd-builder/{nodeId}/create', [
        'uses'=>'NodeController@curdCreate',
        'as'=>'node.curdCreate'
    ]);
});
