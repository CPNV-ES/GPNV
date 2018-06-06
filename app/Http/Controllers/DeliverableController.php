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
     * @return view to see deliverables
     */
    public function show($projectID){
        $project = Project::find($projectID);
        $deliveries = new CheckList('Project', $projectID, 'Livrables');

        $linkedFiles = DB::table('checkList_Items')->whereNotNull('link')->pluck('link');
        $filesInProject = $project->files()->whereNotIn('id', $linkedFiles)->get();

        return view('deliverable/show',['project' => $project, 'livrables'=>$deliveries,'files' => $filesInProject]);
    }

    /**
     * Link a file or link to the selected deliverable (Note: the deliverable id is in the request parameter)
     * @param $projectID Define the actual project id
     * @param $request Define the request data send by POST
     * @return view to see deliverables
     */
    public function LinkTo(Request $request, $ProjectID, $checkListID){
        if( $request->input('type')==null || $request->input('data')==null) return redirect('project/' . $ProjectID . '/deliverables');

        $checkListItem = DB::table('checkList_Items')->where('id', $checkListID)->first();

        if( $checkListItem==null ) return redirect('project/' . $ProjectID . '/deliverables');

        if($request->input('type')=="file"){
            $file = DB::table('files')->where('id','=',$request->input('data'))->first();
            if( $file==null ) return redirect('project/' . $ProjectID . '/deliverables');
        }

        DB::table('checkList_Items')->where('id', $checkListID)->update(['link' => $request->input('data')]);

        return redirect()->route("deliverable.show", ['id'=>$ProjectID]);
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
