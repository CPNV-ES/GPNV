<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckList;
use App\Models\Project;
use DB;

class DeliverableController extends Controller
{

    /**
     * Return the view to see deliveries
     * @param $projectID The project id
     * @return view to see deliveries
     */
    public function show($projectID){
        $project = Project::find($projectID);
        $deliveries = new CheckList('Project', $projectID, 'Livrables');
        return view('deliverable/show',['project' => $project, 'livrables'=>$deliveries]);
    }

    /**
     * Link a file or link to the selected deliverable (Note: the deliverable id is in the request parameter)
     * @param $projectID Define the actual project id
     * @param $request Define the request data send by POST
     */
    public function LinkTo(Request $request, $ProjectID){
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
     * Delete selected deliverable
     * @param $projectID Define the actual project id
     * @param $deliveryID Define the id of the 'checkList_Items' to delete
     */
    public function delete($projectID, $deliveryID){
        $delivery = DB::table('checkList_Items')->where('id', '=', $deliveryID);
        $deliveryItemTitle = $delivery->first()->title;
        $delivery->delete();
        (new EventController())->logEvent($projectID, "Suppression du livrable \"" . $deliveryItemTitle . "\"");
    }
}
