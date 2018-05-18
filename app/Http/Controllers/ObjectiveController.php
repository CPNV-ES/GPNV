<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\CheckList;
use DB;


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

    /**
     * Delete selected objective (also delete scenarios and scenario tests related to it)
     * @param $projectID Define the actual project id
     * @param $objectiveID Define the id of the 'checkList_Items' to delete
     */
    public function delete($projectID, $objectiveID){
        $project = Project::find($projectID);
        $scenarios = DB::table('scenarios')->where('checkList_Item_id', $objectiveID)->get();

        DB::table('scenarios')->where('checkList_Item_id', '=', $objectiveID)->delete();

        $objective = DB::table('checkList_Items')->where('id', '=', $objectiveID);
        $objectiveName = $objective->first()->title;
        $objective->delete();

        // Log the objective removal
        (new EventController())->logEvent($projectID, "Suppression de l'objectif \"" . $objectiveName . "\"");


        // Counting scenarios before logging anything in relation
        $scenarioSummary = 'Suppression du/des scenario(s): ';

        if (count($scenarios) > 0) {
            foreach ($scenarios as $scenario) {
                $scenarioSummary.= $scenario->name . ", ";
            }

            $finalSummary = substr($scenarioSummary, 0, -2);

            // Log the scenarios removal
            (new EventController())->logEvent($projectID, $finalSummary);
        }
    }
}
