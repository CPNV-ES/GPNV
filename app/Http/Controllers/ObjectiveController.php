<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\CheckList;

class ObjectiveController extends Controller
{
    /**
     * Return the view to see objectives
     * @param $projectID The project id
     * @return view to see objectives
     */
    public function show($projectID){
        $project = Project::find($projectID);
        $objectives = new CheckList('Project', $projectID, 'Objectifs');
        return view('objective/show',['project' => $project, 'objectifs'=>$objectives]);
    }
}
