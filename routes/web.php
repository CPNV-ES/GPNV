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
    Route::get('logout', ['as' => 'logouit', 'uses' => 'SessionController@destroy']);

    Route::group(['middleware' => 'auth'], function(){

        Route::group(['middleware' => 'admin'], function(){
        /* ADMIN */
          Route::get('admin',['as' => 'admin','uses' => 'AdminController@show']);
          Route::get('admin/sync',['as' => 'admin.sync','uses' => 'AdminController@synchro']);
        });

        /* TASK */
        Route::resource('project.tasks', 'TaskController');

        Route::delete('deliverable/unlink/{checkList_id}/', ['as' => 'deliverable.unlink', 'uses' => 'CheckListController@unlink']);
/*
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
*/
        /* PROJECT */
        Route::resource('project','ProjectController');
        Route::get('/', ['as' => 'home', 'uses' => 'ProjectController@index' ]);
        Route::get('project/{id}', ['as' => 'project.show', 'uses' => 'ProjectController@show' ])->where('id', '[0-9]+');
        Route::post('project/{id}/tasks', 'ProjectController@storeTask')->where('id', '[0-9]+');
        Route::delete('project/{id}/users/{user}/destroy', 'ProjectController@destroyUser')->where('id', '[0-9]+');
        Route::post('project/{id}/target', ['as' => 'project.storetarget', 'uses' => 'ProjectController@storeTarget'])->where('projectid', '[0-9]+');
        Route::post('target/{target}/valide', ['as' => 'project.validetarget', 'uses' => 'ProjectController@valideTarget'])->where('target', '[0-9]+');
        Route::get('project/{id}/target', ['as' => 'project.gettarget', 'uses' => 'ProjectController@getTarget'])->where('id', '[0-9]+');
        Route::get('project/{id}/getTasks', ['as' => 'project.task.getTasks', 'uses' => 'TaskController@getTasks' ])->where('id', '[0-9]+');

        Route::post('project/{id}/editDescription', 'ProjectController@editDescription')->where('id', '[0-9]+');
        #Route::post('project/{id}/quitProject/', ['as' => 'project.quitProject', 'uses' => 'ProjectController@quitProject'])->where('id', '[0-9]+');
        Route::post('project/{id}/removeFromProject/{user}',  ['as' => 'memberships.quitProject', 'uses' => 'MembershipController@removeUserFromProject'])->where('id', '[0-9]+');

        /*-----------------------------Routes CheckList --------------------------*/
        Route::post('project/{id}/checklist/{CheckListId}/create','CheckListController@store');
        /*--------------------------------------------------------------------*/

        /*----------------------Routes scenario-------------------------------*/
        Route::get('project/{id}/scenario/{scenarionId}', ['as' => 'scenario.show', 'uses' => 'ScenarioController@show']);
        Route::get('project/{id}/deleteScenario/{scenarioId}',['as' => 'scenario.delete', 'uses' => 'ScenarioController@delete']);
        Route::get('project/{id}/checkListItem/{itemId}/scenario/create','ScenarioController@addItem');
        Route::post('project/{id}/checkListItem/{itemId}/scenario/create','ScenarioController@store');
        Route::put('project/{id}/scenario/{scenarioId}',['as'=>'scenario.modify', 'uses' => 'ScenarioController@update']);

        Route::post('project/{id}/scenario/{scenarioId}/uploadMaquete', ['as' => 'scenario.uploadMaquete', 'uses' => 'ScenarioController@uploadMaquete']);
        Route::delete('project/{id}/scenario/{scenarioId}/delMaquete', ['as' => 'scenario.delMaquete', 'uses' => 'ScenarioController@delMaquete']);

        // Route::post('project/{id}/scenario/{scenarioId}/create',['as'=>'scenario.create.item', 'uses' => 'ScenarioController@addStep']);
        // Route::put('project/{id}/scenario/{scenarioId}/item/{itemId}',['as'=>'scenario.item.modify', 'uses' => 'ScenarioController@updateStep']);
        // Route::get('project/{id}/scenario/{stepId}/delete',['as'=>'scenario.del.item', 'uses' => 'ScenarioController@delStep']);
        //  Route::put('project/{id}/scenario/{scenarioId}/changeMaquete', ['as' => 'scenario.changeMaquete', 'uses' => 'ScenarioController@changeMaquete']);
        /*--------------------- Routes objectifs -----------------------------*/
        Route::get('project/{id}/objective/{itemId}','ObjectiveController@showItem');
        Route::put('project/{id}/objective/{itemId}', 'ObjectiveController@update');

        /* SCENARIOSTEP */
        //Route::resource('scenario_steps', 'ScenarioStepController');
        Route::post('project/{id}/scenario/{scenarioId}/create', ['as'=>'scenario_steps.create', 'uses' => 'ScenarioStepController@create']);
        Route::post('project/{id}/scenario/{scenarioId}/item/{itemId}', ['as'=>'scenario_steps.modify', 'uses' => 'ScenarioStepController@update']);
        Route::get('project/{id}/scenario/{stepId}/delete',['as'=>'scenario_steps.destroy', 'uses' => 'ScenarioStepController@destroy']);
        Route::put('project/{id}/scenario/{scenarioId}/changeMaquete', ['as' => 'scenario_steps.changeMaquete', 'uses' => 'ScenarioStepController@changeMaquete']);


       /* MOCKUP */
       //  Route::get('project/{id}/mockups/', ['as' => 'mockups.show', 'uses' => 'MockupController@show'])->where('id', '[0-9]+');

        /* PROJECT HOME (accueil)*/
        Route::get('project/{id}/project', ['as' => 'projectdata.show', 'uses' => 'ProjectController@show'])->where('id', '[0-9]+');


        /* FILES */
        Route::resource('project.files','FileController')->except(['update', 'edit']) ;

        /* Link */
        Route::get('project/{id}/link/{check}', ['as' => 'deliverable.getToLink', 'uses' => 'DeliverableController@getToLink']);
        Route::post('project/{id}/link/{check}', ['as' => 'deliverable.link', 'uses' => 'DeliverableController@LinkTo']);

        /* APP */
        Route::get('logout', ['as' => 'logout','uses' => 'SessionController@destroy']);

        /* Add User */
        Route::get('project/{id}/memberships/', ['as' => 'memberships.show', 'uses' => 'MembershipController@show'])->where('id', '[0-9]+');
        Route::get('project/{id}/memberships/getStudents/', ['as' => 'memberships.getStudents', 'uses' => 'MembershipController@getStudents'])->where('id', '[0-9]+');
        Route::get('project/{id}/memberships/getTeachers/', ['as' => 'memberships.getTeachers', 'uses' => 'MembershipController@getTeachers'])->where('id', '[0-9]+');
        Route::post('project/{id}/memberships/', ['as' => 'memberships.add', 'uses' => 'MembershipController@addUsers'])->where('id', '[0-9]+');

        /* USER */
        Route::resource('user', 'UserController');
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
        Route::resource('project.events', 'EventController');
        Route::get('project/{id}/formEvents', ['as' => 'project.formEvents', 'uses' => 'EventController@formEvent'])->where('id', '[0-9]+');
        Route::post('project/{id}/event/validation', ['as' => 'project.storeEventsValidation', 'uses' => 'EventController@storeValidation'])->where('id', '[0-9]+');

        /* RELOAD ROUTES */
        Route::get('project/{id}/deliverables', ['as' => 'deliverable.show', 'uses' => 'DeliverableController@show']);
        Route::get('project/{id}/objectives', ['as' => 'objective.show', 'uses' => 'ObjectiveController@show']);

        /* DELETE ROUTE*/
        Route::delete('project/{id}/deliverable/{deliveryId}', ['as' => 'deliverables.delete', 'uses' => 'DeliverableController@delete']);
        Route::delete('project/{id}/objective/{objectiveId}', ['as' => 'objective.delete', 'uses' => 'ObjectiveController@delete']);

        Route::post('tasktype', ['as' => 'taskType.store', 'uses' => 'TaskTypesController@store']);
    });
});
