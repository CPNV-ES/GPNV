<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;


use App\Models\StudentClass;
use App\Models\User;
use App\Models\Memberships;
use App\Models\UsersTask;
use App\Models\AcknowledgedEvent;

use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{

    public function show($projectID){
        $project = Project::find($projectID);
        return view('membership/show',['project' => $project]);
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
  
        return view('membership.add', ['project' => $Project, 'users' => $users]);
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
  
        return view('membership.add', ['project' => $Project, 'users' => $users]);
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
                
                $memberShip = new Memberships();
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
        
        
        return view('membership.show', ['project' => $Project]);
        
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
  
        return route('home');
      }

}
