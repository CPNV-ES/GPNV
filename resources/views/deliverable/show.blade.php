<!--*********************** partie PRW2***********************
Created By: Fabio Marques
Description: Show the checkList "Livrables"
-->

@extends('layouts.project')

@section('projectContent')
    <div class="panel panel-default">
        <!-- Display all project informations like the members, a description and so on -->
        <div class="panel-heading">
            <h1>Livrables</h1>
        </div>
        @if(Auth::user()->projects()->find($project->id))
            <div class="form-group col-md-12 div-new-deliverable">
                <div class="col-md-2">
                    <label>Nouveau livrable :</label>
                </div>
                @include('deliverable.create')
            </div>
        @endif
        <div class="panel-body">
            <div class="deliveriesData">
                <div class="progressionCheckList">
                    <div class="barre"
                         style="background: linear-gradient(90deg, #20DE13 {{$livrables->getCompletedPercent()}}%, #efefef 0%);"></div>
                    <p>{{$livrables->getNbItemsDone()}}/{{$livrables->getNbItems()}}</p>
                </div>
                <div>
                    <!-- Display all deliverables -->
                    @if($livrables->showToDo())
                        @foreach($livrables->showToDo() as $checkListItem)
                            @include('checkList.show', array('checkListItem'=>$checkListItem, 'projectId'=>$project->id, 'fileData'=>$livrables->getLink($checkListItem->link), 'file'=>true ))
                        @endforeach
                    @endif
                </div>
                <div class="completed hidden">
                    <h3>Effectués</h3>
                    @if($livrables->showCompleted())
                        @foreach($livrables->showCompleted() as $checkListItem)
                            @include('checkList.show', array('checkListItem'=>$checkListItem, 'projectId'=>$project->id, 'fileData'=>$livrables->getLink($checkListItem->link), 'file'=>true ))
                        @endforeach
                    @endif
                </div>
            </div>
            @if($livrables->getNbItemsDone())
                <a class="btn btn-primary changeView">Voir les éléments effectués</a>
                <a class="btn btn-primary changeView hidden">Cacher les éléments effectués</a>
            @endif
        </div>
    </div>
@endsection

@section('projectCss')
    <link rel="stylesheet" href="{{ URL::asset('css/deliverable.css') }}"/>
@endsection

@push('projectScripts')
<script src="{{ URL::asset('js/deliverables.js') }}"></script>
<script src="{{ URL::asset('js/checkList.js') }}"></script>
@endpush
