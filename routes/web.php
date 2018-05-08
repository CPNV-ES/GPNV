<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'web'], function () {

    //,['as' => '','uses' => '']

    /*Route::get('login',['as' => 'login','uses' => 'SessionController@create'] );
    Route::post('login', 'SessionController@store');*/

    //Route::get('login', view('auth.nologin'));

    //Route::get('test', 'Welcome@Test');
    Route::get('saml2/error',['as' => 'saml_error','uses' => 'SAMLController@error']);
    Route::get('login',['as' => 'login','uses' => 'SessionController@nologin']);

    Route::group(['middleware' => 'auth'], function(){

        Route::group(['middleware' => 'admin'], function(){
        /* ADMIN */
          Route::get('admin',['as' => 'admin','uses' => 'AdminController@show']);
          Route::get('admin/sync',['as' => 'admin.sync','uses' => 'AdminController@synchro']);
        });

        /* TASK */
        Route::resource('tasks', 'TaskController',
            ['parameters' =>
                ['tasks' => 'task']
            ]
        );

        Route::delete('delivery/unlink/{checkList_id}/', ['as' => 'delivery.unlink', 'uses' => 'CheckListController@unlink']);

        Route::get('tasks/{task}/',['as' => 'tasks.show','uses' => 'TaskController@show'])->where('task', '[0-9]+');
        Route::get('tasks/{task}/children/create', ['as' => 'tasks.createChildren','uses' => 'TaskController@createChildren'])->where('task', '[0-9]+');
        Route::post('tasks/{task}/children/', ['as' => 'tasks.storeChildren','uses' => 'TaskController@storeChildren'])->where('task', '[0-9]+');
        Route::post('tasks/{task}/play', ['as' => 'tasks.play', 'uses' => 'TaskController@play'])->where('task', '[0-9]+');
        Route::post('tasks/{task}/status', ['as' => 'tasks.status', 'uses' => 'TaskController@status'])->where('task', '[0-9]+');
        Route::get('tasks/{task}/users/', ['as' => 'tasks.users', 'uses' => 'TaskController@users'])->where('task', '[0-9]+');
        Route::post('tasks/{task}/users/', ['as' => 'tasks.storeUsers', 'uses' => 'TaskController@storeUsers'])->where('task', '[0-9]+');
        Route::delete('tasks/{usersTask}/users/', ['as' => 'tasks.userTaskDelete', 'uses' => 'TaskController@userTaskDelete'])->where('usersTask', '[0-9]+');
        Route::post('tasks/{durationsTask}/stop', ['as' => 'tasks.stop', 'uses' => 'TaskController@stop'])->where('durationsTask', '[0-9]+');
        Route::post('tasks/{task}', 'TaskController@store')->where('task', '[0-9]+');

        /* PROJECT */
        Route::resource('project','ProjectController',
            ['parameters' => ['project' => 'id']],
            ['only' => ['index']]
        );
        Route::get('/', ['as' => 'home', 'uses' => 'ProjectController@index' ]);
        Route::get('project/{id}', ['as' => 'project.show', 'uses' => 'ProjectController@show' ])->where('id', '[0-9]+');
        Route::get('project/{id}/tasks/create', 'ProjectController@createTask')->where('id', '[0-9]+');
        Route::post('project/{id}/tasks', 'ProjectController@storeTask')->where('id', '[0-9]+');
        Route::get('project/{id}/files', ['as' => 'files.show', 'uses' => 'ProjectController@files']);
        Route::delete('project/{id}/users/{user}/destroy', 'ProjectController@destroyUser')->where('id', '[0-9]+');
        Route::post('project/{id}/target', ['as' => 'project.storetarget', 'uses' => 'ProjectController@storeTarget'])->where('projectid', '[0-9]+');
        Route::post('target/{target}/valide', ['as' => 'project.validetarget', 'uses' => 'ProjectController@valideTarget'])->where('target', '[0-9]+');
        Route::get('project/{id}/target', ['as' => 'project.gettarget', 'uses' => 'ProjectController@getTarget'])->where('id', '[0-9]+');
        Route::get('project/{id}/getTasks', ['as' => 'project.getTasks', 'uses' => 'ProjectController@getTasks' ])->where('id', '[0-9]+');

        Route::post('project/{id}/editDescription', 'ProjectController@editDescription')->where('id', '[0-9]+');
        #Route::post('project/{id}/quitProject/', ['as' => 'project.quitProject', 'uses' => 'ProjectController@quitProject'])->where('id', '[0-9]+');
        Route::post('project/{id}/removeFromProject/{user}', 'ProjectController@removeUserFromProject')->where('id', '[0-9]+');

        /*-----------------------------Routes CheckList --------------------------*/
        Route::get('project/{id}/checkListItem/{itemId}','CheckListController@showItem');
        Route::get('project/{id}/checklist/{CheckListId}/create','ProjectController@createCheckListItem');
        Route::put('project/{id}/id/{CheckListId}','CheckListController@update');
        Route::post('project/{id}/checklist/{CheckListId}/create','CheckListController@store');
        /*--------------------------------------------------------------------*/

        /*----------------------Routes scenario-------------------------------*/
        Route::get('project/{id}/scenario/{scenarionId}', ['as' => 'scenario.show', 'uses' => 'ScenarioController@show']);
        Route::get('project/{id}/deleteScenario/{scenarioId}',['as' => 'scenario.delete', 'uses' => 'ScenarioController@delete']);
        Route::get('project/{id}/checkListItem/{itemId}/scenario/create','ScenarioController@addItem');
        Route::post('project/{id}/checkListItem/{itemId}/scenario/create','ScenarioController@store');
        Route::post('project/{id}/scenario/{scenarioId}/create',['as'=>'scenario.create.item', 'uses' => 'ScenarioController@addStep']);
        Route::put('project/{id}/scenario/{scenarioId}/item/{itemId}',['as'=>'scenario.item.modify', 'uses' => 'ScenarioController@updateStep']);
        Route::put('project/{id}/scenario/{scenarioId}',['as'=>'scenario.modify', 'uses' => 'ScenarioController@update']);
        Route::get('project/{id}/scenario/{stepId}/delete',['as'=>'scenario.del.item', 'uses' => 'ScenarioController@delStep']);
        Route::post('project/{id}/scenario/{scenarioId}/uploadMaquete', ['as' => 'scenario.uploadMaquete', 'uses' => 'ScenarioController@uploadMaquete']);
        Route::put('project/{id}/scenario/{scenarioId}/changeMaquete', ['as' => 'scenario.changeMaquete', 'uses' => 'ScenarioController@changeMaquete']);
        Route::delete('project/{id}/scenario/{scenarioId}/delMaquete', ['as' => 'scenario.delMaquete', 'uses' => 'ScenarioController@delMaquete']);
        /*--------------------- Routes objectifs -----------------------------*/

        /* FILES */
        Route::post('project/{id}/file', ['as' => 'files.store', 'uses' => 'FileController@store']);
        //Route::get('project/{id}/file', ['as' => 'files.show', 'uses' => 'FileController@show']);
        Route::delete('project/{id}/file/{file}', ['as' => 'files.destroy', 'uses' => 'FileController@destroy']);
        Route::post('project/{id}/file', ['as' => 'files.store', 'uses' => 'FileController@store'])->where('id', '[0-9]+');
        Route::get('project/{id}/files', ['as' => 'files.show', 'uses' => 'FileController@show'])->where('id', '[0-9]+');

        Route::get('project/{id}/link/{check}', ['as' => 'deliveries.getToLink', 'uses' => 'ProjectController@getToLink']);
        Route::post('project/{id}/link', ['as' => 'deliveries.link', 'uses' => 'ProjectController@LinkToDelivery']);

        /* APP */
        Route::get('logout', ['as' => 'logout','uses' => 'SessionController@destroy']);

        /* Add User */
        Route::get('project/{id}/getStudents/', 'ProjectController@getStudents')->where('id', '[0-9]+');
        Route::get('project/{id}/getTeachers/', 'ProjectController@getTeachers')->where('id', '[0-9]+');
        Route::post('project/{id}/addUsers/', ['as' => 'project.addUsers', 'uses' => 'ProjectController@addUsers'])->where('id', '[0-9]+');

        /* USER */
        Route::get('user/{user}', ['as'=> 'user.show','uses'=>'UserController@show'])->where('user', '[0-9]+');
        Route::post('user/{user}/avatar',['as'=> 'user.avatar','uses'=>'UserController@storeAvatar']);

        /* PLANNING */
        Route::get('project/{projectid}/planning', 'PlanningController@show')->where('projectid', '[0-9]+');

        /* COMMENTS */
        Route::get('tasks/{task}/comment',['as' => 'comment.show','uses' => 'CommentController@show'])->where('comment', '[0-9]+');
        Route::post('tasks/{task}/comment', ['as' => 'comment.store', 'uses' => 'CommentController@store']) -> where('comment', '[0-9]+');

        /* SEARCH */
        Route::get('project/{id}/search', ['as' => 'search.show', 'uses' => 'SearchController@show']);
        Route::post('project/{id}/search', ['as' => 'search.store', 'uses' => 'SearchController@store']);

        /* EVENTS */
        Route::get('project/{id}/events', ['as' => 'project.events', 'uses' => 'EventController@show'])->where('id', '[0-9]+');
        Route::get('project/{id}/formEvents', ['as' => 'project.formEvents', 'uses' => 'EventController@formEvent'])->where('id', '[0-9]+');
        Route::post('project/{id}/events', ['as' => 'project.storeEvents', 'uses' => 'EventController@store'])->where('id', '[0-9]+');
        Route::post('project/{id}/events/validation', ['as' => 'project.storeEventsValidation', 'uses' => 'EventController@storeValidation'])->where('id', '[0-9]+');

        Route::get('test','Welcome@test');
        Route::get('user/{search}',array('as' => 'name', 'uses' => 'UserController@search'));

        /* RELOAD ROUTES */
        Route::get('project/{id}/deliveries', ['as' => 'project.showDeliveries', 'uses' => 'ProjectController@showDeliveries']);
        Route::get('project/{id}/objectives', ['as' => 'objective.show', 'uses' => 'ObjectiveController@show']);

        /* DELETE ROUTE*/
        Route::delete('project/{id}/delivery/{deliveryId}', ['as' => 'project.deleteDelivery', 'uses' => 'ProjectController@deleteDelivery']);
        Route::delete('project/{id}/objective/{objectiveId}', ['as' => 'objective.delete', 'uses' => 'ObjectiveController@delete']);

        Route::post('tasktype', ['as' => 'taskType.store', 'uses' => 'TaskTypesController@store']);
    });
});
