<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livrable;
use DB;
use File;
use Redirect;
use App\Models\Scenario;
use App\Models\ScenarioStep;
use App\Models\Mockup;
use App\Models\Event;
use App\Models\AcknowledgedEvent;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ScenarioController extends Controller
{
  /**
  * show scenario
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @return view to see scenario
  */
  function show($projectId, $scenarioId){
    $scenario = Scenario::find($scenarioId);
    $project = Project::find($projectId);
    return view('scenario.show', ['projectId'=>$projectId, 'scenario'=>$scenario, 'mockups' => $project->mockups, 'project'=>$project] );
  }

  /**
  * update scenario
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @param $requete Define the request data send by POST
  * @return to previous page
  */
  function update($projectId, $scenarioId, Request $requete){
    $scenario = Scenario::find($scenarioId);
    $scenario->name = $requete->name;
    $scenario->description = $requete->description;
    if($requete->actif && $requete->actif=='on')
      $scenario->actif = 1;
    else
      $scenario->actif = 0;

    if($requete->test_validated && $requete->test_validated =='on')
      $scenario->test_validated = 1;
    else
      $scenario->test_validated = 0;

    $scenario->save();

    return redirect()->back();
  }

  /**
  * create new scenario item
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @param $requete Define the request data send by POST
  * @return view to see scenario
  */
  function store($projectId, $checkListId, Request $requete){
    $scenario = new Scenario();
    $scenario->name = $requete->name;
    $scenario->checkList_item_id = $checkListId;
    $scenario->save();
    //$scenarioId = Scenario::newItem($checkListId, $requete->get('name'));

    // Logging the scenario creation in the logbook
    (new EventController())->logEvent($projectId, "Création du scénario \"" . $requete->get('name') . "\"");

    return redirect()->route('scenario.show', ['projectId'=>$projectId, 'scenarioId'=>$scenario->id]);
  }

  /**
  * Delete a scenario
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @return to previous page
  */
  function delete($projectId, $scenarioId){
    $scenario = Scenario::find($scenarioId);

    // Logging the scenario removal in the logbook
    (new EventController())->logEvent($projectId, "Suppression du scénario \"$scenario->name\"");

    $scenario->delete();
    return redirect()->back();
  }

  /**
  * addNewItem form
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @return view to addNewItem form
  */
  function addItem($projectId, $checkListId){
    return view("scenario.addItem", ['projectId'=>$projectId, 'checkListId'=>$checkListId]);
  }

  /**
  * Add a step to scenario//
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @param $requete Define the request data send by POST
  * @return to previous page
  */
 // function addStep($projectId, $scenarioId, Request $requete){
 //   $order = ScenarioStep::where('scenario_id', $scenarioId)->max('order')+1;
//
 //   $step = new ScenarioStep;
 //   $step->action = $requete->action;
 //   $step->result = $requete->reponse;
 //   $step->order = $order;
 //   $step->scenario_id = $scenarioId;
//
 //   $step->save();
//
 //   return redirect()->back();
 // }

  /**
  * Delete a step to scenario
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @return to previous page
  */
  function delStep($projectId, $stepId){
    DB::table('steps')->delete($stepId);
    return redirect()->back();
  }

  /**
  * update scenarioItem
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @param $itemId The scenarioItem id
  * @param $requete Define the request data send by POST
  * @return to previous page
  */
  function updateStep($projectid, $scenarioId, $itemId, Request $requete){
    DB::table('steps')->where('id', $itemId)->update(array('order'=>$requete->order, 'action'=>$requete->action, 'result'=>$requete->reponse));
    return redirect()->back();
  }

  /**
  * Upload maquette picture
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @param $requete Define the request data send by POST
  * @return to previous page
  */
  public function uploadMaquete($projectid, $scenarioId, Request $request){
    if($request->hasFile('maquette')){
      if($request->file('maquette')->isValid()){
        $file = $request->file('maquette');
        $newName = uniqid('img').".".$file->getClientOriginalExtension();
        $path = $file->move("mockup/$projectid/", $newName);

        $project = Project::find($projectid);

        $mockup = new Mockup;
        $mockup->url = $newName;
        $mockup->project()->associate($project);
        $mockup->save();

      }
    }
    return redirect()->back();
  }

  /**
  * Edit maquette picture
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @param $requete Define the request data send by POST
  */
  public function changeMaquete($projectid, $scenarioId, Request $request){
    $step = ScenarioStep::find($request->stepId);
    $image = Mockup::find($request->mockupId);

    if(isset($step) && isset($image))
    {
      $step->mockup_id = $image->id;
      $step->save();
    }
  }

  /**
  * Delete maquette picture
  * @param $projectId The current project id
  * @param $scenarioId The current scenario id
  * @param $requete Define the request data send by POST
  */
  public function delMaquete($projectid, $scenarioId, Request $request){
    $image = Mockup::find($request->mockupId);

    if(!is_null($image)){
      $filename = "mockup/$projectid/".$image->url;

      if($image->delete())
        File::delete($filename);

    }
  }
}
