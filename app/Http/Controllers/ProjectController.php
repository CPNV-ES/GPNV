<?php

namespace App\Http\Controllers;

use App\Models\Memberships;
use App\Models\UsersTask;
use App\Models\Project;
use App\Models\Comment;
use App\Models\User;
use App\Models\Task;
use App\Models\Event;
use App\Models\Target;
use App\Models\CheckList;
use App\Models\AcknowledgedEvent;
use App\Models\StudentClass;

use App\Http\Requests;
use App\Http\Middleware\ProjectControl;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Form;
use Datetime;
use DB;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('ProjectControl', ['except' => [
            'index', 'create', 'store', 'valideTarget'
        ]]);
    }

    /**
    * Define if user can access the project, redirect to projects list if not
    * @return view to all projects
    */
    public function index()
    {
        // If the user has a role like "Eleve", he can access student view and he only can see his projects
        if (Auth::user()->role->name == "Eleve") {

            $projects = Auth::user()->projects()->orderBy('startDate', 'DESC')->get();

            return view('index', ['projects' => $projects]);

        }
        // If the user has a role like "Prof", he can access teacher view ans he can see all projects
        elseif(Auth::user()->role->name == "Prof"){

            #$projects = Project::all();
            $projects = Project::orderBy('startDate', 'DESC')->get();

            return view('index', ['projects' => $projects]);
        }
    }

    /**
    * Display all informations like the user's tasks connected, all project tasks, and so on
    * @param $projectID The project id
    * @return view to see whole project
    */
    public function show($id){
        $project = Project::find($id);
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
        $livrables = new CheckList('Project', $id, 'Livrables');
        /* Created By Fabio Marques
          Description: create a new objectifs checkList
        */
        $objectifs = new CheckList('Project', $id, 'Objectifs', 'project/scenario');

        /* Created By Raphaël B.
          Description: log book event handling
        */
        $events = Event::where('project_id', '=', $id)
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

        return view('project/project', [
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
    * Return the view to see files
    * @param $id The project id
    * @return view to see files
    */
    public function files($id){
      $project = Project::find($id);
      return view('project/file', ['project' => $project]);
    }

    /**
    * Return the view to editing projects
    * @return view to editing projects
    */
    public function edit(){
        return view('project/edit');
    }

    /**
    * Return the view about tasks
    * @return view of task
    */
    public function task(){
        return view('project/task');
    }

    /**
    * Return the view to creating projects
    * @return view of project creation
    */
    public function create(){
        return view('project/edition/create');
    }

    /**
    * Create a task
    * @param $request Define the request data send by POST
    * @return view of project
    */
    public function store(Request $request){
        $Date = explode("/", $request->input('date'));
        $Date = $Date[2]."/".$Date[1]."/".$Date[0];
        $DateTime = $Date." ".$request->input('hour');

        $newProject = new Project;
        $relation = new Memberships;
        $newProject->name = $request->input('name');
        $newProject->description = $request->input('description');
        $newProject->startDate = $DateTime;
        $newProject->save();

        $relation->project_id = $newProject->id;
        $relation->user_id = Auth::user()->id;
        $relation->save();

        /*
          Created By: Fabio Marques
          Decription: create a new checkList for the new project
        */
        CheckList::newCheckList('Project',$newProject->id,'Livrables');
        /*
          Created By: Fabio Marques
          Description: Create a new checkList of objectifs to the project
        */
        CheckList::newCheckList('Project', $newProject->id, 'Objectifs', 'project/scenario');
        $objectifs = new CheckList('Project', $newProject->id, 'Objectifs', 'project/scenario');
        CheckList::newItem($objectifs->getId(), "Intérêt Général");


        return redirect()->route('project.index');
    }


    /**
    * Edit a task
    * @param $request Define the request data send by POST
    */
    public function storeTask(Request $request){
        $project_id = $request->input('project_id');

        $newTask = new Task;
        $newTask->name = $request->input('name');
        $newTask->duration = $request->input('duration');
        $newTask->Objective_id = $request->input('root_task');
        $newTask->type_id = $request->input('taskTypes');
        $newTask->project_id = $project_id;
        $newTask->parent_id = NULL;
        $newTask->status_id = $request->input('status');
        $transactionResult = $newTask->save(); // Indicates whether or not the save was successfull

        (new EventController())->logEvent($project_id, "Création de la tâche parent \"" . $request->input('name') . "\""); // Create an event

        // return redirect()->route("project.show", ['id'=>$project_id]);
        // return json_encode($transactionResult);
    }

    /**
    * Delete one or more users for a project
    * @param $request Define the request data send by POST
    */
    public function destroyUser(Request $request){
        $destroyUser = Memberships::where("project_id", "=", $request->id)->where("user_id", "=", $request->user)->get();
        $destroyUser->delete();
    }

    /**
    * Create a target
    * @param $request Define the request data send by POST
    * @param $id The project id
    * @return view of project
    */
    public function storeTarget(Request $request, $id){

        $target = new Target;
        $target->description = $request->input('description');
        $target->project_id = $id;
        $target->status = "Wait";
        $target->save();

        return redirect()->route("project.show", ['id'=>$id]);
    }

   /**
   * Validate a target
   * @param $id The project id
   * @return view of checklist creation
   */
    public function valideTarget(Request $request, Target $target){

        $target->update([
            'status' => "Finished"
        ]);

    }

    /**
    * Return the target view
    * @param $request Define the request data send by POST
    * @param $id The current project id
    */
    public function getTarget(Request $request, $id){
        return view('target.store', ['project' => $id]);
    }

    /**
    * Create a new checklist item
    * @param $id The project id where to add users
    * @return view of checklist creation
    */
    public function createCheckListItem($id, $checkListId){
      return view('checkList.create', ['checkListId'=>$checkListId, 'projectId' =>$id]);//view('checkList.create', ['checkListId' => $id]);
    }

    /**
    * Edit the description
    * @param $request Define the request data send by POST
    * @param $ProjectID The project id where the description will be edit
    * @return view of project
    */
    public function editDescription(Request $request, $ProjectID){
      $Project = Project::find($ProjectID);
      $Project->description = $request->input('description');
      $Project->save();

      (new EventController())->logEvent($ProjectID, "Modification de la description du projet");

      return redirect()->route("project.show", ['id'=>$ProjectID]);
    }

    /**
    * Get the task from request
    * @param $request Define the request data send by POST
    * @return view of task
    */
    public function getTask(Request $request){

        if($request->ajax())
        {
            return 'getRequest has loaded comple';
        }

        $task = Task::find($request['task']);
        return view('project/taskdetail', ['task' => $task]);

        if(Request::ajax()){
            return Response::json(Request::all());
        }
    }






}
