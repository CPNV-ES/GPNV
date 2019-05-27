<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livrable;
use DB;
use File;
use Redirect;
use App\Models\ScenarioStep;
use App\Models\Mockup;


class ScenarioStepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($projectId, $scenarioId, Request $request)
    {
        $order = ScenarioStep::where('scenario_id', $scenarioId)->max('order') + 1;

        $step = new ScenarioStep;
        $step->action = $request->action;
        $step->condition = $request->condition;
        $step->result = $request->reponse;
        $step->order = $order;
        $step->scenario_id = $scenarioId;

        $step->save();

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($projectId, $scenarioId, $itemId, Request $request) //Was Request $request in last project
    {
        DB::table('steps')->where('id', $itemId)->update(array('order'=>$request->order, 'action'=>$request->action, 'condition'=>$request->condition, 'result'=>$request->reponse));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($projectId, $stepId)
    {
        DB::table('steps')->delete($stepId);
        return redirect()->back();
    }

    public function changeMaquete($projectid, $scenarioId, Request $request){
        $step = ScenarioStep::find($request->stepId);
        $image = Mockup::find($request->mockupId);

        if(isset($step) && isset($image))
        {
            $step->mockup_id = $image->id;
            $step->save();
        }
    }
}
