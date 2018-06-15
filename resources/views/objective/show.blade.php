@extends('layouts.project')

@section('projectContent')
    <div class="panel panel-default">
        <!-- Display all objectives for a project -->
        <div class="panel-heading">
            <h1>Objectifs</h1>
        </div>
        @if(Auth::user()->projects()->find($project->id))
            <div class="form-group col-md-12 div-new-objective">
                <div class="col-md-2">
                    <label>Nouvel objectif :</label>
                </div>
                @include('objective.create')
            </div>
        @endif
        <div id="objectives" class="panel-body objectives">
            <div class="objectivesData">
                <div class="progressionCheckList">
                    <div class="barre"
                         style="background: linear-gradient(90deg, #20DE13 {{$objectifs->getCompletedPercent()}}%, #efefef 0%);"></div>
                    <p>{{$objectifs->getNbItemsDone()}}/{{$objectifs->getNbItems()}}</p>
                </div>
                <div>
                    <!-- Display all yourCheckList -->
                    @if($objectifs->showToDo())
                        @foreach($objectifs->showToDo() as $checkListItem)
                            @unless($checkListItem->title == "Intérêt Général")
                                @include('objective.checklist', array('checkListItem'=>$checkListItem, 'modalBox' => true, 'projectId'=>$project->id, 'scenarios' => $objectifs->getScenariosState($checkListItem->id) ))
                            @endunless
                        @endforeach
                    @endif

                    @if($objectifs->showCompleted())
                        @foreach($objectifs->showCompleted() as $checkListItem)
                            @include('objective.checklist', array('checkListItem'=>$checkListItem, 'modalBox' => true, 'projectId'=>$project->id))
                        @endforeach
                    @endif
                </div>
            </div>
            <button class="btn btn-primary reloadobjectives pull-right">
                <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
            </button>
        </div>
    </div>
@endsection

@section('projectCss')
    <link rel="stylesheet" href="{{ URL::asset('css/objective.css') }}"/>
@endsection

@push('projectScripts')
<script src="{{ URL::asset('js/objectives.js') }}"></script>
@endpush
