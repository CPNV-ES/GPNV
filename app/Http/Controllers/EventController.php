<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Memberships;
use App\Models\AcknowledgedEvent;
use App\Models\UsersTask;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use App\Models\Event;
use App\Http\Middleware\ProjectControl;
use Illuminate\Support\Facades\Auth;
use DB;

class EventController extends Controller
{
    /**
    * Display all project events
    * @param $project The project item
    * @param $request Define the request data send by POST
    * @return json events
    */
    public function show(Project $project, Request $request){
        $currentUserId = Auth::id();

        $eventInfos = DB::table('events')
            ->join('projects', 'projects.id', '=', 'events.project_id')
            ->join('users', 'events.user_id', '=', 'users.id')
            ->select('events.id as eventId', 'users.id as userId', 'users.firstname', 'users.lastname', 'events.description', 'events.created_at')
            ->where('events.project_id', '=', $request->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $projectMembers = Project::find($request->id)->users->sortBy('id');

        $badgeCount = 0;

        // Array containing lists of users that have validated events
        $validations = array();

        foreach ($eventInfos as $eventKey => $event) {
            // Holds ids of users that have validated the event
            $users = array();
            foreach ($projectMembers as $member) {
                $exists = AcknowledgedEvent::where([
                    ['user_id', '=', $member->id],
                    ['event_id', '=', $event->eventId],
                ])->exists();

                if($exists) {
                    $users[] = $member->id;
                }
            }

            $validations[$event->eventId] = $users;

            // Incrementing badgeCount unless the current user validated the event
            if (!in_array($currentUserId, $users)) {
                $badgeCount++;
            }
        }

        return json_encode(array(
            'eventInfos'=>$eventInfos,
            'currentUser'=>['id'=>$currentUserId],
            'validations' => $validations,
            'members' => $projectMembers,
            'badgeCount' => $badgeCount
        ));
    }

    /**
    * Create an event
    * @param $projectId The project id
    * @param $description The event description
    */
    public function logEvent($projectId, $description) {
        $event = new Event;
        $event->user_id = Auth::user()->id;
        $event->project_id = $projectId;
        $event->description = $description;
        $event->save();

        // relationship management
        $relation = new AcknowledgedEvent;
        $relation->event_id = $event->id;
        $relation->user_id = Auth::user()->id;
        $relation->save();
    }

    /**
    * Create an event from a POST request
    * @param $projectId The project id
    * @param $request Define the request data send by POST
    */
    public function store($projectId, Request $request) {
        $this->logEvent($projectId, $request->description);
    }

    /**
    * Create validation
    * @param $project The project item
    * @param $request Define the request data send by POST
    * @return json validation
    */
    public function storeValidation($project, Request $request) {
        $relation = new AcknowledgedEvent;
        $relation->event_id = $request->eventId;
        $relation->user_id = $request->userId;
        $relation->save();

        return json_encode($relation);
    }

    /**
    * Return view event form
    * @param $id event id
    * @return view event form
    */
    public function formEvent($id){
        return view('events.store', ['id' => $id]);
    }
}
