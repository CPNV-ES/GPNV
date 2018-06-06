<?php
/* Created By: Fabio Marques
  Description: Model to interact with the checkList
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livrable;
use DB;
use Redirect;
use App\Models\CheckList;
use App\Models\Event;
use App\Models\AcknowledgedEvent;
use App\Models\Scenario;
use Illuminate\Support\Facades\Auth;

class CheckListController extends Controller
{

  /**
  * create new checkList item
  * @param $id The current project id
  * @param $checkListId The checkList item id
  * @param $requete Define the request data send by POST
  * @return to previous page
  */
  function store(Request $requete, $id, $checkListId){
    $newChecklistItem = CheckList::newItem($checkListId, $requete->get('name'), $requete->get('description'));
    // Getting the checklist type to display in the logs
    $checklistItem = DB::table('checkList_Items')->where('id', $newChecklistItem)->first()->checkList_id;
    $checklistType = DB::table('checkLists')->where('id', $checklistItem)->first()->checkListType_id;
    $checkList = DB::table('checkList_Types')->where('id', $checklistType)->first();
    $type = $checkList->name;

    $singularType = substr($type, 0, strlen($type) - 1);
    $formattedType = strtolower($singularType);

    // Defining the preposition that will be used in the log entry according to the checklist type
    $preposition = ($type == "Livrables") ? 'du ' : 'de l\'';

    // Logging the objective creation in the logbook
    (new EventController())->logEvent($id, "Création " . $preposition . $formattedType . " \"" . $requete->get('name') . "\"");

    return redirect()->back();
  }

  /**
  * Unlink checklist item
  * @param $checkListId The checkList item id
  */
  function unlink($checkListID){
      DB::table('checkList_Items')->where('id', $checkListID)->update(['link' => null]);
  }

}
