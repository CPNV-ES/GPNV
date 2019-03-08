<?php

namespace App\Http\Controllers;

use App\Models\DurationsTask;
use App\Models\UsersTask;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use DateTime;
use App\Models\User;
use DB;
use App\Models\Project;
use App\Models\Event;
use App\Models\Status;
use App\Models\CheckList;
use App\Http\Requests;

class TaskController extends Controller
{
    /**
     * Return the view task
     * @param Project $project
     * @return view to see whole project
     */
    function index(Project $project)
    {
        $currentUser = Auth::user();
        $userTasks = UsersTask::where("user_id", "=", $currentUser->id)->get();
        $duration = null;
        $task = null;
        $request="";
        foreach ($userTasks as $userstask) {
            foreach ($userstask->durationsTasks()->get() as $durationtask) {
                if ($durationtask->ended_at == null) {
                    $duration = $durationtask->id;
                    $task = $userstask->task_id;
                }
            }
        }

        /* Created By Fabio Marques
          Description: create a new checkListObject
        */
        $livrables = new CheckList('Project', $project->id, 'Livrables');
        /* Created By Fabio Marques
          Description: create a new objectifs checkList
        */
        $objectifs = new CheckList('Project', $project->id, 'Objectifs', 'project/scenario');

        /* Created By Raphaël B.
          Description: log book event handling
        */
        $events = Event::where('project_id', '=', $project->id)
            ->orderBy('created_at', 'desc')->get();

        $projectMembers = $project->users->sortBy('id');

        $badgeCount = 0;

        // Array containing lists of users that have validated events
        $validations = array();

        foreach ($events as $event) {
            // Holds ids of users that have validated the event
            $users = array();
            foreach ($projectMembers as $member) {
                $exists = AcknowledgedEvent::where([
                    ['user_id', '=', $member->id],
                    ['event_id', '=', $event->id],
                ])->exists();

                if($exists) {
                    $users[] = $member->id;
                }
            }

            $validations[$event->id] = $users;

            // Incrementing badgeCount unless the current user validated the event
            if (!in_array($currentUser->id, $users)) {
                $badgeCount++;
            }
        }

        return view('task.index', [
            'project' => $project,
            'livrables'=>$livrables,
            'objectifs'=>$objectifs,
            'duration' => $duration,
            'taskactive' => $task,
            'currentUser' => $currentUser,
            'members' => $projectMembers,
            'events' => $events,
            'validations' => $validations,
            'badgeCount' => $badgeCount
        ]);
    }

    /**
    * Return the view about the creation a children task
    * @param $task The task object
    * @return view to create a children task
    */
    function createChildren(Task $task){
        return view('task.createChildren', ['task' => $task]);
    }

    /**
    * Return the view about the creation a root task
    * @param $task The task object
    * @return view to create a root task
    */
    function create(Task $task, Request $request)  {
        return view('task.create', ['task' => $task]);
    }

    /**
    * Create a new task
    * @param $task The task object
    * @param $request Define the request data send by POST
    */
    function storeChildren(Task $task, Request $request){
        $newTask = new Task;
        $newTask->name = $request->input('name');
        $newTask->duration = $request->input('duration');
        $newTask->project_id = $request->input('project_id');
        $newTask->parent_id = $request->input('parent_id');
        $newTask->status_id = $request->input('status'); // hardcoded until the UI allows user friendly status changes
        $transactionResult = $newTask->save(); // Indicates whether or not the save was successfull

        //modified By: Fabio Marques
        $parentTask = Task::find($request->input('parent_id'));
        foreach ($parentTask->usersTasks as  $usertask) {
          $usertask->delete();
        }

        // return redirect()->route("project.show", ['id'=>$task->project_id]);
        // return json_encode($transactionResult);
    }

    /**
    * Delete a task
    * @param $task The task object
    */
    function destroy(Task $task){
        //Modified By: Fabio Marques
        foreach ($task->usersTasks as  $usertask) {
          $usertask->delete();
        }
        $transactionResult = $task->delete();

        $projectId = $task->project_id;

        (new EventController())->logEvent($projectId, "Suppression de la tâche \"" . $task->name . "\""); // Create an event

        // return ("destroy" . $task);
        //return json_encode($transactionResult);
    }

    /**
    * Return the view about the edition
    * @param $task The task object
    * @return view to edit a task
    */
    function edit(Task $task, Request $request){
        $taskType = DB::table('taskTypes')->orderBy('name')->get();
        $actualTaskType = DB::table('taskTypes')->where('id',$task->type_id)->first();
        return view('task.edit', ['task' => $task, "taskTypes" => $taskType, "actualTaskType" => $actualTaskType, 'statuses' => Status::all()]);
    }

    /**
    * Use to update or store a task
    * @param $task The task object
    * @param $request Define the request data send by POST
    */
    function store(Task $task, Request $request){
        $transactionResult = $task->update([
            'name' => $request->input('name'),
            'duration' => $request->input('duration'),
            'parent_id' => $request->input('parent_id') == '' ? null : $request->input('parent_id'),
            'status' => $request->input('status'),
            'type_id' => $request->input('taskTypes'),
            'status_id' => $request->input('status')
        ]);

        //(new EventController())->store($request->input('project_id'), "Créer une tâche enfant"); // Create an event

        // return redirect()->route("project.show", ['id'=>$task->project_id]);
        // return json_encode($transactionResult);
    }

    /**
    * Start a task
    * @param $request Define the request data send by POST
    * @return duration item id
    */
    public function play(Request $request){
        $durationTask = new DurationsTask;
        $durationTask->user_task_id = $request->task;

        $user = Auth::user();
        if (!$user->getActiveTask()->isEmpty()) {
            return "";
        }

        $durationTask->save();
        return $durationTask->id;
    }

    /**
    * Stop a task
    * @param $durationsTask DurationTask Item
    */
    public function stop(DurationsTask $durationsTask){
        $now = new DateTime(); // Add the current time in a variable $now

        // Update the duration with the current time
        $durationsTask->update([
            'ended_at' => $now,
        ]);
    }

    /**
    * Display the users with a common task
    * @param $task The task object
    * @param $request Define the request data send by POST
    * @return return view with users
    */
    public function users(Task $task, Request $request){

        $usersTasks = $task->usersTasks;
        $refuse = [];
        foreach($task->project->users as $user){
            foreach($task->usersTasks as $usertask){
                if($usertask->user_id == $user->id){
                    $refuse[] = $usertask->user_id;
                }else{
                }
            }
        }

        return view('task.users', ['task' => $task,'userstask' => $usersTasks, 'project' => $task->project, 'refuse' => $refuse]);
    }

    /**
    * Add one or more users for a task
    * @param $task The task object
    * @param $request Define the request data send by POST
    * @return return task
    */
    public function storeUsers(Task $task, Request $request){

        foreach($request->input('user') as $key => $value){
            $newUserTask = new UsersTask();
            $newUserTask->task_id = $request->task->id;
            $newUserTask->user_id = $key;
            $transactionResult = $newUserTask->save();
        }

        // return redirect()->route("project.show", ['id'=>$task->project_id]);
        return json_encode($transactionResult);
    }

    /**
    * Delete a user of task
    * @param $usersTask user of a task
    * @param $request Define the request data send by POST
    */
    public function userTaskDelete(UsersTask $usersTask, Request $request){
        $taskName = $usersTask->task->name;
        $taskUser = $usersTask->user->firstname . " " . $usersTask->user->lastname;
        $usersTask->delete();

        $projectId = $request->projectId;
        $description = "Abandon de la tâche \"" . $taskName . "\" par: " . $taskUser . ". Raison: " . $request->comment;

        (new EventController())->logEvent($projectId, $description);
    }


    /**
    * Verify the validity of task
    * @param $task The task object
    * @param $request Define the request data send by POST
    */
    public function status(Task $task, Request $request){

        if(!$task->ifChildTaskNoValidate()){ // Return a error message

            dd("La tâche ne peut pas être validée");

        }else{ // Return a message

            $task->update([
                'status' => 'validate',
            ]);

            dd("La tâche peut être validée");

        }
    }

}
