<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\CheckList;
use App\Models\Scenario;
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
     * show scenario for an objective
     * @param $projectId The current project id
     * @param $itemId The checkList item id
     * @return view to see checkList item
     * @with scenarios, projectId
     */
    function showItem($projectId, $itemId){
        $item = CheckList::getItem($itemId);
        //get scenarios linked to the item
        $scenarios = Scenario::where('checkList_item_id', $item->id)->get(); //DB::table('scenarios')->where('checkList_item_id', $item->id)->get();
        $scenarios->id = $itemId;
        return view('objective.showItem')->with(compact('scenarios', 'projectId'));
    }

    /**
     * update checkListItem
     * @param $projectId The current project id
     * @param $id The checkList item id
     * @param $requete Define the request data send by POST
     */
    function update($id,$itemId, Request $requete){
        if(null !== $requete->get('validate'))
        {
            CheckList::validate($itemId, $requete->get('done'));
        }
        else
        {
            CheckList::updateItem($itemId,$requete);
        }
        return redirect()->back();
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
