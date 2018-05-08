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

        return view('project/show', [
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
    * Return the view to see deliveries
    * @param $projectID The project id
    * @return view to see deliveries
    */
    public function showDeliveries($projectID){
      $project = Project::find($projectID);
      $deliveries = new CheckList('Project', $projectID, 'Livrables');
      return view('project/delivery',['project' => $project, 'livrables'=>$deliveries]);
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
    * Returns the html representation of all views mathing a set of filter
    * specified in the request parameter
    * @param $request Define the request data send by POST
    * @return tasks
    */
    public function getTasks(Request $request) {
        $projectId = $request->id;
        $status = $request->status;
        $taskOwner = $request->taskOwner;
        $taskObjective = $request->taskObjective;

        // Stores the task views representations that will be displayed to the user
        $viewStack = "";

        // Holds tasks matching the search criterias/filters
        $tasks = collect(new Task);

        switch ($taskOwner) {
            case 'all':
                $query = Task::join('users_tasks', 'tasks.id', '=', 'users_tasks.task_id')
                    ->select('tasks.*')
                    ->where("tasks.project_id", "=", $projectId)
                    ->when(count($status) > 0, function ($query) use ($status) {
                        return $query->whereIn("tasks.status_id", $status);
                    })
                    ->distinct()
                    ->whereNull('tasks.parent_id');
                if(isset($taskObjective) && $taskObjective!='all')
                  $query->where('tasks.Objective_id','=', $taskObjective);
                $tasks = $query->get();
                break;

            case 'nobody':
                $query = Task::doesntHave('usersTasks')
                    ->where("tasks.project_id", "=", $projectId)
                    ->when(count($status) > 0, function ($query) use ($status) {
                        return $query->whereIn("tasks.status_id", $status);
                    })
                    ->whereNull('tasks.parent_id');

                if(isset($taskObjective) && $taskObjective!='all')
                  $query->where('tasks.Objective_id','=', $taskObjective);

                $tasks = $query->get();
                break;

            default:
                $query = Task::join('users_tasks', 'tasks.id', '=', 'users_tasks.task_id')
                    ->select('tasks.*')
                    ->where('users_tasks.user_id', "=", $taskOwner)
                    ->where("tasks.project_id", "=", $projectId)
                    ->when(count($status) > 0, function ($query) use ($status) {
                        return $query->whereIn("tasks.status_id", $status);
                    })
                    ->whereNull('tasks.parent_id');

                if(isset($taskObjective) && $taskObjective!='all')
                  $query->where('tasks.Objective_id','=', $taskObjective);

                $tasks = $query->get();
                break;
        }

        // Making sure there are tasks to display / show a message otherwise

        if (count($tasks) > 0) {
            foreach ($tasks as $task) {
                $taskView = view('project/task', ['task' => $task]);
                $viewStack .= $taskView;
            }
            return $viewStack;
        } else {
            return "<p id=\"resultLess\">Aucune tâche ne correspond aux filtres de recherche.</p>";
        }
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
    * Return te view to creating tasks
    * @param $id The project id
    * @return view of task creation
    */
    public function createTask($id){
        $taskTypes = DB::table('taskTypes')->get();
        return view('task.create', ['project' => $id, 'taskTypes' => $taskTypes]);
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
    * Get list of student from class who can be added to the project
    * @param $id The project id where to add users
    * @return view of users to add
    */
    public function getStudents($id){

      // Recover users in the current porject
      $Project = Project::find($id);
      $usersInProject = $Project->users()->select('users.id')->get()->toArray();

      $usersDontNeed = [];
      foreach ($usersInProject as $user){
           array_push($usersDontNeed,$user['id']);
      }

      $UserClassID = Auth::user()->class_id;
      $UserClass = StudentClass::find($UserClassID);
      $ClassYearSection = substr($UserClass->name, -2);

      $Test = str_replace('SI-','',$UserClass->name);
      $Test = str_replace($ClassYearSection,'',$Test);

      if($Test!='T'){
        $Regex = "SI-(MI)".$ClassYearSection."|SI-[C]".$ClassYearSection;
        $Regex = '/'.$Regex.'/';

        $Classes = StudentClass::all();
        foreach ($Classes as $Classe) {
          if(preg_match($Regex, $Classe->name)){
            if($UserClassID!=$Classe->id){
              $AddClass = $Classe->id;
              break;
            }
          }
        }
      }

      // Add user from same classes if needed
      if(isset($AddClass)){
        $users = User::whereNotIn('users.id', $usersDontNeed)
          ->select('users.id', 'avatar', 'mail', 'firstname', 'lastname', 'class_id')
          ->where('class_id', '=', $UserClassID)
          ->orWhere('class_id', '=', $AddClass)
          ->join('roles', 'users.role_id', '=', 'roles.id')
          ->orderBy('lastname', 'asc')
          ->get();
      }
      // Get Only teacher user when the authentificated user is a teacher
      else{
        if(Auth::user()->role_id==2){
          $users = User::whereNotIn('users.id', $usersDontNeed)
            ->select('users.id', 'avatar', 'mail', 'firstname', 'lastname', 'class_id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('role_id', '=', 1)
            ->orderBy('lastname', 'asc')
            ->get();
        }
        else{
          $users = User::whereNotIn('users.id', $usersDontNeed)
            ->select('users.id', 'avatar', 'mail', 'firstname', 'lastname', 'class_id')
            ->where('class_id', '=', $UserClassID)
            ->where('role_id', '=', 1)
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->orderBy('lastname', 'asc')
            ->get();
        }
      }

      return view('project.addUsers', ['project' => $Project, 'users' => $users]);
    }

    /**
    * Get teacher to add and remove the one hardly in the project
    * @param $id The project id where to add users
    * @return view of teacher to add
    */
    public function getTeachers($id){
      // Recover users in the current porject
      $Project = Project::find($id);
      $usersInProject = $Project->users()->select('users.id')->get()->toArray();

      $usersDontNeed = [];
      foreach ($usersInProject as $user){
           array_push($usersDontNeed,$user['id']);
      }

      $users = User::whereNotIn('users.id', $usersDontNeed)
        ->select('users.id', 'avatar', 'mail', 'firstname', 'lastname', 'class_id')
        ->where('role_id', '=', 2)
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->orderBy('lastname', 'asc')
        ->get();

      return view('project.addUsers', ['project' => $Project, 'users' => $users]);
    }

    /**
    * Add users to project
    * @param $request Define the request data send by POST
    * @param $ProjectId The current project id
    * @return view of users in project
    */
    public function addUsers(Request $request, $ProjectID){
      if($request->input('user')) {
          foreach ($request->input('user') as $key => $value) {
              $memberShip = new Memberships;
              $memberShip->user_id = $key;
              $memberShip->project_id = $ProjectID;
              $memberShip->save();

              $member = User::find($key);
              $memberFullName = $member->getFullNameAttribute();

              // Add a new entry to the logbook
              (new EventController())->logEvent($ProjectID, "Ajout de " . $memberFullName . " au projet ");
          }
      }
      $Project = Project::find($ProjectID);
      return view('project.membership', ['project' => $Project]);
    }

    /**
    * Remove user from the project, also remove task attribution
    * @param $UserID User to remove from project
    * @param $ProjectId Define the actual project id
    * @return view of project
    */
    public function removeUserFromProject($ProjectId, $UserID=null){
      if($UserID!=null)
        $currentUser = User::find($UserID);
      else
        $currentUser = Auth::user();
      $Project = Project::find($ProjectId);
      $Memberships = Memberships::where('user_id', '=', $currentUser->id)->where('project_id', '=', $Project->id)->get()[0];

      $Tasks = $Project->tasks()->get();

      foreach ($Tasks as $Task) {
        $UserTask = UsersTask::where('user_id', '=', $currentUser->id)->where('task_id', '=', $Task->id)->get();
        if(isset($UserTask[0])){
            $UserTask[0]->delete();

            // Log the event
            (new EventController())->logEvent($ProjectId, $currentUser->getFullNameAttribute() . " a été retiré de la tâche \"" . $Task->name . "\"");
        }
      }

      $Events = $Project->events()->get();
      $EventsID = [];
      foreach ($Events as $event){
           array_push($EventsID,$event['id']);
      }

      $AcknowledgedEventsU = AcknowledgedEvent::where('user_id', '=', $currentUser->id)->whereIn('event_id', $EventsID)->get();

      foreach ($AcknowledgedEventsU as $AcknowledgedEventU) {
        $AcknowledgedEventU->delete();
      }

      $eventDescription = '';

      if($UserID!=null)
        $eventDescription = $currentUser->getFullNameAttribute() . " a été retiré du projet";
      else
        $eventDescription = "Abandon du projet";

      (new EventController())->logEvent($ProjectId, $eventDescription);

      $Memberships->delete();

      return view('project.membership', ['project' => $Project]);
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

    /**
    * Get files to link and view
    * @param $ProjectId The current project id
    * @return view of files or url to link
    */
    public function getToLink($projectID, $check){
      $Project = Project::find($projectID);

      $linkedFiles = DB::table('checkList_Items')->whereNotNull('link')->pluck('link');
      $filesInProject = $Project->files()->whereNotIn('id', $linkedFiles)->get();

      return view('project.toLink', ['project' => $Project, 'files' => $filesInProject, 'checkID'=> $check]);
    }


    /**
    * Link a file or link to the selected delivery (Note: the delivery id is in the request parameter)
    * @param $projectID Define the actual project id
    * @param $request Define the request data send by POST
    */
    public function LinkToDelivery(Request $request, $ProjectID){
      if( $request->input('check')==null || $request->input('type')==null || $request->input('data')==null) return redirect('project/' . $ProjectID);

      $checkListID = $request->input('check');
      $checkListItem = DB::table('checkList_Items')->where('id', $checkListID)->first();
      if( $checkListItem==null ) return redirect('project/' . $ProjectID);

      if($request->input('type')=="file"){
        $file = DB::table('file')->where('id','=',$data)->first();
        if( $file==null ) return redirect('project/' . $ProjectID);
      }

      DB::table('checkList_Items')->where('id', $checkListID)->update(['link' => $request->input('data')]);

      return redirect()->route("project.show", ['id'=>$ProjectID]);

    }



    /**
    * Delete selected delivery
    * @param $projectID Define the actual project id
    * @param $deliveryID Define the id of the 'checkList_Items' to delete
    */
    public function deleteDelivery($projectID, $deliveryID){
      $delivery = DB::table('checkList_Items')->where('id', '=', $deliveryID);
      $deliveryItemTitle = $delivery->first()->title;
      $delivery->delete();
      (new EventController())->logEvent($projectID, "Suppression du livrable \"" . $deliveryItemTitle . "\"");
    }

}
